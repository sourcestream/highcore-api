<?php namespace Highcore;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Stack
 *
 * @property string name
 * @property bool provisioned
 * @property int environment_id
 * @property int template_id
 *
 * @method static Stack find($id, $columns = array('*'))
 *
 * @package Bestend
 */
class Stack extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'stacks';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'parameters' => 'array',
        'components' => 'array',
        'stacks' => 'array',
        'ui' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'parameters',
        'components',
        'stacks',
        'ui',
        'environment_id',
        'template_id',
        'provisioned',
    ];

    public function __construct(array $attributes = array())
    {
        unset($attributes['status']);
        parent::__construct($attributes);

        $this->components = $this->components ?: [];
        $this->parameters = $this->parameters ?: [];
        $this->stacks = $this->stacks ?: [];
    }

    public function environment()
    {
        return $this->belongsTo('Highcore\Environment');
    }

    public function template()
    {
        return $this->belongsTo('Highcore\Template');
    }

    public function modules()
    {
        return $this->hasMany('Highcore\Module');
    }
}
