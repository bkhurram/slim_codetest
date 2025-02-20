<?php

namespace App\Application\Services;

use Illuminate\Support\MessageBag;
use Illuminate\Validation\Factory;

class ValidatorService
{
    protected Factory $validator;

    public function __construct()
    {
        // Initialize the Validator factory without translation
        $this->validator = new Factory(new DummyTranslator());
    }

    public function validate(array $data, array $rules, array $messages = []): ?MessageBag
    {
        // Perform validation
        $validation = $this->validator->make($data, $rules, $messages);

        if ($validation->fails()) {
            return $validation->errors();
        }

        return null;
    }
}
