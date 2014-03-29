<?php

use \Illuminate\Database\Eloquent\Model as Eloquent;

class Role extends Eloquent{
    public function users()
    {
        return $this->hasMany('User');
    }  
    public function permissions()
    {
        return $this->belongsToMany('Permission');
    } 
}