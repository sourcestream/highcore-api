<?php namespace Highcore\Models;

use Crypt;

/**
 * Class Parameter
 * @SWG\Definition(
 *     @SWG\Property(property="id", type="string"),
 *     @SWG\Property(property="value", type="string"),
 *     @SWG\Property(property="sensitive", type="boolean")
 * )
 * @property string $id
 * @property string $value
 * @package Highcore\Models
 */
class Parameter extends Model {
    public function __construct($data) {
        $parameter_data = $data;
        parent::__construct($parameter_data);
    }

    /**
     * @inheritdoc
     */
    public function fill($attributes) {
        parent::fill($attributes);
        if ($this->isSensitive() && array_has($attributes, 'value')) {
            $this->value = Crypt::encrypt($this->value);
        }
        return $this;
    }

    public function isSensitive() {
        $sensitive = $this->get('sensitive', false);
        return $sensitive && $sensitive != 'false';
    }
}
