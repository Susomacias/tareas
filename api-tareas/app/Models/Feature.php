<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    protected $table = 'features';
    
    protected $fillable = [
        'name','description'
    ];


    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }   

    public function article(){
        return $this->belongsTo('App\Models\Article', 'article_id');
    } 

    public function Feature(){
        return $this->hasMany('App\Models\Feature');
    }
    
}