<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
	// Mass assignation
	protected $fillable = ['deposit_id', 'operation_datetime', 'operation_amount', 'type'];
	public $timestamps = false;

	public function deposit()
	{
		return $this->belongsTo('App\Deposit');
	}
}
