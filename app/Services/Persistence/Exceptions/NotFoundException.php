<?php namespace Highcore\Services\Persistence\Exceptions;

class NotFoundException extends \Exception{
    protected $code = 404;
}
