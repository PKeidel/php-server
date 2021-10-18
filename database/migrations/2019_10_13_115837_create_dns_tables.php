<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDnsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create('dnszones', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('host');
            $table->ipAddress('ip');
            $table->string('class', 10);
            $table->string('type', 10);
            $table->integer('ttl');
            $table->timestamps();

            $table->index(['host', 'type', 'ip']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dnszones');
    }
}
