<?php namespace Highcore\Models;
/**
 * Class Template
 * @package Highcore\Models
 *
 * @property Project $project project
 * @property mixed $name
 * @property string $repository
 * @property string $refspec
 *
 * @SWG\Definition(
 *     @SWG\Property(property="id", type="integer"),
 *     @SWG\Property(property="project_id", type="integer"),
 *     @SWG\Property(property="name", type="string"),
 *     @SWG\Property(property="parameters", @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Parameter")))
 * )
**/

class Template extends Model {

    public function __construct($data) {
        $template_data = $data;
        parent::__construct($template_data);
    }

}
