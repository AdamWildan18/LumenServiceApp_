<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = array('categories', 'post_id', 'user_id');

    public $timestamps = true;

    public function posts()
    {
        return $this->belongsToMany('App\Models\Post');
    }
}
