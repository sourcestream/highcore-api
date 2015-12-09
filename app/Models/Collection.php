<?php namespace Highcore\Models;

class Collection extends \Illuminate\Support\Collection {

    /**
     * @inheritdoc
     */
    public function __construct($items = null, $class_slug = null, $key_by = null) {
        $items = is_null($items) ? [] : $items;
        if ($items instanceof \Illuminate\Support\Collection) {
            return parent::__construct($items);
        }
        if ($class_slug) {
            $items = array_map(function ($item) use ($class_slug) {
                if (is_null($item)) {
                    return $item;
                }
                if (!is_array($item)) {
                    throw new \Exception("Failed to cast a model");
                }
                $class = Model::getModelClass($class_slug);
                return new $class($item);
            }, $items);
        }
        if ($key_by) {
            $items = array_build($items, function($key, $value) use($key_by) {
                /** @var Model $value */
                $key = $value instanceof Model ? $value->get($key_by) : data_get($value, $key_by);

                return [$key, $value];
            });
        }
        return parent::__construct($items);
    }

    /**
     * Flatten a multi-dimensional associative array with configurable delimiter
     *
     * @param  array   $array
     * @param  string  $prepend
     * @return array
     */
    public static function dotify($array, $prepend = '', $delimiter = null)
    {
        $results = [];

        foreach ($array as $key => $value)
        {
            if (is_array($value))
            {
                $results = array_merge($results, static::dotify($value, $prepend.$key.($delimiter===null ? chr(0x1D) : $delimiter)));
            }
            else
            {
                $results[$prepend.$key] = $value;
            }
        }

        return $results;
    }

    /**
     * Set an array item to a given value using "dot" notation.
     *
     * If no key is given to the method, the entire array will be replaced.
     *
     * @param  array   $array
     * @param  string  $key
     * @param  mixed   $value
     * @return array
     */
    public static function set(&$array, $key, $value, $delimiter = null)
    {
        if (is_null($key)) return $array = $value;

        $keys = explode($delimiter === null ? chr(0x1D) : $delimiter, $key);

        while (count($keys) > 1)
        {
            $key = array_shift($keys);

            // If the key doesn't exist at this depth, we will just create an empty array
            // to hold the next value, allowing us to create the arrays to hold final
            // values at the correct depth. Then we'll keep digging into the array.
            if ( ! isset($array[$key]) || ! is_array($array[$key]))
            {
                $array[$key] = [];
            }

            $array =& $array[$key];
        }

        $array[array_shift($keys)] = $value;

        return $array;
    }

    /**
     * @param Collection $items
     * @return $this
     */
    public function mergeRecursive($items) {
        $merged_dot = array_merge(static::dotify($this->toArray()), static::dotify($items->toArray()));
        $merged = [];
        foreach ($merged_dot as $key => $value) {
            static::set($merged, $key, $value);
        }
        foreach ($merged as $key => $value) {
            $class = get_class($this->get($key) ?: $items->get($key));
            static::set($merged, $key, new $class($value));
        }
        $result = new static($merged);
        return $result;
    }

    /**
     * @inheritdoc
     */
    public function get($key = null, $default = null) {
        if (empty($key)) {
            return $this->items;
        }
        return parent::get($key, $default);
    }

    public function pluck($key = null){
        return new Collection(
            array_map(function($item) use ($key) {
                return $item->get($key);
            }, $this->items)
        );
    }

    public function replaceKeys(Callable $callback) {
        $keys = array_keys($this->items);
        $changed_keys = array_map(function($key) use($callback) {return $callback($key);}, $keys);
        return static::make(array_combine($changed_keys, $this->items));
    }

    /**
     * @inheritdoc
     * @param string $class Cast data arrays to models of the specified class
     * @param string $key_by Key collection using a getter with the specified key
     *
     * @return static
     */
    public static function make($items = null, $class = null, $key_by = null)
    {
        return new static($items, $class, $key_by);
    }
}
