<?php namespace Highcore\Services\Persistence\Exceptions;

class StackNotFoundException extends NotFoundException{
    protected $message = "Stack Not Found";
}
