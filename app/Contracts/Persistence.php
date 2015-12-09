<?php namespace Highcore\Contracts;

use Highcore\Models\Project;
use Highcore\Models\Environment;
use Highcore\Models\Stack;
use Highcore\Models\Template;

interface Persistence {

    public function getEnvironments($parameters = [], $key = 'id');
    public function getEnvironment($parameters = [], $key = 'id');
    public function saveEnvironment(Environment $environment);
    public function deleteEnvironment(Environment $environment);

    public function getProjects($parameters = [], $key = 'id');
    public function getProject($parameters = [], $key = 'id');
    public function saveProject(Project $project);
    public function deleteProject(Project $project);

    public function getStack($parameters = [], $key = 'id');
    public function getStacks($parameters = [], $key = 'id');
    public function saveStack(Stack $stack);
    public function deleteStack(Stack $stack);

    public function getTemplate($parameters = [], $key = 'id');
    public function getTemplates($parameters = [], $key = 'id');
    public function saveTemplate(Template $stack);
    public function deleteTemplate(Template $stack);

}
