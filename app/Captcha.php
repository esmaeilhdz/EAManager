<?php

namespace App;

use App\Exceptions\ApiException;
use Illuminate\Support\Str;

class Captcha
{
    private $alphabet_chars = 'ABCDEFGHJKLMNPRSTWXYZ';
    private $number_chars = '123456789';
    private $mixed_chars;

    public function __construct()
    {
        $this->mixed_chars = env('CAPTCHA_POOL', 'ABCDEFGHJKLMNPRSTWXYZ123456789');
    }

    private function generateString($input, $strength = 5): string
    {
        $input_length = strlen($input);
        $random_string = '';
        for ($i = 1; $i <= $strength; $i++) {
            $random_character = $input[mt_rand(0, $input_length - 1)];
            $random_string .= $random_character;
        }

        return $random_string;
    }

    /**
     * تولید بک گراند کپچا
     * @return false|\GdImage|resource
     */
    private function renderCaptchaBackground()
    {
        $image = imagecreatetruecolor(200, 50);

        imageantialias($image, true);

        $colors = [];

        $red = rand(125, 175);
        $green = rand(125, 175);
        $blue = rand(125, 175);

        for ($i = 0; $i < 5; $i++) {
            $colors[] = imagecolorallocate($image, $red - 20 * $i, $green - 20 * $i, $blue - 20 * $i);
        }

        imagefill($image, 0, 0, $colors[0]);

        for ($i = 0; $i < 10; $i++) {
            imagesetthickness($image, rand(2, 10));
            $rect_color = $colors[rand(1, 4)];
            imagerectangle($image, rand(-10, 190), rand(-10, 10), rand(-10, 190), rand(40, 60), $rect_color);
        }

        return $image;
    }

    /**
     * تولید متن کپچا
     * @param $image
     * @param $characters
     * @param $string_length
     * @return bool[]
     * @throws ApiException
     */
    private function renderCaptchaString($image, $characters, $string_length)
    {
        $black = imagecolorallocate($image, 0, 0, 0);
        $white = imagecolorallocate($image, 255, 255, 255);
        $text_colors = [$black, $white];

        $fonts = [
            base_path('public' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . 'ttf' . DIRECTORY_SEPARATOR . 'arial.ttf'),
            base_path('public' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . 'ttf' . DIRECTORY_SEPARATOR . 'verdana.ttf'),
            base_path('public' . DIRECTORY_SEPARATOR . 'fonts' . DIRECTORY_SEPARATOR . 'ttf' . DIRECTORY_SEPARATOR . 'tahoma.ttf')
        ];

        $captcha_string = $this->generateString($characters, $string_length);

        for ($i = 0; $i < $string_length; $i++) {
            $letter_space = round(170 / $string_length);
            $initial = 15;

            imagettftext($image, 20, rand(-15, 15), $initial + $i * $letter_space, rand(20, 40), $text_colors[rand(0, 1)], $fonts[array_rand($fonts)], $captcha_string[$i]);
        }

//        return $image;
//        header('Content-type: image/png');
        ob_start();
        imagepng($image);
        $image_data = ob_get_contents();
        ob_end_clean();
        $image_data_base64 = base64_encode($image_data);
        imagedestroy($image);

        return [
            'image' => $image_data_base64,
            'mime_type' => 'image/png',
            'result_captcha' => $this->insertCaptcha($captcha_string)
        ];
    }

    /**
     * درج کد کپچا
     * @param $captcha_phrase
     * @return array
     * @throws ApiException
     */
    private function insertCaptcha($captcha_phrase): array
    {
        try {
            $minutes = config('custom.captcha_expire_time_minutes');
            $expire_at = date('Y-m-d H:i:s', strtotime("+$minutes minutes "));

            $captcha = new \App\Models\Captcha();

            $captcha->code = Str::random(16);
            $captcha->captcha = $captcha_phrase;
            $captcha->expire_at = $expire_at;

            return [
                'result' => $captcha->save(),
                'code' => $captcha->code,
                'expire_at' => $captcha->expire_at
            ];
        } catch (\Exception $e) {
            throw new ApiException($e, false);
        }
    }

    /**
     * @param int $string_length
     * @return bool[]
     * @throws Exceptions\ApiException
     */
    public function getAlphabetCaptcha(int $string_length = 5): array
    {
        $image = $this->renderCaptchaBackground();
        return $this->renderCaptchaString($image, $this->alphabet_chars, $string_length);
    }

    /**
     * @throws ApiException
     */
    public function getNumberCaptcha($string_length = 5): array
    {
        $image = $this->renderCaptchaBackground();
        return $this->renderCaptchaString($image, $this->number_chars, $string_length);
    }

    /**
     * @throws ApiException
     */
    public function getMixedCaptcha($string_length = 5): array
    {
        $image = $this->renderCaptchaBackground();
        return $this->renderCaptchaString($image, $this->mixed_chars, $string_length);
    }

    /**
     * بررسی صحت کد کپچا
     * @param $captcha
     * @param $code
     * @return array
     * @throws ApiException
     */
    public function checkCaptcha($captcha, $code): array
    {
        $result = [
            'result' => true,
            'message' => null,
            'data' => null
        ];

        try {
            $valid_captcha = \App\Models\Captcha::select(['id', 'is_enable'])
                ->where('captcha', $captcha)
                ->where('code', $code)
                ->where('expire_at', '>', now())
                ->where('is_enable', 1)
                ->first();
        } catch (\Exception $e) {
            throw new ApiException($e, false);
        }

        if (is_null($valid_captcha)) {
            $result = [
                'result' => false,
                'message' => __('messages.captcha_wrong'),
                'data' => null
            ];
        } else {
            try {
                $valid_captcha->is_enable = 0;
                $valid_captcha->save();
            } catch (\Exception $e) {
                throw new ApiException($e, false);
            }
        }

        return $result;
    }
}
