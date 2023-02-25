<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class familiaRecurso extends Model
{
    use HasFactory;

    public function vocabulario(){
        return $this->hasMany('App\Models\vocabulario','familia_id');
    }
}
