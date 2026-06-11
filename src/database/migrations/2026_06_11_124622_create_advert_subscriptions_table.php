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
        Schema::create('advert_subscriptions', function (Blueprint $table) {
            $table->ulid('id')->primary();
            $table->ulid('subscriber_id');
            $table->ulid('advert_id');
            $table->timestamps();

            $table->foreign('subscriber_id')->references('id')->on('subscribers')->onDelete('cascade');
            $table->foreign('advert_id')->references('id')->on('adverts')->onDelete('cascade');

            $table->unique(['subscriber_id', 'advert_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('advert_subscriptions');
    }
};
