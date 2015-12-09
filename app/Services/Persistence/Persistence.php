<?php namespace Highcore\Services\Persistence;

use Highcore\Contracts\Persistence as PersistenceContract;
use Highcore\Models\Collection;
use Highcore\Models\Model;
use Highcore\Models\Project;
use Highcore\Models\Environment;
use Highcore\Models\Stack;
use Highcore\Models\Template;
use Highcore\Services\Persistence\Exceptions\EnvironmentNotFoundException;
use Highcore\Services\Persistence\Exceptions\ProjectNotFoundException;
use Highcore\Services\Persistence\Exceptions\StackNotFoundException;
use Highcore\Services\Persistence\Exceptions\TemplateNotFoundException;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as EloquentModel;

class Persistence implements PersistenceContract {

    protected static function getQueryClass($class_name) {
        $class_name = ($pos = strrpos($class_name, '\\')) ? substr($class_name, $pos + 1) : $class_name;
        return $query_class = "\\Highcore\\$class_name";
    }

    protected static function where( $query, $key, $value) {
        $c = is_array($value) ? $value : compact('value');
        $operator = array_get($c, 'operator', '=');
        if (in_array($operator, ['In', 'NotIn'])) {
            return $query->whereIn($key,
                array_get($c, 'values'),
                array_get($c, 'boolean', 'and'),
                $operator == 'NotIn' ?: false
            );
        } else {
            return $query->where($key,
                array_get($c, 'operator', '='),
                array_get($c, 'value', null),
                array_get($c, 'boolean', 'and')
            );
        }
    }

    /**
     * @param string $class_slug Model class name
     * @param mixed|array $parameters Array of parameters to use in where clause
     * @param array $relations Array of relations with parameters as keys.
     *                         Each relation provides key and relation path for QueryBuilder
     * @param string $key Field to be used as a key
     * @return Collection
     */
    protected function getModels($class_slug, $parameters = [], $relations, $key) {
        if (!is_array($parameters)) {
            $parameters = ['key' => $parameters];
        }
        $relations_parameters = array_intersect_key($relations, $parameters);
        $model_parameters = array_except($parameters, array_keys($relations_parameters));

        $orm_class = self::getQueryClass($class_slug);
        /** @var Builder $query */
        $query = $orm_class::query();
        foreach ($model_parameters as $parameter => $value) {
            if ($parameter == 'key') {$parameter = $key;}
            self::where($query, $parameter, $value);
        }
        $query->with(array_map(function($r) {return $r['relation'];}, array_values($relations)));
        foreach ($relations_parameters as $parameter => $relation_data) {
            $value = $parameters[$parameter];
            $query->with([$relation_data['relation'] => function($query) use ($key, $value) {
                self::where($query, $key, $value);
            }]);
            $query->whereHas($relation_data['relation'], function($query) use ($key, $value) {
                self::where($query, $key, $value);
            });
        }
        $orm_models = $query->get();
        $models = Collection::make();
        $class = Model::getModelClass($class_slug);

        foreach ($orm_models as $orm_model) {
            $models->push($class::make($orm_model));
        }

        return $models;
    }

    protected function saveModel(Model $model) {
        $orm_class = self::getQueryClass(get_class($model));
        $id = $model->get('id', false);
        /** @var EloquentModel $record */
        $record = $id ? $orm_class::find($id)->fill($model->toArray()) : new $orm_class($model->toArray());
        $record->push();
        if (!$id) {$model->id = $record->id;};
        return $model;
    }

    protected function deleteModel(Model $model) {
        $orm_class = self::getQueryClass(get_class($model));
        $orm_class::destroy($model->id);
    }

    /**
     * @inheritdoc
     */
    public function getStacks($parameters = [], $key = 'id') {
        if (!in_array($key, ['id', 'name'])) {abort(500, 'Invalid persistence key');}
        $relations = [
            'environment_key' => ['key' => $key, 'relation' => 'environment'],
            'project_key' => ['key' => "projects.$key", 'relation' => 'environment.project'],
            'template_key' => ['key' => $key, 'relation' => 'template'],
            'template_key_project' => ['key' => $key, 'relation' => 'template.project'],
        ];
        return $this->getModels('Stack', $parameters, $relations, $key);
    }

    /**
     * @inheritdoc
     */
    public function getStack($parameters = [], $key = 'id') {
        $result = $this->getStacks($parameters, $key)->first();
        if (empty($result)) {throw new StackNotFoundException();}
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function saveStack(Stack $model) {
        return $this->saveModel($model);
    }

    /**
     * @inheritdoc
     */
    public function deleteStack(Stack $model) {
        $this->deleteModel($model);
    }

    /**
     * @inheritdoc
     */
    public function getEnvironments($parameters = [], $key = 'id') {
        if (!in_array($key, ['id', 'name'])) {abort(500, 'Invalid persistence key');}
        $relations = [
            'project_key' => ['key' => $key, 'relation' => 'project']
        ];
        return $this->getModels('Environment', $parameters, $relations, $key);
    }

    /**
     * @inheritdoc
     */
    public function getEnvironment($parameters = [], $key = 'id') {
        $result = $this->getEnvironments($parameters, $key)->first();
        if (empty($result)) {throw new EnvironmentNotFoundException();}
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function saveEnvironment(Environment $model) {
        return $this->saveModel($model);
    }

    /**
     * @inheritdoc
     */
    public function deleteEnvironment(Environment $model) {
        $this->deleteModel($model);
    }

    /**
     * @inheritdoc
     */
    public function getProjects($parameters = [], $key = 'id') {
        if (!in_array($key, ['id', 'name'])) {abort(500, 'Invalid persistence key');}
        $relations = [];
        return $this->getModels('Project', $parameters, $relations, $key);
    }

    /**
     * @inheritdoc
     */
    public function getProject($parameters = [], $key = 'id') {
        $result = $this->getProjects($parameters, $key)->first();
        if (empty($result)) {throw new ProjectNotFoundException();}
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function saveProject(Project $model) {
        return $this->saveModel($model);
    }

    /**
     * @inheritdoc
     */
    public function deleteProject(Project $model) {
        $this->deleteModel($model);
    }

    /**
     * @inheritdoc
     */
    public function getTemplates($parameters = [], $key = 'id') {
        if (!in_array($key, ['id', 'name'])) {abort(500, 'Invalid persistence key');}
        $relations = [
            'project_key' => ['key' => $key, 'relation' => 'project']
        ];
        return $this->getModels('Template', $parameters, $relations, $key);
    }

    /**
     * @inheritdoc
     */
    public function getTemplate($parameters = [], $key = 'id') {
        $result = $this->getTemplates($parameters, $key)->first();
        if (empty($result)) {throw new TemplateNotFoundException();}
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function saveTemplate(Template $model) {
        return $this->saveModel($model);
    }

    /**
     * @inheritdoc
     */
    public function deleteTemplate(Template $model) {
        $this->deleteModel($model);
    }
}
