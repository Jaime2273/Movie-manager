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
    Schema::create('reviews', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->foreignId('movie_id')->constrained()->onDelete('cascade');
        
        $table->integer('rating'); 
        $table->text('comment')->nullable(); 
        
        // Moderación: El administrador puede ocultar/mostrar reseñas
        $table->boolean('is_visible')->default(true); 
        $table->timestamps();
        //Una valoración por usuario y película
        $table->unique(['user_id', 'movie_id']);
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
