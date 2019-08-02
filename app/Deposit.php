<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deposit extends Model
{
    public function client()
		{
			return $this->belongsTo('App\Client');
		}

		public function operations()
		{
			return $this->hasMany('App\Operation');
		}
}
