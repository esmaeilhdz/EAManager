<?php

namespace App\Exceptions;

use App\Traits\Common;
use Exception;
use Illuminate\Support\Facades\Log;

class ApiException extends Exception
{
    use Common;

    // پیغام تولید شده دستی در کنترلر
    public $custom_message;

    // exception تولید شده در catch
    public $e;

    public function __construct($e = null, $has_lang = false)
    {
        parent::__construct();
        $this->custom_message = $this->errorHandling($e);
        $this->e = $e;
        if ($has_lang) {
            $this->custom_message = __('messages.' . $this->custom_message);
        }
    }

    /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report()
    {
        if ($this->e) {
            Log::error($this->e->getMessage());
        } else {
            Log::error($this->getMessage());
        }
    }

    public function getMessageByLang()
    {
        return $this->custom_message;
    }
}
