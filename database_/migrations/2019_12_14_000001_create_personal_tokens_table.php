<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class CreatePersonalTokensTable extends Migration
{
    
    public function up()
    {
        DB::statement("ALTER TABLE `users` CHANGE `foto` `foto` LONGBLOB NULL;");

        Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
            $table->engine='InnoDB';
            $table->collation='utf8_unicode_ci';                                    
        });
    }
        
    public function down()
    {
        Schema::dropIfExists('personal_access_tokens');
    }       
}

