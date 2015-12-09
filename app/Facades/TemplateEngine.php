<?php namespace Highcore\Facades;

use Highcore\Models\Template;
use Illuminate\Support\Facades\Facade;

/**
 * Class TemplateEngine
 * @package Highcore\Facades
 *
 * @method static array getParams(Template $template) Get a nested array of parameters defined in the template
 * @method static array getComponents(Template $template) Get array of components defined in the template
 * @method static array getTemplate(Template $template, $parameters, $update) Get CloudFormation Template
 * @method static array getInputDefinition(Template $template) Get array of components defined in the template
 * @method static void updateTemplate(Template $template) Ensure project template is available and is at the right $refspec
 * @method static void cleanupTemplate(Template $template) Remove local files for the template
 */
class TemplateEngine extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'template_engine';
    }
}
