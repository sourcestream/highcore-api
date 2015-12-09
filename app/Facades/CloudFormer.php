<?php namespace Highcore\Facades;

use Highcore\Models\Collection;
use Highcore\Models\Stack;
use Highcore\Template;
use Illuminate\Support\Facades\Facade;

/**
 * Class CloudFormer
 * @package Highcore\Facades
 *
 * @method static Stack createStack(Stack $stack) Create a new Cloud stack
 * @method static Stack updateStack(Stack $stack) Update existing Cloud stack
 * @method static Stack describeStack(Stack $stack) Describe specified Cloud stack
 * @method static Stack deleteStack(Stack $stack) Delete specified Cloud stack
 * @method static string createTemplate(Stack $stack) Generate Template for Cloud Stack
 * @method static string diffTemplate(Stack $stack) Compare new Template against actual one
 * @method static Collection getTemplateParams(Template $template) Get a collection of parameters defined in the template
 * @method static Collection getTemplateComponents(Template $template) Get a collection of components defined in the template
 */
class CloudFormer extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'cloud_former';
    }
}
