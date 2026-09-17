<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAreaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('area', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('nom_area',190);
            $table->string('ubicacion',190);
            $table->string('telf_area',15);
            $table->string('coord_area',190);
            $table->unsignedBigInteger('id_divi'); 
            $table->unsignedBigInteger('id_func'); 
            $table->unsignedBigInteger('created_by')->nullable(); 
            $table->unsignedBigInteger('updated_by')->nullable(); 
            $table->unsignedBigInteger('deleted_by')->nullable();             

            $table->index('id_func','id_func');
            $table->index('id_divi','id_divi');                             

            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
            $table->foreign('id_divi')->references('id')->on('division')->onUpdate('cascade')->onDetele('restrict');
            $table->foreign('id_func')->references('id')->on('funcionarios')->onUpdate('cascade')->onDetele('restrict');

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
        Schema::dropIfExists('area');
    }
}