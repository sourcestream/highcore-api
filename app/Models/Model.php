<?php namespace Highcore\Models;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;

/**
 * Class Model
 * @package Highcore\Models
 *
 * @property mixed $id
 */
class Model implements Arrayable, Jsonable
{
    protected $attributes = [];

    protected static $models = [
        'component' => true,
        'environment' => true,
        'project' => true,
        'stack' => true,
        'template' => true,
        'parameter' => true,
    ];

    public function __construct($data) {
        if ($data instanceof \Illuminate\Database\Eloquent\Model) {
            $data = $data->toArray();
        }
        foreach ((array) $data as $key => $value) {
            $this->$key = $value;
        }
    }

    /**
     * Convert the model to its string representation.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toJson();
    }

    /**
     * Convert the model instance to JSON.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Convert the model instance to an array.
     *
     * @param bool $preserve_keys Keep keys when casting collections to array
     * @return array
     */
    public function toArray($preserve_keys = false)
    {
        $attributes = array_map(function($attribute) use ($preserve_keys) {
            if ($attribute instanceof Model) {
                return $attribute->toArray($preserve_keys);
            }
            if ($attribute instanceof Collection) {
                $items = $attribute->toArray();
                return $preserve_keys ? $items : array_values($items);
            }
            return $attribute;
        }, $this->attributes);

        return $attributes;
    }

    /**
     * @param Model|Collection $model
     * @param null $key
     * @return $this
     */
    public function assign($model, $key = null) {
        $class = class_basename($model);
        $this->attributes[$key ?: snake_case($class)] = $model;
        return $this;
    }

    public function get($key = null, $default = null) {
        $attributes = $this->attributes;
        $result = data_get($attributes, $key, function() use(&$attributes, &$key, $default) {
            $segments = explode('.', $key);
            $attribute = array_shift($segments);
            if (empty($key)) {
                return $attributes;
            } else {
                return array_has($attributes, $attribute) ? $attributes[$attribute]->get(implode('.', $segments)) : value($default);
            }
        });
        return $result;
    }

    public function __get($name) {
        if (array_has($this->attributes, $name)) {
            return $this->get($name);
        }
        return data_get($this, $name);
    }

    public static function getModelClass($slug) {
        return __NAMESPACE__ . '\\' . studly_case($slug);
    }

    /**
     * @inheritdoc
     * @param array|\Illuminate\Database\Eloquent\Model $data Data source for the model
     *
     * @return $this
     */
    public static function make($data = [])
    {
        return new static($data);
    }

    /**
     * @param $key
     * @param $value
     * @return $this
     */
    public function __set($key, $value)
    {
        if (isset(self::$models[$key])) {
            $class = self::getModelClass($key);
            $this->attributes[$key] = new $class($value);
        } elseif (!is_null($value) && isset(self::$models[$singular = rtrim($key, 's')])) {
            $this->attributes[$key] = new Collection($value, $singular, 'id');
        } else {
            $this->attributes[$key] = $value;
        }

        return $this;
    }

    /**
     * @param $attributes
     * @return $this
     */
    public function fill($attributes)
    {
        foreach ($attributes as $key => $value) {
            $this->$key = $value;
        }
        return $this;
    }
}
