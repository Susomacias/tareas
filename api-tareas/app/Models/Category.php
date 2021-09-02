<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    
    protected $fillable = [
        'name','description'
    ];


    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }   

    public function department(){
        return $this->belongsTo('App\Models\Department', 'department_id');
    } 
    
    
}