<?php

namespace App\Models;

use App\Notifications\ResetPasswordNotification;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * App\Models\Usuario
 *
 * @property int $id
 * @property string $usuario
 * @property string|null $identificador
 * @property string $password
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Alerta[] $alertas
 * @property-read \App\Models\Configuracion $configuracion
 * @property-read \App\Models\ConfiguracionBusiness $configuracionBusiness
 * @property-read \App\Models\DatosFacturacion $datosFacturacion
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Envio[] $envios
 * @property-read \App\Models\Imagen $imagen
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Mensaje[] $mensajes
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MetodoCobro[] $metodosCobro
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\MetodoPago[] $metodosPago
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\NotificacionViaje[] $notificacionesViaje
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|
 * \Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Pago[] $pagos
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Pedido[] $pedidos
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\ProductoBusiness[] $productos
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Punto[] $puntos
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Puntuacion[] $puntuacion
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Rol[] $roles
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Saldo[] $saldo
 * @property-read \App\Models\Store $store
 * @property-read \App\Models\UsuarioOauth $usuarioOauth
 * @property-read \App\Models\Vehiculo $vehiculo
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Viaje[] $viajes
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|Usuario newQuery()
 * @method static \Illuminate\Database\Query\Builder|Usuario onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Usuario query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|Usuario whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Usuario whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Usuario whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Usuario whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Usuario whereIdentificador($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Usuario wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Usuario whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Usuario whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Usuario whereUsuario($value)
 * @method static \Illuminate\Database\Query\Builder|Usuario withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Usuario withoutTrashed()
 * @mixin \Eloquent
 */
class Usuario extends Authenticatable
{

    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'usuario', 'email', 'password',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];


    protected $table = 'usuarios';
    public $timestamps = true;

    use SoftDeletes;

    protected $dates = ['deleted_at'];


    public function hasRole($role)
    {
        return true;
    }


    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token, $this));
    }

    
}
