<?php

namespace App\Application\Models;

use App\Application\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Post extends Model
{
	use HasUuid;

	const STATUS_ONLINE = 'online';
	const STATUS_OFFLINE = 'offline';

	protected $fillable = [
		'uuid',
		'title',
		'body',
		'status',
	];

	protected $hidden = [
		'id',
	];

	public $timestamps = false;

	public function tags(): BelongsToMany
	{
		return $this->belongsToMany(Tag::class);
	}
}
