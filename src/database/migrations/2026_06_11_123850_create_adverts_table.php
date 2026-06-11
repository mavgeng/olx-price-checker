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
        Schema::create('adverts', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->string('external_id');
            $table->string('url');
            $table->string('title')->nullable()->default(null);
            $table->unsignedBigInteger('last_price')->nullable()->default(null);
            $table->string('currency')->default('UAH');
            $table->timestamp('last_checked_at')->nullable()->default(null);
            $table->timestamps();

            $table->index(['external_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('adverts');
    }
};
