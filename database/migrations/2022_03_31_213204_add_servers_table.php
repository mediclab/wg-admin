<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('servers', static function (Blueprint $table) {
            $table->id('server_id');
            $table->integer('user_id');
            $table->text('private_key');
            $table->string('public_key', 44);
            $table->string('address', 15);
            $table->integer('port');
            $table->timestampsTz();

            $table->unique(['address']);
            $table->unique(['port']);
            $table->foreign('user_id')->references('user_id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('servers');
    }
};
