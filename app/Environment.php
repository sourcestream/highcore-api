<?php namespace Highcore;

use Illuminate\Database\Eloquent\Model;

class Environment extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'environments';

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
