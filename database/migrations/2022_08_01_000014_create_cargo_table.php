<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateCargoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cargo', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();            
            $table->unsignedBigInteger('id_lcargo');
            $table->date('fecha_ex');
            $table->unsignedBigInteger('id_area');
            $table->unsignedBigInteger('id_desig');
            $table->date('fecha_inic');
            $table->date('fecha_culm')->nullable();        
            $table->string('sta_contrato',20);
            $table->string('sta_cargo',20);
            $table->string('observaciones',190)->nullable();

            $table->unsignedBigInteger('created_by')->nullable(); 
            $table->unsignedBigInteger('updated_by')->nullable(); 
            $table->unsignedBigInteger('deleted_by')->nullable();             

            $table->index('id_lcargo','id_lcargo');
            $table->index('id_area','id_area');
            $table->index('id_desig','id_desig');

            $table->timestamps();

            $table->foreign('id_lcargo')->references('id')->on('lcargo')->onUpdate('cascade')->onDetele('restrict');            
            $table->foreign('id_area')->references('id')->on('area')->onUpdate('cascade')->onDetele('restrict');            
            $table->foreign('id_desig')->references('id')->on('designacion')->onUpdate('cascade')->onDetele('restrict');
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
        Schema::dropIfExists('cargo');
    }
}