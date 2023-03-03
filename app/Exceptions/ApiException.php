<?php

namespace App\Exceptions;

use App\Traits\Common;
use Exception;
use Illuminate\Support\Facades\Log;

class ApiException extends Exception
{
    use Common;

    // پیغام تولید شده دستی
    public string $custom_message;

    public Exception $e;

    public function __construct($e = null, $has_lang = false)
    {
        parent::__construct();
        $this->message = $this->errorHandling($e);
        Log::error($e->getMessage());
        $this->e = $e;
        if ($has_lang) {
            $this->message = __('messages.' . $this->custom_message);
        }
        abort(400, $this->message);
    }

    /**
     * Report or log an exception.
     *
     * @return void
     */
    public function report()
    {
        Log::error($this->e->getMessage());
    }
}
