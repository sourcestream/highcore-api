<?php namespace Highcore\Services\CloudFormer;

 use Highcore\Models\Component;
 use Highcore\Models\Parameter;
 use Illuminate\Contracts\Support\Arrayable;
 use Illuminate\Support\Collection;
 use Crypt;
 use Illuminate\Contracts\Encryption\DecryptException;

 class ParametersCollection extends Collection implements Arrayable {

     const SENSITIVE = 1;
     const INSENSITIVE = 2;
     const SNAKE = 4;
     const STUDLY = 8;
     const FLAT = 16;
     const DECRYPTED = 32;
     const NO_METADATA = 64;
     const DEFINED = 128;
     const MASK_SENSITIVE = 256;

     protected $filters = 0;
     protected $filters_closures = [];

     public function __construct($items = array(), $filters = 0) {
         parent::__construct($items);
         $this->filters = $filters;

         $this->filters_closures = [

            // Keep only sensitive
            self::SENSITIVE => function($k, $v, $filters) {
                $sensitive = $v instanceof Parameter ? $v->sensitive : data_get($v, 'sensitive');
                return $sensitive ? [$k, $v] : null;
            },

            // Keep only insensitive
            self::INSENSITIVE => function($k, $v, $filters) {
                $sensitive = $v instanceof Parameter ? $v->sensitive : data_get($v, 'sensitive');
                return $sensitive ? null : [$k, $v];
            },

            // Mask sensitive
            self::MASK_SENSITIVE => function($k, $v, $filters) {
                $sensitive = $v instanceof Parameter ? $v->sensitive : data_get($v, 'sensitive');
                $value = $v instanceof Parameter ? $v->value : data_get($v, 'value');
                if ($value and $sensitive) {
                    if ($v instanceof Parameter) {
                        $v = clone $v;
                        $v->value = '*****';
                    } else {
                        array_set($v, 'value', '*****');
                    }
                }
                return [$k, $v];
            },

            // Flatten keys
            self::FLAT => function($k, $v, $filters) {
                if ($v instanceof Parameter) {
                    $v = $v->get('value');
                }
                if (is_array($v)) {
                    $flat = array_build(array_dot($v), function($kk, $vv) use ($k, $filters) {
                        array_forget($filters, [self::SENSITIVE, self::INSENSITIVE]);
                        return static::filterKeyValue(str_replace('.', '_', "$k.$kk"), $vv, $filters);
                    });
                    return [null, value($flat)];
                }
                return [$k, $v];
            },

            // Studly case
            self::STUDLY => function($k, $v, $filters) {
                return [studly_case($k), $v];
            },

            // Snake case
            self::SNAKE => function($k, $v, $filters) {
                return [snake_case($k), $v];
            },

            // Decrypted values
            self::DECRYPTED => function($k, $v, $filters) {
                if (is_array($v)) {
                    $dot = array_dot($v);
                    $dot_decrypted = array_map(function($v) {
                        try {$v = Crypt::decrypt($v);} catch (DecryptException $e) {}
                        return $v;
                    }, $dot);
                    foreach ($dot_decrypted as $key => $decrypted_value) {
                        array_set($v, $key, $decrypted_value);
                    }
                }
                if (is_string($v)) {
                    try {$v = Crypt::decrypt($v);} catch (DecryptException $e) {}
                }
                return [$k, $v];
            },

            // Remove parameters with metadata
            self::NO_METADATA => function($k, $v, $filters) {
                return array_has(array_flip(['ui','status']), $k) ? null : [$k, $v];
            },

            // Remove parameters with null value
            self::DEFINED => function($k, $v, $filters) {
                return is_null($v) ? null : [$k, $v];
            },
         ];
     }

     public function toAws() {
         $aws_params = [];
         foreach ($this->toArray() as $key => $value) {
             $aws_params[] = [
                 'ParameterKey' => $key,
                 'ParameterValue' => $value,
             ];
         }
         return $aws_params;
     }

     public function toShell() {
         $items = $this->items;
         $keys = array_map(
             function($key) {
                 return '--' . strtr($key, '_', '-');
             },
             array_keys($items)
         );
         return array_combine($keys, $items);
     }

     public function toCollection() {
         $walk = $this->walk();
         return static::make($walk);
     }

     public function toArray() {
        return $this->walk();
     }

     public function now()
     {
         return static::make($this->walk());
     }

     public function toJson($options = 0) {
         return Collection::make($this->walk())->toJson();
     }

     public function walk($items = null) {
         $items = $items === null ? $this->items : $items;
         $result = [];
         foreach ($items as $key => $value) {
             if ($value instanceof Collection) {
                 $v = $this->walk($value->all());
                 $kv = $v ? [$key, $v] : null;
             } elseif ($value instanceof Component) {
                 $v = $this->walk($value->get());
                 $kv = $v ? [$key, $v] : null;

             } else {
                 $kv = static::filterKeyValue($key, $value, $this->getFilters());
             }
             if ($kv) {
                 list($k, $v) = $kv;
                 if (!$k && is_array($v)) {
                     $result = array_merge($result, $v);
                 } else {
                     $result[$k] = $v;
                 }
             }
         }
         return $result;
     }

     public function onlyIds($only_ids) {
         $only_ids = array_flip($only_ids);
         $filtered_items = [];
         foreach ($this->items as $key => $value) {
             $id = $value instanceof Parameter ? $value->id : $key;
             if (isset($only_ids[$id])) {
                 $filtered_items[$key] = $value;
             }
         }
         return static::make($filtered_items);
     }

     public static function filterKeyValue($k, $v, $filters) {
         $filters_original = $filters;
         /** @var Callable $filter */
         while ($filter = array_shift($filters)) {
             $result = $filter($k, $v, $filters);
             if (is_null($result)) return null;
             list($k, $v) = $result;
         }

         if ($v instanceof Parameter) {
             return static::filterKeyValue($v->get('id'), $v->get(), $filters_original);
         }

         return isset($result) ? $result : [$k, $v];
     }

    public function sensitive() {
        return static::make($this->items, $this->filters | static::SENSITIVE);
    }

     public function studly() {
         return static::make($this->items, $this->filters | static::STUDLY);
     }

     public function snake() {
         return static::make($this->items, $this->filters | static::SNAKE);
     }

     public function insensitive() {
         return static::make($this->items, $this->filters | static::INSENSITIVE);
     }

     public function maskSensitive() {
         return static::make($this->items, $this->filters | static::MASK_SENSITIVE);
     }

     public function flat() {
         return static::make($this->items, $this->filters | static::FLAT);
     }

     public function noMetadata() {
         return static::make($this->items, $this->filters | static::NO_METADATA);
     }

     public function defined() {
         return static::make($this->items, $this->filters | static::DEFINED);
     }

     public function decrypted() {
         return static::make($this->items, $this->filters | static::DECRYPTED);
     }

     protected function getFilters() {
         return array_only($this->filters_closures, array_filter(
             array_keys($this->filters_closures),
             function($key) {return $this->filters & $key;})
         );
     }

     public function parametersKeys() {
         $keys = array_pluck($this->items, 'id');
         return $keys;
     }

     /**
      * @inheritdoc
      * @param int $filters
      */
     public static function make($items = null, $filters = 0)
     {
         return new static($items, $filters);
     }
}
