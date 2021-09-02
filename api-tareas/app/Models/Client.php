<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $table = 'clients';
    
    protected $fillable = [
        'name','email','phone','address','data','observations'
    ];


    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }   


    public function Client(){
        return $this->hasMany('App\Models\Client');
    }
    
}