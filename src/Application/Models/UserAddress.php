<?php

namespace App\Application\Models;

use App\Application\Models\Concerns\HasUser;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
	use HasUser;

	protected $fillable = [
		'user_id',
		'street',
		'city',
		'post_code',
		'country_code',
		'lat',
		'lng'
	];

	protected $hidden = ['id'];

	public $timestamps = false;

	protected $casts = [
		'lat' => 'float',
		'lng' => 'float'
	];
}
