<?php namespace Highcore;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Project
 *
 * @property string name
 */
class Project extends Model {

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'projects';

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

    public function templates()
    {
        return $this->hasMany('Highcore\Template');
    }

    public function environments()
    {
        return $this->hasMany('Highcore\Environment');
    }
}
