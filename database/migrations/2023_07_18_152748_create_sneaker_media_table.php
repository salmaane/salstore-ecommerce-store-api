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
        Schema::create('sneaker_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sneaker_id');
            $table->string('imageUrl');
            $table->string('smallImageUrl');
            $table->string('thumbUrl');
            $table->foreign('sneaker_id')->references('id')->on('sneakers')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sneaker_media');
    }
};
