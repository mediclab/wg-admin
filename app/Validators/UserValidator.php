<?php

declare(strict_types=1);

namespace App\Validators;

use Illuminate\Validation\Factory as ValidatorFactory;
use Illuminate\Validation\Validator;

class UserValidator
{
    public static function rules(): array
    {
        return [
            'name' => 'required|string|min:6|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
        ];
    }

    public static function getValidator(array $data): Validator
    {
        return \Validator::make($data, self::rules());
    }
}
