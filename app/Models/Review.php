<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = [
        'user_id',
        'movie_id',
        'rating',
        'content',
        'is_visible',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // La reseña pertenece a una película
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}

