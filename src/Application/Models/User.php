<?php

namespace App\Application\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Model
{
	protected $fillable = [
		'giveName',
		'familyName',
		'email',
		'password',
		'dateOfBirth',
	];

	protected $hidden = [
		'id',
		'password',
	];

	protected $appends = [
		'name',
	];


	// Disable timestamps if not needed
	public $timestamps = false;

	public static function boot()
	{
		parent::boot();

		static::creating(function ($model) {
			$model->createdAt = $model->freshTimestamp();
		});
	}

	public function address(): HasOne
	{
		return $this->hasOne(UserAddress::class, 'userId');
	}

	public function getNameAttribute()
	{
		return trim("$this->giveName $this->familyName");
	}

}
