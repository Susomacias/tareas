<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Budguet extends Model
{
    protected $table = 'budgets';
    
    protected $fillable = [
        'number_budguet', 'name','description','price','tax','total'
    ];


    public function user(){
        return $this->belongsTo('App\Models\User', 'user_id');
    }   

    public function client(){
        return $this->belongsTo('App\Models\Client', 'client_id');
    }  

    public function Budguet(){
        return $this->hasMany('App\Models\Budguet');
    }
    
}