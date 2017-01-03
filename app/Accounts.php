<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Accounts extends Model
{
    public function project(){
        return $this->hasOne('App\Projects');
    }

    public function expenditure(){
        return $this->hasMany('App\Expenditure');
    }

    public function income(){
        return $this->hasMany('App\Income');
    }
}
