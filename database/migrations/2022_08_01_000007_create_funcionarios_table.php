<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateFuncionariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('funcionarios', function (Blueprint $table) {
            $table->bigIncrements('id')->unsigned();
            $table->string('cedula',10)->unique('cedula');
            $table->string('rif',12)->unique('rif');            
            $table->string('nombre',190);
            $table->string('apellido',190);            
            $table->date('fecha_in');
            $table->date('fecha_nac');
            $table->string('correo_i',190);
            $table->string('correo_alt',190);
            $table->string('twitter',190);
            $table->string('telefono_p',15);
            $table->string('telefono_cont',15);
            $table->string('sexo',1);
            $table->string('alergia',190)->default('');
            $table->string('direccion',190);
            $table->string('tipo_s',30)->default('');
            $table->string('condicion_in',30);
            $table->smallInteger('ancho')->default('0');
            $table->smallInteger('alto')->default('0');;;
            $table->char('tipoimagen',15)->default('');                                 
            $table->binary('foto')->nullable();
            $table->unsignedBigInteger('created_by')->nullable(); 
            $table->unsignedBigInteger('updated_by')->nullable(); 
            $table->unsignedBigInteger('deleted_by')->nullable(); 
            $table->unsignedBigInteger('id_dep')->nullable();             
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users');
            $table->foreign('updated_by')->references('id')->on('users');
            $table->foreign('deleted_by')->references('id')->on('users');
            
            $table->engine='InnoDB';
            $table->collation='utf8_unicode_ci';                                    
        });
        DB::statement("ALTER TABLE `funcionarios` CHANGE `foto` `foto` LONGBLOB NULL;");        
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('funcionarios');
    }
}
