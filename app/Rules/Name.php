<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Name implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
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
        $testmin = strlen($value) >= 3;
        $testmax = strlen($value) <= 30;
        return ($testmin & $testmax);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The text must be larger than 2 and smaller than 31 characters.';
    }
}
