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
        Schema::create('devices', static function (Blueprint $table) {
            $table->uuid('device_id')->primary();
            $table->integer('server_id');
            $table->string('name', 255);
            $table->text('private_key');
            $table->string('public_key', 44);
            $table->text('preshared_key');
            $table->string('address', 15);
            $table->smallInteger('keep_alive')->default(0);
            $table->integer('mtu')->default(1420);
            $table->jsonb('dns')->default('["8.8.8.8","8.8.4.4"]');
            $table->string('is_active', 15)->default(true);
            $table->timestampsTz();

            $table->index('public_key');
            $table->foreign('server_id')->references('server_id')->on('servers');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('devices');
    }
};
