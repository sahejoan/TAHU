<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateAsistenciaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('asistencia', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->datetime('hora_e')->nullable();
            $table->datetime('hora_s')->nullable();
            $table->datetime('hora_e2')->nullable();
            $table->datetime('hora_s2')->nullable();            
            $table->datetime('hora_e3')->nullable();
            $table->datetime('hora_s3')->nullable();            
            $table->datetime('hora_e4')->nullable();
            $table->datetime('hora_s4')->nullable();            
            $table->datetime('hora_e5')->nullable();
            $table->datetime('hora_s5')->nullable();            
            $table->datetime('hora_e6')->nullable();
            $table->datetime('hora_s6')->nullable();            
            $table->datetime('hora_e7')->nullable();
            $table->datetime('hora_s7')->nullable();            
            $table->datetime('hora_e8')->nullable();
            $table->datetime('hora_s8')->nullable();            
            $table->unsignedBigInteger('id_func');
            $table->unsignedBigInteger('id_dep')->nullable();
            $table->string('observacion',200)->nullable();            

            $table->string('o1',200)->nullable();            
            $table->string('o2',200)->nullable();            
            $table->string('o3',200)->nullable();            
            $table->string('o4',200)->nullable();            
            $table->string('o5',200)->nullable();            
            $table->string('o6',200)->nullable();            
            $table->string('o7',200)->nullable();            
            $table->string('o8',200)->nullable();            
            $table->string('o9',200)->nullable();            
            $table->string('o10',200)->nullable();            
            $table->string('o11',200)->nullable();            
            $table->string('o12',200)->nullable();            
            $table->string('o13',200)->nullable();            
            $table->string('o14',200)->nullable();            
            $table->string('o15',200)->nullable();            
            $table->string('o16',200)->nullable();            

            $table->unsignedBigInteger('created_by')->nullable(); 
            $table->unsignedBigInteger('updated_by')->nullable(); 
            $table->unsignedBigInteger('deleted_by')->nullable();             

            $table->index('id_func','id_func');
                                 
            $table->timestamps();

            $table->foreign('id_func')->references('id')->on('funcionarios')->onUpdate('cascade')->onDetele('restrict');
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
        Schema::dropIfExists('asistencia');
    }
}