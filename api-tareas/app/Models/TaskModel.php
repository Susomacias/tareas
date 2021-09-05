<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskModel extends Model
{
    protected $table = 'tasks';
    
    protected $fillable = [
        'name','description'
    ];


    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }   

    public function department(){
        return $this->belongsTo('App\Models\ListModel', 'list_id');
    } 
    
    
}