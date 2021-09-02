<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'department';
    
    protected $fillable = [
        'name','description'
    ];


    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function category(){
        return $this->hasMany('App\Models\Category');
    }

    public function Article(){
        return $this->hasMany('App\Models\Article');
    }
    
}