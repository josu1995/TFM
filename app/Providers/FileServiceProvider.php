<?php

namespace App\Providers;

use Validator;
use Illuminate\Support\ServiceProvider;
use Services\ValidarDni;

class FileServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return bool
     */
    public function boot()
    {
        Validator::extend('mimedocimg', function($attribute, $value, $parameters, $validator) {

            $acceptedExt = ['doc', 'docx', 'odt', 'pdf'];
            $ext = explode('.', $value->getClientOriginalName())[count(explode('.', $value->getClientOriginalName())) - 1];

            if(substr($value->getMimeType(), 0, 5) == 'image' || is_numeric(array_search($ext, $acceptedExt))) {
                return true;
            } else {
                return false;
            }
        });

        Validator::extend('mimedocimgname', function($attribute, $value, $parameters, $validator) {

            $acceptedExt = ['doc', 'docx', 'odt', 'pdf', 'tif', 'tiff', 'bmp', 'jpg', 'jpeg', 'gif', 'png'];
            $ext = explode('.', $value)[count(explode('.', $value)) - 1];

            if(is_numeric(array_search($ext, $acceptedExt))) {
                return true;
            } else {
                return false;
            }
        });
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        
    }
}