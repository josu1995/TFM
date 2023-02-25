<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

use App\Models\TipoCodigoBarras;

class CodigoBarrasValidationRule implements Rule
{

    private $tipoCodigoBarras;

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct($tipoCodigoBarras = null)
    {
        $this->tipoCodigoBarras = $tipoCodigoBarras;
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
        if ($this->tipoCodigoBarras == null){
            $dato = explode('.', $attribute);
            
            if (sizeof($dato) > 1){
                $indice = $dato[1];
                $tipo = request()->input("data.{$indice}.tipo_codigo_barras");
                $tipoCodigoBarras = TipoCodigoBarras::where('nombre', $tipo)->first();
                if($tipoCodigoBarras){
                    $this->tipoCodigoBarras = $tipoCodigoBarras->id;
                } 
            }       
        }

        $resultado = false;
        $longitud = strlen(trim($value));
        switch ($this->tipoCodigoBarras){
            case TipoCodigoBarras::EAN13:
            {
                //13 números
                if($longitud == 13){
                    if (preg_match("/^[0-9]+$/", $value)) {
                        $arrayCodigoBarras = str_split($value);
                        $sumaPares = 0; //pares empezando por 0
                        $sumaImpares = 0;
                        for($i=0; $i<12; $i++){
                            if($i % 2 == 0) {
                                $sumaPares += $arrayCodigoBarras[$i];
                            } else {
                                $sumaImpares += $arrayCodigoBarras[$i];
                            }                                                    
                        }
                        $sumaImpares = $sumaImpares * 3;
                        $suma = $sumaImpares + $sumaPares;

                        // $sumaMultiplo = $suma;
                        // $digitoControl = 10 - $sumaMultiplo%10;

                        // while(($sumaMultiplo%10) > 0){
                        //     $sumaMultiplo++; 
                        // }
                        // $digitoControl = $sumaMultiplo - $suma;

                        $digitoControl = 10 - $suma%10;

                        if ($digitoControl == $arrayCodigoBarras[12]){
                            $resultado = true;
                        }
                    }
                }
                break;
            }
            case TipoCodigoBarras::EAN8:
            {
                //8 números
                if($longitud == 8){
                    if (preg_match("/^[0-9]+$/", $value)) {
                        $arrayCodigoBarras = str_split($value);
                        $sumaPares = 0; //pares empezando por 0
                        $sumaImpares = 0;
                        for($i=0; $i<7; $i++){
                            if($i % 2 == 0) {
                                $sumaPares += $arrayCodigoBarras[$i];
                            } else {
                                $sumaImpares += $arrayCodigoBarras[$i];
                            }                                                    
                        }
                        $sumaPares = $sumaPares * 3;
                        $suma = $sumaImpares + $sumaPares;

                        $digitoControl = 10 - $suma%10;

                        if ($digitoControl == $arrayCodigoBarras[7]){
                            $resultado = true;
                        }
                    }
                }
                break;
            }
            case TipoCodigoBarras::UPCA:
            {
                //12 números
                if($longitud == 12){
                    if (preg_match("/^[0-9]+$/", $value)) {
                        $arrayCodigoBarras = str_split($value);
                        $sumaPares = 0; //pares empezando por 0
                        $sumaImpares = 0;
                        for($i=0; $i<11; $i++){
                            if($i % 2 == 0) {
                                $sumaPares += $arrayCodigoBarras[$i];
                            } else {
                                $sumaImpares += $arrayCodigoBarras[$i];
                            }                                                    
                        }
                        $sumaPares = $sumaPares * 3;
                        $suma = $sumaImpares + $sumaPares;

                        $digitoControl = 10 - $suma%10;

                        if ($digitoControl == $arrayCodigoBarras[11]){
                            $resultado = true;
                        }
                    }
                }
                break;
            }
            case TipoCodigoBarras::Code128:
            {
                // if (preg_match("/[\x00-\x7F]/", $value)) {
                if(preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $value)){
                    $resultado = true;
                }
                break;
            }
            default:
                break;

        }

        $this->tipoCodigoBarras = null;
        
        return $resultado;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'El código de barras no tiene un formato válido';
    }
}
