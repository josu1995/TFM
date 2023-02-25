<?php

namespace App\Providers;

use Validator;
use Illuminate\Support\ServiceProvider;
use Services\ValidarDni;

class DniServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return bool
     */
    public function boot()
    {
        Validator::extend('dni', function($attribute, $value, $parameters, $validator) {

                 //comprobamos que el formato sea el correcto
                 if(0 === preg_match("/\d{1,8}[a-z]/i", $value)){
                     return false;
                 }

                 $numero = substr($value,0,-1);
                 $letra = strtoupper(substr($value,-1));

                 //comprobar que la letra es la correcta
                 $replaced = strtr($numero, 'XYZ', '012');
                 if (!is_numeric($replaced) || $letra !== substr('TRWAGMYFPDXBNJZSQVHLCKE', $replaced % 23, 1)) {
                     return false;
                  } 

                  return true;

              });

        Validator::extend('nifNie', function($attribute, $value, $parameters, $validator) {

            return $this->validateNifNie($value);

        });

        Validator::extend('cif', function($attribute, $value, $parameters, $validator) {

            return $this->validateCif($value);

        });

        Validator::extend('nifNieCif', function($attribute, $value, $parameters, $validator) {

            return $this->validateNifNie($value) || $this->validateCif($value);

        });

    }

    private function validateNifNie($value) {
        //comprobamos que el formato sea el correcto
        if(0 === preg_match("/[X|Y|Z]\d{1,7}[a-z]|\d{1,8}[a-z]/i", $value)){
            return false;
        }

        $numero = substr($value,0,-1);
        $letra = strtoupper(substr($value,-1));

        //comprobar que la letra es la correcta
        $replaced = strtr($numero, 'XYZ', '012');
        if (!is_numeric($replaced) || $letra !== substr('TRWAGMYFPDXBNJZSQVHLCKE', $replaced % 23, 1)) {
            return false;
        }

        return true;
    }

    private function validateCif($value) {
        //comprobamos que el formato sea el correcto
        if(0 === preg_match("/[A-H|J|N|P-S|U-W]\d{1,7}[A-Z|\d]/i", $value)){
            return false;
        }

        $letra = substr($value,0,1);
        $provincia = substr($value,1,2);
        $control = substr($value, strlen($value) - 1,1);
        $numeros = substr($value, 1, 7);

        // 1.- Sumamos digitos pares
        $sumaPares = intval(substr($numeros, 1, 1)) + intval(substr($numeros, 3,1)) + intval(substr($numeros, 5,1));
        // 2.- Dígitos impares * 2 y sumar dígitos
        $sumaImpares = 0;
        for($i = 0 ; $i <= 6 ; $i+=2 ) {
            $numeroPor2 = intval(substr($numeros, $i, 1)) * 2;
            $sumaImpares += intval(substr($numeroPor2, 0,1)) + intval(substr($numeroPor2, 1, 1));
        }
        // 3.- Sumamos resultado de pares e impares
        $parImpar = $sumaPares + $sumaImpares;
        // 4.- Cogemos el dígito de las unidades de la suma anterior
        $unidad = intval(substr($parImpar, strlen($parImpar)-1, 1));
        // 5.- Restamos la unidad a 10 si es distinto de 0
        $digitoControl = 0;
        if($unidad != 0) {
            $digitoControl = 10 - $unidad;
        }

        $letrasControl = 'JABCDEFGHI';

        $letraControl = substr($letrasControl, $digitoControl, 1);

        // Comprobamos el dígito o letra de control
        if((strpos('PQSW', $letra) || $provincia == '00')) {
            if($control == $letraControl) {
                return true;
            }
        } else if (strpos('ABEH', $letra)) {
            if($control == $digitoControl) {
                return true;
            }
        } else {
            if($control == $digitoControl || $control == $letraControl) {
                return true;
            }
        }

        return false;
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