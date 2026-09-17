<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateFirmasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('firmas', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();            
            $table->string('firma',190);            
            $table->unsignedBigInteger('id_dep');
            $table->tinyInteger('actual');
            $table->string('provi_jefe',20);            
            $table->date('fecha_provi')->nullable(); 
            $table->unsignedBigInteger('gaceta_provi');
            $table->date('fecha_gaceta')->nullable(); 

            $table->unsignedBigInteger('created_by')->nullable(); 
            $table->unsignedBigInteger('updated_by')->nullable(); 
            $table->unsignedBigInteger('deleted_by')->nullable();             
            $table->index('id_dep','id_dep');

            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
            $table->foreign('id_dep')->references('id')->on('dependencia')->onUpdate('cascade')->onDetele('restrict');                                 

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
        Schema::dropIfExists('firmas');
    }
}