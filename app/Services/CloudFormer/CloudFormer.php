<?php namespace Highcore\Services\CloudFormer;

use Crypt;
use Log;
use SebastianBergmann\Diff\Differ;
use TemplateEngine;
use Cache;
use Aws\CloudFormation\CloudFormationClient;
use Aws\CloudFormation\Exception\CloudFormationException;
use Aws\S3\S3Client;
use Highcore\Contracts\CloudFormer as CloudFormerContract;
use Highcore\Exceptions\CloudFormerException;
use Highcore\Models\ComponentCollection;
use Highcore\Models\LinkedCollection;
use Ramsey\Uuid\Uuid;
use Highcore\Models\Collection;
use Highcore\Models\Parameter;
use Highcore\Models\Stack;
use Highcore\Models\Template;

class CloudFormer implements CloudFormerContract {
    const CACHE_LIFETIME = 0.2; // 60 * X must not be floating, otherwise fatal

    /**
     * @inheritdoc
     */
    public function createStack(Stack $stack){
        $this->manageStack($stack, 'create');

        return $stack;
    }

    /**
     * @inheritdoc
     */
    public function createTemplate(Stack $stack){
        $stack_params = $this->getParams($stack, $with_outputs = true);
        $template = $this->generateTemplate($stack, $stack_params);
        return $template;
    }

    /**
     * @inheritdoc
     */
    public function updateStack(Stack $stack) {
        $this->manageStack($stack, 'update');

        return $stack;
    }

    /**
     * @inheritdoc
     */
    public function diffTemplate(Stack $stack) {
        $actual_template = $stack->provisioned
            ? $this->cfn($stack)
                ->getTemplate(['StackName' => $stack->get('name')])
                ->get('TemplateBody')
            : '{}';
        $new_template = $this->createTemplate($stack);
        $arr_diff = new Differ();
        $diff = $arr_diff->diff(json_encode(json_decode($actual_template, true), JSON_PRETTY_PRINT),
            json_encode(json_decode($new_template, true), JSON_PRETTY_PRINT));

        return $diff;
    }

    /**
     * @inheritdoc
     */
    public function deleteStack(Stack $stack) {
        $this->manageStack($stack, 'delete');

        return $stack;
    }

    /**
     * @inheritdoc
     */
    public function describeStack(Stack $stack) {
        $this->manageStack($stack, 'describe');

        return $stack;
    }

    /**
     * Get merged parameters for the stack
     *
     * @param  Stack $stack
     * @param  bool $with_related Retrieve outputs of related stacks
     * @return Collection
     */
    private function getParams(Stack $stack, $with_related = false)
    {
        $parameters_sources = [
            $stack->get('environment.project.'),
            $stack->get('environment.'),
            $stack->get(),
        ];

        /** @var Collection $components_parameters */
        $components_parameters = $stack->get('components')->pluck()->get();
        foreach ($components_parameters as $component => $component_data) {
            if (($parameters = array_get($component_data, 'parameters')) instanceof Collection) {
                $parameters_flat = $parameters->replaceKeys(function($key) use ($component) {return "${component}_${key}";});
                $parameters_sources[] = ['parameters' => $parameters_flat];
            }
        }
        $parameters = array_pluck($parameters_sources, 'parameters');

        if ($with_related && $related_stacks = $stack->get('stacks')) {
            $parameters = array_merge(
                [$this->getOutputs($stack, $related_stacks)],
                $parameters
            );
        }

        $parameters = array_filter($parameters);
        $parameters = array_reduce($parameters, function($result, Collection $item) {
            if($result === null){ return $item; }
            /** @var $result Collection */
            return $result->mergeRecursive($item);
        });
        $parameters['components'] = $stack->get('components');
        return $parameters;
    }

    /**
     * @param Stack $stack
     * @param string $bucket
     * @param string $template
     * @return string TemplateURL
     */
    private function publishTemplate(Stack $stack, $bucket, $template) {
        $key = sprintf("%s-%s", $stack->get('name'), Uuid::uuid4());
        $response = $this->s3($stack)->upload($bucket, $key, $template);
        $url = $response->get('ObjectURL');
        return compact('key', 'url');
    }

    /**
     * @param Stack $stack
     * @param Collection $stack_params
     * @return string Generated template
     */
    private function generateTemplate(Stack $stack, Collection $stack_params)
    {
        $stack_definition = ParametersCollection::make($stack_params)
            ->enabled()
            ->maskSensitive()
            ->noMetadata()
            ->defined()
            ->toJson();

        $template = TemplateEngine::getTemplate($stack->template, $stack_definition);

        $bucket = $stack_params->get(Stack::PARAM_TEMPLATE_BUCKET)->value;
        $s3_template = $this->publishTemplate($stack, $bucket, $template);
        $cfn_params = new ParametersCollection();
        $cfn_params->put('TemplateURL', $s3_template['url']);
        $this->cfn($stack)->validateTemplate($cfn_params->toArray());
        $this->destroyTemplate($stack, $bucket, $s3_template['key']);
        return $template;
    }

