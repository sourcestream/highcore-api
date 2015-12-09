<?php namespace Highcore\Facades;

use Highcore\Models\Collection;
use Highcore\Models\Stack;
use Highcore\Models\Environment;
use Highcore\Models\Project;
use Highcore\Models\Template;
use Illuminate\Support\Facades\Facade;

/**
 * Class Persistence
 * @package Highcore\Facades
 *
 * @method Environment getEnvironment(mixed $parameters, string $key) Get a single Environment model matching parameters
 * @method Collection getEnvironments(mixed $parameters, string $key) Get Environment models matching parameters
 * @method Environment saveEnvironment(Environment $environment) Save Environment model
 * @method void deleteEnvironment(Environment $environment) Delete Environment
 *
 * @method Project getProject(mixed $parameters, string $key) Get a single Project model matching parameters
 * @method Collection getProjects(mixed $parameters, string $key) Get Project models matching parameters
 * @method Project saveProject(Project $project) Save Project model
 * @method void deleteProject(Project $project) Delete Project
 *
 * @method Stack getStack(mixed $parameters, string $key) Get a single Stack model matching parameters
 * @method Collection getStacks(mixed $parameters, string $key) Get Stack models matching parameters
 * @method Stack saveStack(Stack $stack) Save Stack model
 * @method void deleteStack(Stack $stack) Delete Stack
 *
 * @method Template getTemplate(mixed $parameters, string $key) Get a single Template model matching parameters
 * @method Collection getTemplates(mixed $parameters, string $key) Get Template models matching parameters
 * @method Template saveTemplate(Template $stack) Save Template model
 * @method void deleteTemplate(Template $stack) Delete Template
 */
class Persistence extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'persistence';
    }
}
