<?php namespace Highcore\Services\CloudFormer;

use File;
use Highcore\Models\Template;
use Highcore\Util\GitElephant\AuthenticationProvider;
use Highcore\Util\GitElephant\Command\ResetCommand;
use Highcore\Util\GitElephant\Repository;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use Exception;
use Highcore\Contracts\TemplateEngine;
use Symfony\Component\Yaml\Yaml;
use \Log;

class Sparkle implements TemplateEngine {

    const REMOTE_HIGHCORE = 'highcore';
    const APP_SSH_PRIVKEY = 'app/id_rsa';

    protected $authProvider;

    protected function ensureKeyExists($storage_path)
    {
        if(!file_exists($storage_path)){
            $cmd = sprintf('ssh-keygen -b 2048 -t rsa -f %s -q -N ""', escapeshellarg($storage_path));
            $process = new Process($cmd);
            $process->setTimeout(5000);
            $process->run();
            if ($process->getExitCode() !== 0) {
                $text = 'Exit code: ' . $process->getExitCode();
                $text .= ' while executing: "' . $cmd;
                $text .= '" with reason: ' . $process->getErrorOutput();
                $text .= "\n" . $process->getOutput();
                throw new \RuntimeException($text);
            }
        }
        return $storage_path;
    }

    public function updateTemplate(Template $template)
    {
        $repoPath = $this->getTemplatePath($template);
        if(!is_dir($repoPath) && !mkdir($repoPath, 0755, true)) {
            throw new \RuntimeException('Cannot create template repo directory '.$repoPath);
        }

        $repo = Repository::openAuthenticated($repoPath, $this->getAuthProvider());

        $repo->isInitialized() or $repo->init();
        $repo->setRemote(self::REMOTE_HIGHCORE, $template->repository);
        $repo->fetch(self::REMOTE_HIGHCORE);
        $repo->reset(sprintf('%s/%s', self::REMOTE_HIGHCORE, $template->refspec), ResetCommand::RESET_HARD, true);
        $repo->updateSubmodule(true, true, true);

        return $repo->getCommit();
    }

    /**
     * @inheritdoc
     * @var string $stack_definition Json-encoded set of stack parameters
     */
    public function getTemplate(Template $template, $stack_definition, $update = true)
    {
        $update && $this->updateTemplate($template);

        $parameters = (new ParametersCollection([
            'stack_definition'  => $stack_definition,
            'template'          => $template->name,
        ]))->toShell();

        $input = new ArrayInput($parameters, $this->getInputDefinition($template));
        $command = new Process("./template.sh $input", $this->getTemplatePath($template), $this->getTemplateScriptEnv());

        Log::debug(sprintf('Calling sparke for template %s in project %s: %s',
            $template->name, $template->project->name, $command->getCommandLine()));

        //$exitCode = 0; //$command->run();
        $exitCode = $command->run();
        if ($exitCode) {
            throw new Exception($command->getErrorOutput());
        }
        //$output = ''; //$command->getOutput();
        $output = $command->getOutput();
        return $output;
    }

    /**
     * @inheritdoc
     */
    public function getInputDefinition(Template $template) {
        $input_options = [
            new InputOption('stack-definition', null, InputOption::VALUE_REQUIRED),
            new InputOption('template', null, InputOption::VALUE_REQUIRED),
        ];
        return new InputDefinition($input_options);
    }

    /**
     * @inheritdoc
     */
    public function getParams(Template $template) {
        $stack = $this->getDefinition($template);
        $stack_parameters = array_get($stack, 'parameters', []);
        $components_parameters = [];
        $components = array_get($stack, 'components');
        foreach ($components as $component) {
            $component_id = $component['id'];
            $parameters = array_get($component, 'parameters', []);
            $parameters_flat = array_map(function($parameter) use($component_id) {
                $parameter['id'] = "${component_id}_${parameter['id']}";
                return $parameter;
            }, $parameters);
            $components_parameters = array_merge($components_parameters, $parameters_flat, $parameters);
        }
        $parameters = array_merge($stack_parameters, $components_parameters);
        return $parameters;
    }

    /**
     * @inheritdoc
     */
    public function getComponents(Template $template) {
        return array_get($this->getDefinition($template), 'components');
    }

    private function getTemplatePath(Template $template) {
        return storage_path("app/projects/{$template->id}-{$template->name}");
    }

    /**
     * @inheritdoc
     */
    private function getDefinition(Template $template) {
        $template_contents = File::get($this->getTemplatePath($template)."/templates/{$template->name}.yml");
        return Yaml::parse($template_contents);
    }

    /**
     * Deletes local template files
     *
     * @param Template $template
     * @return void
     */
    public function cleanupTemplate(Template $template)
    {
        $dir = $this->getTemplatePath($template);

        if(!File::deleteDirectory($dir)){
            throw new \RuntimeException("Could not delete ".$dir);
        }
    }

    private function getTemplateScriptEnv()
    {
        return $this->getAuthProvider()->getEnv() + ['PATH' => '/usr/local/bin:/usr/bin'];
    }

    /**
     * @return AuthenticationProvider
     */
    private function getAuthProvider()
    {
        if($this->authProvider === null){
            $this->authProvider = new AuthenticationProvider($this->ensureKeyExists(storage_path(self::APP_SSH_PRIVKEY)));
        }
        return $this->authProvider;
    }
}