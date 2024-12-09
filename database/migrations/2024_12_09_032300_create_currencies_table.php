<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id');
            $table->string('name');
            $table->string('symbol');
            $table->decimal('price', 16, 8)->nullable();
            $table->decimal('rise_alert', 5, 2)->default(0);
            $table->integer('rise_alert_interval')->default(15);

            $table->decimal('fall_alert', 5, 2)->default(0);
            $table->integer('fall_alert_interval')->default(15);

            $table->boolean('is_active')->default(false);

            $table->decimal('m1', 16, 8)->nullable();
            $table->decimal('m5', 16, 8)->nullable();
            $table->decimal('m15', 16, 8)->nullable();
            $table->decimal('m30', 16, 8)->nullable();
            $table->decimal('h1', 16, 8)->nullable();
            $table->decimal('h4', 16, 8)->nullable();
            $table->decimal('h12', 16, 8)->nullable();
            $table->decimal('d1', 16, 8)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('currencies');
    }
};
