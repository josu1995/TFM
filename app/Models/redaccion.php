<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class redaccion extends Model
{
    use HasFactory;

    public function idioma(){
        return $this->belongsTo('App\Models\idioma','idioma_id');
    }
}
