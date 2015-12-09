<?php namespace Highcore\Facades;

use Highcore\Stack;
use Illuminate\Support\Facades\Facade;

/**
 * Class CloudFormer
 * @package Highcore\Facades
 *
 * @method static Stack registerStack(Stack $stack) Register stack in provisioner
 */
class Provisioner extends Facade {

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'provisioner';
    }
}
