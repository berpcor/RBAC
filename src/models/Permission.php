<?php

use \Illuminate\Database\Eloquent\Model as Eloquent;

    class Permission extends Eloquent{
        public function roles()
        {
            return $this->belongsToMany('Role');
        }  
    }