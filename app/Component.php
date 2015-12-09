<?php namespace Highcore;

use Illuminate\Database\Eloquent\Model;

class Component extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'modules';

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'parameters' => 'array',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'parameters',
    ];
    public function __construct(array $attributes = array())
    {
        unset($attributes['status']);
        parent::__construct($attributes);
    }

    public function project()
    {
        return $this->belongsTo('Highcore\Project');
    }
}
