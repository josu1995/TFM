<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

use App\Models\TipoCodigoBarras;

class TipoCodigoBarrasValidationRule implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
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
		$existe = false;
		$tipoCodigoBarras = TipoCodigoBarras::where('nombre', $value)->first();
		if($tipoCodigoBarras){
			$existe = true;
		} 

		return $existe;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attribute no tiene un valor válido';
    }
}
