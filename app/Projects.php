<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Projects extends Model
{
    public function budget(){
        return $this->hasOne('App\Budget');
    }


    public function accounts(){
        return $this->hasOne('App\Accounts');
    }

    public function expenditures(){
        return $this->hasMany('App\Expenditure');
    }

    public function departments(){
        return $this->belongsTo('App\Departments');
    }

    public function totalAmount(){
        return $this->hasOne('App\CalculatedTotal');
    }
}
