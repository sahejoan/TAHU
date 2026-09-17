<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateDependenciaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dependencia', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('nom_dep',190);
            $table->string('ubicacion',190);
            $table->string('telf_dep',15);

            $table->unsignedBigInteger('id_reg');
            $table->unsignedBigInteger('id_jefedep');

            $table->unsignedBigInteger('created_by')->nullable(); 
            $table->unsignedBigInteger('updated_by')->nullable(); 
            $table->unsignedBigInteger('deleted_by')->nullable();             
            
            $table->index('id_reg','id_reg');
            $table->index('id_jefedep','id_jefedep');

            $table->timestamps();

            $table->foreign('id_reg')->references('id')->on('region')->onUpdate('cascade')->onDetele('restrict');
            $table->foreign('id_jefedep')->references('id')->on('jefedependencia')->onUpdate('cascade')->onDetele('restrict');

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');

            $table->engine='InnoDB';
            $table->collation='utf8_unicode_ci';                                    
        });

        DB::statement("INSERT INTO dependencia (id, nom_dep, ubicacion, telf_dep, id_reg, id_jefedep,) values (1,'Gerencia Regional', 'Calabozo', '0000-0000000',1, 1);"); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dependencia');
    }
}