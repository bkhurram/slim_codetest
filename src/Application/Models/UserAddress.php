<?php

namespace App\Application\Models;

use App\Application\Models\Concerns\HasUser;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
	use HasUser;

	protected $fillable = [
		'userId',
		'street',
		'city',
		'post_code',
		'country_code',
		'latitude',
		'longitude'
	];

	protected $hidden = ['id'];

	public $timestamps = false;
}
