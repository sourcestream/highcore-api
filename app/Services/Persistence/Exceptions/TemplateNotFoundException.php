<?php namespace Highcore\Services\Persistence\Exceptions;

class TemplateNotFoundException extends NotFoundException{
    protected $message = "Template Not Found";
}
