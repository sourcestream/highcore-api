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
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'project_id',
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
