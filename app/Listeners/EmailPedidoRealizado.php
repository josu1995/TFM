<?php

namespace App\Listeners;

// Evento
use App\Events\PedidoRealizado;

// Mail
use Mail;

// Modelos
use App\Models\Usuario;
use App\Models\Pedido;

class EmailPedidoRealizado
{
    public function __construct()
    {
    }

    public function handle(PedidoRealizado $evento)
    {
        $pedido = $evento->pedido;
        $usuario = $evento->usuario;

        // Envio de mail a usuario que realiza el pedido
        $this->emailUsuario($pedido, $usuario);
    }

    private function emailUsuario($pedido, $usuario)
    {
        Mail::send('email.creacionEnvio', ['usuario' => $usuario, 'pedido' => $pedido], function ($m) use ($usuario) {
            $m->from(env('MAIL_ADDRESS'), env('MAIL_NAME'));
            $m->to($usuario->email, $usuario->configuracion->nombre)->subject('Detalles de tu envío');
        });
    }
}
