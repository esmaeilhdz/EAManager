<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class MbMax implements Rule
{

    private int $length;
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($length)
    {
        $this->length = $length;
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
        return mb_strlen($value) <= $this->length;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "تعداد کاراکترهای :attribute نباید بیشتر از $this->length کاراکتر باشد.";
    }
}
