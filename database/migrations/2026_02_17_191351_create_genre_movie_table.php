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
    Schema::create('genre_movie', function (Blueprint $table) {
        $table->id();
        // foreignId('genre_id') conecta con la tabla 'genres'
        $table->foreignId('genre_id')->constrained()->onDelete('cascade');
        // foreignId('movie_id') conecta con la tabla 'movies'
        $table->foreignId('movie_id')->constrained()->onDelete('cascade');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('genre_movie');
    }
};
