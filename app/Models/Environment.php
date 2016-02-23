<?php namespace Highcore\Models;

/**
 * Class Environment
 * @SWG\Definition(
 *     @SWG\Property(property="id", type="integer"),
 *     @SWG\Property(property="name", type="string"),
 *     @SWG\Property(property="parameters", type="array",
 *         @SWG\Items(ref="#/definitions/Parameter")
 *     )
 * )
 *
 * @package Highcore\Models
 * @property Project project
 * @property string name
 */
class Environment extends Model {

    public function __construct($data) {
        $env_data = $data;
        parent::__construct($env_data);
    }

}
