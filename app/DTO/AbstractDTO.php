<?php

declare(strict_types=1);

namespace App\DTO;

use Illuminate\Support\Str;

class AbstractDTO
{
    public function __construct(array $data = [])
    {
        foreach ($data as $property => $value) {
            $camelCaseProperty = Str::camel($property);

            if (property_exists($this, $camelCaseProperty)) {
                $type = (new \ReflectionProperty($this, $camelCaseProperty))
                    ->getType()?->getName()
                ;

                $this->{$camelCaseProperty} = match ($type) {
                    'bool' => (bool) $value,
                    'int' => (int) $value,
                    'float' => (float) $value,
                    default => (string) $value,
                };
            }
        }
    }
}
