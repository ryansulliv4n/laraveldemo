<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model {

	public $timestamps = false;

	protected $fillable = [
		'user_id',
		'active_id',
		'firstname',
		'lastname',
		'email',
		'phone',
		'info',
	];

	protected $guarded = [
		'id',
	];
}
