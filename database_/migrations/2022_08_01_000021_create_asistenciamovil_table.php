<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAsistenciaMovilTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asistenciamovil', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();            
            $table->bigIncrements('id_func')->unsigned();            
            $table->bigIncrements('id_dep')->unsigned();            
            $table->text('observacion')->nullable();;                        

            $table->unsignedBigInteger('id_jefedep');             
            $table->tinyInteger('actual');
            $table->unsignedBigInteger('created_by')->nullable(); 
            $table->unsignedBigInteger('updated_by')->nullable(); 
            $table->unsignedBigInteger('deleted_by')->nullable();             

            $table->timestamps();
            
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
        Schema::dropIfExists('asistenciamovil');
    }
}