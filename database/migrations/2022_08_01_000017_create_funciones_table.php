<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateFuncionesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funciones', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();            
            $table->unsignedBigInteger('id_cargo');
            $table->unsignedBigInteger('id_lfuncion');

            $table->unsignedBigInteger('created_by')->nullable(); 
            $table->unsignedBigInteger('updated_by')->nullable(); 
            $table->unsignedBigInteger('deleted_by')->nullable();             

            $table->index('id_cargo','id_cargo');
            $table->index('id_lfuncion','id_lfuncion');

            $table->timestamps();

            $table->foreign('id_cargo')->references('id')->on('cargo')->onUpdate('cascade')->onDetele('restrict');            
            $table->foreign('id_lfuncion')->references('id')->on('lfuncion')->onUpdate('cascade')->onDetele('restrict');            
                                 
            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');

            $table->engine='InnoDB';
            $table->collation='utf8_unicode_ci';                 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('funciones');
    }
}