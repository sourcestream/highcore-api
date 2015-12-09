<?php namespace Highcore\Services\Persistence\Exceptions;

class EnvironmentNotFoundException extends NotFoundException{
    protected $message = "Environment Not Found";
}
