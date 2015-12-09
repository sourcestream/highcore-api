<?php namespace Highcore;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Template
 *
 * @property string  name
 * @property Project project
 */
class Template extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'templates';

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
        'project_id',
        'parameters',
        'repository',
        'refspec',
    ];

    public function project()
    {
        return $this->belongsTo('Highcore\Project');
    }

    public function stacks()
    {
        return $this->hasMany('Highcore\Stack');
    }
}
