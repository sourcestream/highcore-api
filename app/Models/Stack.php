<?php namespace Highcore\Models;

/**
 * Class Stack
 * @package Highcore\Models
 *
 * @SWG\Definition(definition="Stack",
 *     @SWG\Property(property="id", type="integer"),
 *     @SWG\Property(property="name", type="string"),
 *     @SWG\Property(property="environment_id", type="integer"),
 *     @SWG\Property(property="template_id", type="integer"),
 *     @SWG\Property(property="components", type="array",
 *         @SWG\Items(ref="#/definitions/Component")
 *     ),
 *     @SWG\Property(property="stacks", description="referenced Stacks", type="array",
 *         @SWG\Items(ref="#/definitions/StackRef")
 *     ),
 *     @SWG\Property(property="parameters", description="parameter values for this stack", type="array",
 *         @SWG\Items(ref="#/definitions/Parameter")
 *     ),
 *     @SWG\Property(property="outputs", description="output from the cloud", type="array",
 *         @SWG\Items(ref="#/definitions/AwsOutput")
 *     ),
 *     @SWG\Property(property="provisioned", type="boolean", description="Was the stack deployed to the cloud or not"),
 *     @SWG\Property(property="status", ref="#/definitions/AwsOutput")
 * )
 *
 * @property string $name
 * @property int $id
 * @property int $environment_id
 * @property int $template_id
 * @property Template $template
 * @property Collection $components
 * @property Collection $stacks
 * @property Collection $parameters
 * @property Collection $outputs
 * @property Environment $environment
 * @property bool $provisioned
 * @property \Aws\CloudFormation\Enum\StackStatus|string $status
 *
 */

class Stack extends Model {
    //these are mandatory stack parameters (used by CloudFormer)
    const PARAM_TEMPLATE_BUCKET = 'template_bucket';
    const PARAM_CLOUD_REGION = 'cloud_region';
    const PARAM_CLOUD_KEY = 'cloud_key';
    const PARAM_CLOUD_SECRET = 'cloud_secret';

    public function __construct($data) {
        $stack_data = $data;
        parent::__construct($stack_data);
    }
}
