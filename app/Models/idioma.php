<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class idioma extends Model
{
    use HasFactory;

    public function recursos(){
        return $this->hasMany('App\Models\recurso','idioma_id');
    }
}
