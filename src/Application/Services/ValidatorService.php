<?php

namespace App\Application\Services;

use Illuminate\Validation\Factory;
use Illuminate\Validation\Validator;

class ValidatorService
{
    protected $validator;

    public function __construct()
    {
        // Initialize the Validator factory without translation
        $this->validator = new Factory(new DummyTranslator());
    }

    public function validate(array $data, array $rules, array $messages = [])
    {
        // Perform validation
        $validation = $this->validator->make($data, $rules, $messages);

        if ($validation->fails()) {
            return $validation->errors();
        }

        return null;
    }
}
