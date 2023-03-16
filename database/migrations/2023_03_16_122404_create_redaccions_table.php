<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('redaccions', function (Blueprint $table) {
            $table->bigIncrements('id');    
            $table->string('usuario_id');
            $table->longText('texto');
            $table->longText('coreccion');
            $table->integer('corregido');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('redaccions');
    }
};
