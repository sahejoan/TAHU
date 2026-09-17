<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDesignacionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('designacion', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('numdesig',10)->nullable();
            $table->date('fecha_desig')->nullable();
            $table->date('fecha_cese')->nullable();
            $table->string('estatus',10);            
            $table->string('provi_jefe',20);            
            $table->date('fecha_provi')->nullable(); 
            $table->unsignedBigInteger('gaceta_provi');
            $table->date('fecha_gaceta')->nullable(); 
            
            $table->unsignedBigInteger('id_func');
            $table->unsignedBigInteger('id_dep');
            $table->unsignedBigInteger('id_jefedep');
            $table->unsignedBigInteger('id_area');
            $table->unsignedBigInteger('id_divi');
            $table->unsignedBigInteger('id_firma');
            $table->unsignedBigInteger('id_jefediv');
            $table->unsignedBigInteger('id_jefeth');
            $table->unsignedBigInteger('id_coorth');

            $table->unsignedBigInteger('created_by')->nullable(); 
            $table->unsignedBigInteger('updated_by')->nullable(); 
            $table->unsignedBigInteger('deleted_by')->nullable();             

            $table->index('id_func','id_func');
            $table->index('id_dep','id_dep');
            $table->index('id_jefedep','id_jefedep');
            $table->index('id_area','id_area');                 
            $table->index('id_divi','id_divi');                 
            $table->index('id_jefediv','id_jefediv');                 
            $table->index('id_jefeth','id_jefeth');                 
            $table->index('id_coorth','id_coorth');                 
            $table->index('id_firma','id_firma');                 


            $table->timestamps();

            $table->foreign('id_func')->references('id')->on('funcionarios')->onUpdate('cascade')->onDetele('restrict');
            $table->foreign('id_dep')->references('id')->on('dependencia')->onUpdate('cascade')->onDetele('restrict');
            $table->foreign('id_jefedep')->references('id')->on('jefedependencia')->onUpdate('cascade')->onDetele('restrict');
            $table->foreign('id_area')->references('id')->on('area')->onUpdate('cascade')->onDetele('restrict');
            $table->foreign('id_divi')->references('id')->on('division')->onUpdate('cascade')->onDetele('restrict');            
            $table->foreign('id_firma')->references('id')->on('firmas')->onUpdate('cascade')->onDetele('restrict');
            $table->foreign('id_jefediv')->references('id')->on('jefedependencia')->onUpdate('cascade')->onDetele('restrict');
            $table->foreign('id_jefeth')->references('id')->on('jefedependencia')->onUpdate('cascade')->onDetele('restrict');
            $table->foreign('id_coorth')->references('id')->on('users')->onUpdate('cascade')->onDetele('restrict');

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
        Schema::dropIfExists('designacion');
    }
}