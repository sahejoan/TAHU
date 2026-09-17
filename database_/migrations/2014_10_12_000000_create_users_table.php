<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('cedula',10);
            $table->string('name')->default('webmaster');
            $table->string('email')->unique()->default('webmaster@gmail.com');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->default(bcrypt('12345678'));
            $table->timestamp('estatus')->default('ACTIVO');
            $table->unsignedBigInteger('id_dep');
            //$table->string('password')->default(\Hash::make('12345678'));
            $table->string('idsesion',255)->default('');
            $table->smallInteger('ancho')->default('0');
            $table->smallInteger('alto')->default('0');
            $table->char('tipoimagen',15)->default('');                                 
            $table->binary('foto')->nullable();     
            $table->rememberToken();
            $table->index('id_dep','id_dep');

            $table->timestamps();

            $table->foreign('id_dep')->references('id')->on('dependencia')->onUpdate('cascade')->onDetele('restrict');

            $table->engine='InnoDB';
            $table->collation='utf8_unicode_ci';                        

            //Crear campo indice
            //$table->index('idcampo','idcampo');

            //Relacionar campo indice con campo llave de la tabla primaria con actualizacion en cascada y eliminación restringida de registos relacionados
            //$table->foreign('idcampo')->references('idllave')->on('tablaprimaria')->onUpdate('cascade')->onDetele('restrict');            

        });

        DB::statement("ALTER TABLE `users` CHANGE `foto` `foto` LONGBLOB NULL;");
        DB::statement("INSERT INTO users (id, id_dep) values (1,1);"); 
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
