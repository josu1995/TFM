<?php

namespace App\Services;

class GeneratorService
{

    public static function quickRandom($length)
    {
        $pool = '0123456789';

        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

    public static function generatePassword($length)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ*+#&%=@';

        return substr(str_shuffle(str_repeat($pool, $length)), 0, $length);
    }

}