    /**
     * @param Stack $stack
     * @param string $bucket S3 Bucket containing the object
     * @param string $key Object key
     * @return void
     */
    private function destroyTemplate(Stack $stack, $bucket, $key) {
        $this->s3($stack)->deleteMatchingObjects($bucket, $key);
    }

    /**
     * @param Stack $stack
     * @param $action
     * @return Stack
     * @throws \Aws\CloudFormation\Exception\CloudFormationException
     * @throws \Exception
     */
    private function manageStack(Stack $stack, $action) {
        $cfn_params = new ParametersCollection([
            'StackName' => $stack->get('name'),
//            TODO make it optional
            'DisableRollback' => true,
            'Capabilities' => ['CAPABILITY_IAM'],
        ]);

        switch ($action) {
            case 'create':
            case 'update':
                $stack_params = $this->getParams($stack, $with_outputs = true);
                $stack_params->forget('cloud_credentials');
                $template = $this->generateTemplate($stack, $stack_params);
                $bucket = $stack_params->get(Stack::PARAM_TEMPLATE_BUCKET)->value;
                $s3_template = $this->publishTemplate($stack, $bucket, $template);
                $cfn_params->put('TemplateURL', $s3_template['url']);

                $params = $cfn_params->merge($stack_params);
                $params->forget('components');

                $defined_params = TemplateEngine::getParams($stack->template);
                $defined_params_keys = ParametersCollection::make($defined_params)->parametersKeys();

                $sensitive = $params
                        ->onlyIds($defined_params_keys)
                        ->sensitive()
                        ->flat()
                        ->studly()
                        ->decrypted()
                        ->toCollection();
                if ($sensitive_aws = $sensitive->toAws()) {
                    $cfn_params->put('Parameters', $sensitive_aws);
                }

//                TODO protect credentials from logging / output
                try {
                    $action == 'create'
                        ? $this->cfn($stack)->createStack($cfn_params->toArray())
                        : $this->cfn($stack)->updateStack($cfn_params->toArray());
                } catch (CloudFormationException $e) {
                    if ($e->getMessage() != 'No updates are to be performed.') {
                        throw $e;
                    }
                } finally {
                    $this->destroyTemplate($stack, $bucket, $s3_template['key']);
                }
                break;
            case 'describe':
                Log::debug(__METHOD__);

                if ($stack->provisioned) {
                    $components = ComponentCollection::make($stack->components->all());

                    $cache_key = 'CloudFormer-DescribeStack-' . $stack->name;
                    if (($cf_response = Cache::get($cache_key)) === null) {
                        Log::debug('No cache for ' . $cache_key);
                        $cf_response = $this->cfn($stack)->describeStacks($cfn_params->toArray());
                        Cache::add($cache_key, $cf_response, self::CACHE_LIFETIME);
                    }
                    $cf_response = head($cf_response->get('Stacks'));
                    $stack->status = $this->translateAwsStatus($cf_response['StackStatus']);
                    $stack->outputs = Collection::make([]);

                    $distributedOutputs = $components->distributeResources(data_get($cf_response, 'Outputs', []), 'OutputKey');
                    $distributedOutputs->map(function(Collection $componentOutputs, $componentName) use($components, $stack) {
                        if(in_array($componentName, ['_stack','_ungrouped'])){
                            $stack->outputs = $stack->outputs->merge($componentOutputs);
                        } else {
                            $components[$componentName]->outputs = $componentOutputs;
                        }
                    });


                    $cache_key = 'CloudFormer-ListStackResources-' . $stack->name;
                    if (($cf_stack_resources = Cache::get($cache_key)) === null) {
                        Log::debug('No cache for ' . $cache_key);
                        $cf_stack_resources = $this->cfn($stack)->listStackResources($cfn_params->toArray())['StackResourceSummaries'];
                        Cache::add($cache_key, $cf_stack_resources, self::CACHE_LIFETIME);
                    }

                    $components->distributeResources($cf_stack_resources)->map(function(Collection $resources, $componentName) use($components, $stack) {

                        $status = $resources->reduce(function ($carry, $item) {
                            $itemStatus = $this->translateAwsStatus($item['ResourceStatus']);
                            if ($carry === null) {
                                return $itemStatus;
                            }

                            return ($this->weighStatus($itemStatus) > $this->weighStatus($carry) ? $itemStatus : $carry);
                        });
                        if(!in_array($componentName, ['_stack','_ungrouped']) && is_object($status)){
                            $components[$componentName]->status = $status;
                        }
                    });
                }
                break;
            case 'delete':
                $this->cfn($stack)->deleteStack($cfn_params->toArray());
                break;
        }

        return $stack;
    }

    public function getTemplateParams(Template $template, $key_by = null){
        return Collection::make(
            TemplateEngine::getParams($template),
            'parameter', $key_by
        );
    }

    public function getTemplateComponents(Template $template){
        return Collection::make(
            TemplateEngine::getComponents($template),
            'component');
    }

    /**
     * @param Stack $stack
     * @return CloudFormationClient
     */
    private function cfn(Stack $stack){
        return CloudFormationClient::factory($this->getAwsConfig($stack));
    }

