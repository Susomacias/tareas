<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ListModel extends Model
{
    protected $table = 'lists';
    
    protected $fillable = [
        'name'
    ];


    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function category(){
        return $this->hasMany('App\Models\TaskModel');
    }
    
}