<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $exception) {
            if ($exception instanceof ValidationException) {
                $errors = [];

                foreach ($exception->validator->getMessageBag()->getMessages() as $name => $error) {
                    $errors = array_merge($errors, [
                        $name => $error[0]
                    ]);
                }

                return response(res_template([
                    'status' => 422,
                    'message' => 'داده های نامعتبر',
                    'errors' => $errors,
                ]), 422);
            }
        });
    }
}
