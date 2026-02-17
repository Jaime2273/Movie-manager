<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    protected $fillable = [
        'tmdb_id',
        'title',
        'release_date',
        'overview',
        'runtime',
        'poster_path',
    ];
    public function users()
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('status')
                    ->withTimestamps();
    }
    // Una película puede estar en muchas colecciones
public function collections()
{
    return $this->belongsToMany(Collection::class);
}
public function reviews() {
    return $this->hasMany(Review::class);
}
}

