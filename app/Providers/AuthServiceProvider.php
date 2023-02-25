<?php

namespace App\Providers;

use Illuminate\Contracts\Auth\Access\Gate as GateContract;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

// Modelos
use App\Models\Usuario;
use App\Models\Punto;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any application authentication / authorization services.
     *
     * @param  \Illuminate\Contracts\Auth\Access\Gate  $gate
     * @return void
     */
    public function boot(GateContract $gate)
    {
        $this->registerPolicies($gate);

        // No se permite procesar un pago sin mail/teléfono verificados
        $gate->define('usuario-validado', function ($usuario) {
            return $usuario->configuracion->movil_certificado && $usuario->configuracion->mail_certificado;
        });

        // Comprueba si el envío pertenece a un pedido (ya ha sido pagado)
        $gate->define('envio-pagado', function($envio) {
            return is_null($envio->pedido_id);
        });

        // Comprueba si 2 usuarios son iguales (permisos sobre recurso)
        $gate->define('propietario', function($usuarioAutenticado, $usuario) {

            return $usuarioAutenticado == $usuario;
        });
    }
}
