<?php namespace Highcore\Models;

/**
 * Class Component
 * @package Highcore\Models
 *
 * @SWG\Definition(
 *     @SWG\Property(property="id", type="string"),
 *     @SWG\Property(property="template_component", type="string"),
 *     @SWG\Property(property="ui", ref="#/definitions/Ui"),
 *     @SWG\Property(property="status", ref="#/definitions/AwsStatus"),
 *     @SWG\Property(property="parameters", type="array",
 *         @SWG\Items(ref="#/definitions/Parameter")
 *     )
 * )
 *
 * @property string $id
 * @property string $template_component
 * @property Collection $parameters
 * @property \Aws\CloudFormation\Enum\ResourceStatus|string $status
 */
class Component extends Model {

    public function __construct($data) {
        $component_data = $data;
        parent::__construct($component_data);
    }
}
