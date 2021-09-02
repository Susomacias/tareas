<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Row extends Model
{
    protected $table = 'rows';
    
    protected $fillable = [
        'name','email','phone','address','data','observations'
    ];


    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }

    public function budguet(){
        return $this->belongsTo('App\Models\Budguet', 'budguet_id');
    } 


    public function Row(){
        return $this->hasMany('App\Models\Row');
    }
    
}