<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';
    
    protected $fillable = [
        'name','description'
    ];


    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }   

    public function department(){
        return $this->belongsTo('App\Models\Department', 'department_id');
    }  

    public function category(){
        return $this->belongsTo('App\Models\Category', 'category_id');
    }  

    public function Article(){
        return $this->hasMany('App\Models\Article');
    }
    
}