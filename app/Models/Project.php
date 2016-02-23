<?php namespace Highcore\Models;
/**
 * Class Project
 * @package Highcore\Models
 *
 * @property string name
 * @property Collection parameters
 *
 * @SWG\Definition(
 *     @SWG\Property(property="id", type="integer"),
 *     @SWG\Property(property="name", type="string"),
 *     @SWG\Property(property="parameters", type="array",
 *         @SWG\Items(ref="#/definitions/Parameter")
 *     )
 * )
 *
 */
class Project extends Model {

    public function __construct($data) {
        $project_data = $data;
        parent::__construct($project_data);
    }

}
