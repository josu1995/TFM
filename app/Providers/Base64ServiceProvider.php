<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Validator;

class Base64ServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('base64', function($attribute, $value, $parameters, $validator) {
            $image = base64_decode($value);
            $f = finfo_open();
            $result = finfo_buffer($f, $image, FILEINFO_MIME_TYPE);
            return $result == 'image/png' || $result == 'image/jpeg' || $result == 'image/gif' || $result == 'application/octet-stream';
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
