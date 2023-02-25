<?php

namespace App\Services;

use App\Models\Banco;
use App\Models\Metodo;
use App\Models\MetodoCobro;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Session;
use DB;

// Eventos
use Event;

use Uuid;
use Config;
use Log;
use Crypt;

// Repositorios
use App\Repositories\OpcionRepository;

class MetodoCobroService
{
    private $calcularViaje;
    private $mailService;
    private $opcionRepository;

    public function __construct(CalcularViaje $calcularViaje, MailService $mailService, OpcionRepository $opcionRepository)
    {
        $this->calcularViaje = $calcularViaje;
        $this->mailService = $mailService;
        $this->opcionRepository = $opcionRepository;
    }

    public function getDomiciliacionByIBAN($iban) {

        $iban = str_replace(' ','', $iban);

        $codigoBanco = substr($iban, 4, 4);

        $banco = Banco::find($codigoBanco);

        if(!is_null($banco)) {
            return $banco->nombre;
        } else {
            return null;
        }

    }

    /** Crea un metodo de cobro por IBAN **/
    public function createMetodoCobroByIBAN($iban) {

        $usuario = Auth::user();

        $codigoBanco = substr($iban, 4, 4);

        $banco = Banco::find($codigoBanco);

        $metodoCobro = new MetodoCobro();

        if(!is_null($banco)) {
            $metodoCobro->domiciliacion = $banco->nombre;
        }

        $metodoCobro->titular = $usuario->configuracion->nombre .' '.$usuario->configuracion->apellidos;

        $metodoCobro->usuario_id = $usuario->id;

        $metodoCobro->iban = Crypt::encrypt($iban);

        $metodoCobro->tipo_metodo_id = Metodo::TRANSFERENCIA;

        $metodoCobro->created_at = Carbon::now();
        $metodoCobro->updated_at = Carbon::now();

        // Si no existe metodo paypal por defecto, ponemos este por defecto
//        if(is_null(MetodoCobro::where([['usuario_id', $usuario->id],['tipo_metodo_id', Metodo::PAYPAL],['defecto', 1]])->first())) {
            $metodoCobro->defecto = 1;
//        }

        $metodoCobro->save();

    }

    /** Crea un metodo de cobro por Paypal **/
    public function createMetodoCobroByEmail($email) {

        $usuario = Auth::user();

        $metodoCobro = new MetodoCobro();

        $metodoCobro->usuario_id = $usuario->id;

        $metodoCobro->email = $email;

        $metodoCobro->tipo_metodo_id = Metodo::PAYPAL;

        $metodoCobro->created_at = Carbon::now();
        $metodoCobro->updated_at = Carbon::now();

        // Si no existe metodo de transferencia por defecto, ponemos este por defecto
//        if(is_null(MetodoCobro::where([['usuario_id', $usuario->id],['tipo_metodo_id', Metodo::TRANSFERENCIA],['defecto', 1]])->first())) {
            $metodoCobro->defecto = 1;
//        }

        $metodoCobro->save();

    }
}
