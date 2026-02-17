<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Collection extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'description',
        'is_public',
    ];
    // Una colección pertenece a un usuario
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Una colección tiene muchas películas
    public function movies()
    {
        return $this->belongsToMany(Movie::class);
    }
}

