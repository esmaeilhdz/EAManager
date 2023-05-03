<?php

namespace App\Http\Controllers;

use App\Captcha;
use App\Exceptions\ApiException;
use App\Models\User;
use App\Traits\Common;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    use Common;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * دریافت کپچا
     * @return \Illuminate\Http\JsonResponse
     * @throws ApiException
     */
    public function getCaptcha(): \Illuminate\Http\JsonResponse
    {
        $captcha = new Captcha();
        $captcha = $captcha->getMixedCaptcha(env('CAPTCHA_LENGTH', 5));

        $return = [
            'captcha' => $captcha['image'],
            'mime_type' => $captcha['mime_type'],
            'key' => $captcha['result_captcha']['code'],
            'expire_at' => [
//                'jalali' => jdate($captcha['result_captcha']['expire_at'])->format('Y/m/d H:i:s'),
                'gregorian' => $captcha['result_captcha']['expire_at']
            ]
        ];

        return $this->api_response->response(200, __('messages.success'), $return);
    }

    /**
     * تولید توکن
     * @param Request $request
     * @return JsonResponse
     * @throws ApiException
     */
    public function login(Request $request): JsonResponse
    {
        $inputs = $request->all();

        $rules = [
            'captcha' => 'required|string',
            'captcha_key' => 'required|string|size:16',
            'username' => 'required|string',
            'password' => 'required|string',
        ];

        $validator = Validator::make($inputs, $rules);
        if ($validator->fails()) {
            return $this->api_response->response(400, $this->structureValidationMessage($validator->getMessageBag()), null);
        }

        $this->cleanInput($inputs, ['captcha', 'captcha_key', 'username', 'password']);

        $captcha = new Captcha();
        $result = $captcha->checkCaptcha($inputs['captcha'], $inputs['captcha_key']);
        if (!$result['result']) {
            return $this->api_response->response($result['result'], $result['message'], $result['data']);
        }

        try {
            if (!Auth::attempt(['mobile' => $request['username'], 'password' => $request['password']])) {
                if (!Auth::attempt(['email' => $request['username'], 'password' => $request['password']])) {
                    return $this->api_response->response(401, __('messages.username_password_wrong'), null);
                }
            }

            $user = User::query()
                ->where('email', $inputs['username'])
                ->orWhere('mobile', $inputs['username'])
                ->firstOrFail();
        } catch (\Exception $e) {
            throw new ApiException($e, false);
        }

        // ایجاد توکن
        $token = $user->createToken('auth_token')->plainTextToken;

        $now = Carbon::now();
        $token_expire_at = $now->addMinutes(120);

        $user->api_token = $token;
        $user->token_expire_at = $token_expire_at;
        $user->save();

        $data = [
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
        return $this->api_response->response(200, __('messages.welcome'), $data);
    }

    /**
     * حذف توکن
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function logOut(Request $request): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();
        $user->tokens()->where('id', $user->currentAccessToken()->id)->delete();

        return $this->api_response->response(200, __('messages.success'), null);
    }
}
