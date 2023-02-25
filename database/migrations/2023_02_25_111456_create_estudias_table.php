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
        Schema::create('estudias', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('recurso_id');
            $table->string('usuario_id');
            $table->integer('nivel');
            $table->date('fecha_ultima_repeticion');
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
        Schema::dropIfExists('estudias');
    }
};
