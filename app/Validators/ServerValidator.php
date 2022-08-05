<?php

declare(strict_types=1);

namespace App\Validators;

use App\Helpers\Util;
use App\Rules\InIpRange;
use Illuminate\Validation\Validator;

class ServerValidator
{
    public static function rules(array $addresses, array $ports): array
    {
        return [
            'address' => [
                'required',
                'ipv4',
                'ends_with:.1',
                'unique:servers,address',
                new InIpRange($addresses['max'], $addresses['min']),
            ],
            'port' => [
                'required',
                'integer',
                'unique:servers,port',
                "min:{$ports['min']}",
                "max:{$ports['max']}",
            ],
        ];
    }

    public static function getValidator(array $data, array $addresses, array $ports): Validator
    {
        return \Validator::make($data, self::rules($addresses, $ports));
    }
}
