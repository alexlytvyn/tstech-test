<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    public function deposits()
		{
			return $this->hasMany('App\Deposit');
		}
}
