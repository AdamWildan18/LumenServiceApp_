<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = array('title', 'content', 'status', 'user_id');

    public $timestamps = true;

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function comment()
    {
        return $this->hasMany('App\Models\Comment');
    }

    public function categories()
    {
        return $this->belongsToMany('App\Models\Category');
    }
}
