<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\DataAwareRule;

class InIpRange implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(
        protected string $maxAddress,
        protected string $minAddress,
    ) {}

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        $validateInput = \Validator::make([
            'maxAddress' => $this->maxAddress,
            'minAddress' => $this->minAddress,
            'value' => $value
        ], [
            'maxAddress' => 'required|ipv4',
            'minAddress' => 'required|ipv4',
            'value' => 'required|ipv4',
        ])->passes();

        if (!$validateInput) {
            return false;
        }

        $intValue = ip2long($value);

        return ip2long($this->maxAddress) >= $intValue && $intValue >= ip2long($this->minAddress);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return "IP address must be in range from {$this->minAddress} to {$this->maxAddress}";
    }
}
