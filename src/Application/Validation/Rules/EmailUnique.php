<?php

namespace App\Application\Validation\Rules;

use App\Application\Models\User;
use Illuminate\Contracts\Validation\Rule;

class EmailUnique implements Rule
{
	/**
	 * Create a new rule instance.
	 *
	 * @return void
	 */
	public function __construct(private $user_id = null)
	{
		//
	}

	/**
	 * Determine if the validation rule passes.
	 *
	 * @param  string  $attribute
	 * @param  mixed  $value
	 * @return bool
	 */
	public function passes($attribute, $value)
	{
		$query = User::query();
		$query->where('email', $value);
		if($this->user_id) {
			$query->where('id', '!=', $this->user_id);
		}

		if($query->exists()){
			return false;
		}

		return true;
	}

	/**
	 * Get the validation error message.
	 *
	 * @return string
	 */
	public function message()
	{
		return 'Email already used.';
	}
}