    /**
     * @param Stack $stack
     * @return S3Client
     */
    private function s3(Stack $stack){
        $config = array_except($this->getAwsConfig($stack), 'region');
        return S3Client::factory($config);
    }

    /**
     * @param Stack $stack
     * @param Collection $remote_stacks
     * @return Collection
     * @throws CloudFormerException
     */
    private function getOutputs(Stack $stack, Collection $remote_stacks = null) {
        $remote_stacks = $remote_stacks ? $remote_stacks : Collection::make([$stack]);
        $params = Collection::make();
        foreach ($remote_stacks as $remote_stack) {
            if(!$remote_stack->provisioned){
                throw new CloudFormerException('Cannot use non-provisioned remote stack '.$remote_stack->name);
            }
            $cfn_params = new ParametersCollection(['StackName' => $remote_stack->name]);
            $response = $this->cfn($stack)->describeStacks($cfn_params->all());
            foreach (array_get(head($response->get('Stacks')), 'Outputs', []) as $output) {
                $key = snake_case($output['OutputKey']);
                $value = $output['OutputValue'];
                if (starts_with($value, ['[']) && ends_with($value, [']'])) {
                    $value = explode(',', trim($value, '[]'));
                }
                $param = [
                    'id' => $key,
                    'value' => $value,
                ];
                $params->put($key, Parameter::make($param));
            }
        }
        return $params;
    }

    /**
     * @param Stack $stack
     * @param string|null $nextToken
     * @return \Highcore\Models\LinkedCollection
     */
    public function getEvents(Stack $stack, $nextToken = null){

        $cache_key = 'CloudFormer-DescribeStackEvents-' . $stack->name.'-'.$nextToken;
        if (($cf_events = Cache::get($cache_key)) === null)  {
            Log::debug('No cache for ' . $cache_key);
            $cf_events = $this->cfn($stack)->describeStackEvents(['StackName' => $stack->name, 'NextToken'=>$nextToken]);
            Cache::add($cache_key, $cf_events, self::CACHE_LIFETIME);
        }

        $eventsByComponent = ComponentCollection::make($stack->components->all())
            ->distributeResources($cf_events['StackEvents']);

        $eventsByComponent = $eventsByComponent->map(function(Collection $events, $componentName) use ($stack){
            $events = $events->map(function($event) use($componentName, $stack) {
                $event = $this->translateAwsEvent($event, $componentName, $stack);
                return $event;
            });
            return $events;
        });

        $events = $eventsByComponent->collapse();

        $componentEventCollection = LinkedCollection::make($events);
        $componentEventCollection->nextToken = $cf_events['NextToken'];

        return $componentEventCollection;
    }

    private function getAwsConfig(Stack $stack){
        $params = $this->getParams($stack);
        $key = $params->get(Stack::PARAM_CLOUD_KEY)->value;
        $secret = Crypt::decrypt($params->get(Stack::PARAM_CLOUD_SECRET)->value);
        $region = $params->get(Stack::PARAM_CLOUD_REGION)->value;
        if($key === null || $secret === null){
            throw new \RuntimeException('No usable credentials for stack '.$stack->name);
        }
        return [
            'key'         => $key,
            'secret'      => $secret,
            'region'      => $region,
            'config_file' => null,
        ];
    }

    /**
     * @param string $status aws status
     * @return object
     */
    private function translateAwsStatus($status)
    {
        return (object) array_combine(['operation', 'state'], explode('_', $status, 2));
    }

    protected function weighStatus($status){
        return
            ($status->operation == 'DELETE' ? 30 : $status->operation == 'CREATE' ? 20 : 10) + // DELETE > CREATE > UPDATE
            ($status->state == 'IN_PROGRESS' ? 3 : $status->state == 'FAILED' ? 2 : 1);      // IN_PROGRESS > FAILED > COMPLETE
    }


    /**
     * Triggers provisioning on applicable stack components
     *
     * @param Stack $stack
     * @return bool
     * @throws CloudFormerException
     */
    public function provisionStack(Stack $stack)
    {
        if(!($sqs_url = $stack->get('provisioning_url'))){
            throw new CloudFormerException('Stack does not have a provisioning_url set');
        }

        //TODO: Send SQS message for stack's deployable components
    }

    private function translateAwsEvent(array $awsEvent, $componentName, Stack $stack)
    {
        $status = $this->translateAwsStatus($awsEvent['ResourceStatus']);
        $status->reason = data_get($awsEvent,'ResourceStatusReason');

        $event = [
            'timestamp' => $awsEvent['Timestamp'],
            'event_id' => $awsEvent['EventId'],
            'logical_resource_id' => $awsEvent['LogicalResourceId'],
            'component_name' => !in_array($componentName, ['_stack', '_ungrouped']) ? $componentName : null,
            'stack_name' => $stack->name,
            'environment_name' => $stack->environment->name,
            'project_name' => $stack->environment->project->name,
            'status' => $status
        ];

        return $event;
    }
}
