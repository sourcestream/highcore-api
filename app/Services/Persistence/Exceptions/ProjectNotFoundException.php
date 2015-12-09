<?php namespace Highcore\Services\Persistence\Exceptions;

class ProjectNotFoundException extends NotFoundException{
    protected $message = "Project Not Found";
}
