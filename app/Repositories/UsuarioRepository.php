<?php

namespace App\Repositories;
// Servicios
use Validator;
use Hash;

// Modelos
use App\Models\Usuario;
use App\Models\Configuracion;
use App\Models\Rol;

class UsuarioRepository
{
    // Reglas de validación para actualización de configuración
    public $reglasConfiguracion = [
        'nombre' => 'required|max:255',
        'apellidos' => 'required|max:255',
        'telefono' => 'required|phone:ES,mobile',
        'date' => 'date',
        'dni' => 'required|dni'
    ];

    // Reglas validación de cambio de contraseña
    public $reglasPassword = [
        'password' => 'required',
        'nuevo' => 'required|min:6|confirmed',
    ];

    // Reglas validación de creación de contraseña
    public $reglasCrearPassword = [
        'nuevo' => 'required|min:6|confirmed',
    ];

    /**
     * Crear configuración de usuario por defecto.
     *
     * @param Usuario $usuario
     *
     * @return Usuario
     */
    private function crearConfiguracionUsuario(Usuario $usuario)
    {
        $configuracion = new Configuracion();

        $usuario->configuracion()->save($configuracion);

        return $usuario;
    }

    /**
     * Asignar rol a usuario.
     *
     * @param Usuario $usuario
     * @param String  $nombre  | nombre del rol a asignar
     *
     * @return Usuario
     */
    public function asignarRol(Usuario $usuario, $nombre)
    {
        $rol = Rol::where('nombre', $nombre)->first();

        if ($rol) {
            $usuario->roles()->save($rol);
        }

        return $usuario;
    }

    public function actualizarEmail($email) {



    }

    /**
     * Actualizar configuración de usuario.
     *
     * @param Usuario $usuario
     * @param Request $request
     *
     * @return Usuario
     */
    public function actualizarDireccion(Usuario $usuario, $datos)
    {
        $configuracion = $usuario->configuracion;

        foreach ($datos as $campo => $valor) {
                $configuracion->$campo = $valor;

        }
        $configuracion->save();
        return $usuario;
    }

    public function actualizarConfiguracion(Usuario $usuario, $datos)
    {
        $configuracion = $usuario->configuracion;

        foreach ($datos as $campo => $valor) {
            if($campo == 'telefono' && $configuracion->telefono !== $datos['telefono']) {
                $configuracion->movil_certificado = 0;
            }
            $configuracion->$campo = $valor;
        }

        $configuracion->save();

        return $usuario;
    }

    // Actualizar contraseña de usuario
    public function actualizarPassword(Usuario $usuario, $datos)
    {
        // Contraseña actual coincide con la enviada
        if(Hash::check($datos['password'], $usuario->password)) {
            $usuario->password = Hash::make($datos['nuevo']);
            $usuario->save();
            return true;
        } else {
            return false;
        }
    }

    // Crear password para usuarios nuevos desde facebook
    public function crearPassword(Usuario $usuario, $datos)
    {
        $usuario->password = Hash::make($datos['nuevo']);
        $usuario->save();

        return true;
    }

}
