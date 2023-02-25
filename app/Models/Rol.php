<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\Rol
 *
 * @property int $id
 * @property string $tipo
 * @property string $descripcion
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Usuario[] $usuarios
 * @method static \Illuminate\Database\Eloquent\Builder|Rol newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Rol query()
 * @method static \Illuminate\Database\Eloquent\Builder|Rol whereDescripcion($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rol whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Rol whereTipo($value)
 * @mixin \Eloquent
 */
class Rol extends Model
{

    protected $table = 'roles';
    public $timestamps = false;

    const ADMINISTRADOR = 1;
    const USUARIO = 2;
    const VIAJERO = 3;
    const PUNTO = 4;
    const BLOG = 5;
    const CLIENTE = 6;
    const CLIENTE_POTENCIAL = 7;
    const VIAJERO_POTENCIAL = 8;
    const BUSINESS = 9;

    public function usuarios()
    {
        return $this->belongsToMany('App\Models\Usuario', 'rol_usuario');
    }
}
