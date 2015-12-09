<?php namespace Highcore\Contracts;

use Highcore\Stack;

interface Provisioner {

    /**
     * Register stack in provisioner
     *
     * @param  Stack  $stack
     * @return void
     */
    public function registerStack(Stack $stack);

}
