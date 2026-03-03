<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Genre;
use App\Models\Review;
use App\Models\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovieController extends Controller
{
    /**
     * Vista de "My Movies" (Lista personal del usuario)
     */
    public function index(Request $request)
    {
        $user = auth()->user();

        // 1. Iniciamos la consulta base de las películas del usuario
        $query = $user->movies()
            ->with([
                'genres',
                'reviews' => function ($q) {
                    $q->where('user_id', auth()->id());
                }
            ])
            ->withAvg([
                'reviews as reviews_avg_rating' => function ($q) {
                    $q->where('is_visible', true);
                }
            ], 'rating');

        // 2. FILTRO POR COLECCIÓN (Esto es lo que te faltaba)
        // Si en la URL viene ?collection=ID, filtramos por esa carpeta
        if ($request->filled('collection')) {
            $query->whereHas('collections', function ($q) use ($request) {
                $q->where('collections.id', $request->collection);
            });
        }

        // 3. Resto de filtros (Búsqueda, Género, etc.)
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('genre')) {
            $query->whereHas('genres', function ($q) use ($request) {
                $q->where('genres.id', $request->genre);
            });
        }
        if ($request->filled('year')) {
            $query->whereYear('release_date', $request->year);
        }
        if ($request->filled('status')) {
            $query->wherePivot('status', $request->status);
        }

        // 4. Ordenación
        $sort = $request->get('sort', 'title');
        switch ($sort) {
            case 'year':
                $query->orderBy('release_date', 'desc');
                break;
            case 'rating':
                $query->orderBy('reviews_avg_rating', 'desc');
                break;
            default:
                $query->orderBy('title', 'asc');
        }

        $myMovies = $query->paginate(12)->withQueryString();
        $genres = Genre::all();
        $collections = $user->collections; // Para los selects de la vista

        return view('movies.index', compact('myMovies', 'genres', 'collections'));
    }

    /**
     * Vista de Catálogo Global (Dashboard / Movies)
     */
    public function dashboard(Request $request)
    {
        $query = Movie::query()
            ->with(['genres'])
            ->with([
                'users' => function ($q) {
                    $q->where('users.id', auth()->id());
                }
            ])
            ->withAvg([
                'reviews as reviews_avg_rating' => function ($q) {
                    $q->where('is_visible', true);
                }
            ], 'rating');

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        if ($request->filled('genre')) {
            $query->whereHas('genres', function ($q) use ($request) {
                $q->where('genres.id', $request->genre);
            });
        }
        if ($request->filled('year')) {
            $query->whereYear('release_date', $request->year);
        }

        if ($request->filled('status')) {
            $query->whereHas('users', function ($q) use ($request) {
                $q->where('users.id', auth()->id())
                    ->where('movie_user.status', $request->status);
            });
        }

        $sort = $request->get('sort', 'title');
        if ($sort == 'rating')
            $query->orderBy('reviews_avg_rating', 'desc');
        elseif ($sort == 'year')
            $query->orderBy('release_date', 'desc');
        else
            $query->orderBy('title', 'asc');

        return view('dashboard', [
            'movies' => $query->paginate(18)->withQueryString(),
            'genres' => Genre::all()
        ]);
    }

    /**
     * Ficha técnica de la película
     */
    public function show(Movie $movie)
    {
        $movie->load([
            'genres',
            'reviews' => function ($q) {
                if (!auth()->user()->can('admin')) {
                    $q->where('is_visible', true);
                }
                $q->with('user')->latest();
            }
        ]);

        $averageRating = $movie->reviews()->where('is_visible', true)->avg('rating');
        $userMovie = auth()->user()->movies()->where('movie_id', $movie->id)->first();
        $collections = auth()->user()->collections;

        $availableCollections = $collections->filter(function ($col) use ($movie) {
            return !$col->movies->contains($movie->id);
        });

        return view('movies.show', [
            'movie' => $movie,
            'averageRating' => $averageRating,
            'pivot' => $userMovie ? $userMovie->pivot : null,
            'reviews' => $movie->reviews,
            'collections' => $collections,
            'availableCollections' => $availableCollections
        ]);
    }

    /**
     * Añadir película a una colección específica evitando duplicados
     */
    public function addToCollection(Request $request, Movie $movie)
    {
        $request->validate([
            'collection_id' => 'required|exists:collections,id'
        ]);

        $collection = auth()->user()->collections()->findOrFail($request->collection_id);

        // Comprobar si ya existe en la colección
        if ($collection->movies()->where('movie_id', $movie->id)->exists()) {
            return back()->with('error', "This movie is already in: {$collection->name}");
        }

        $collection->movies()->attach($movie->id);

        return back()->with('success', "Added to {$collection->name}!");
    }

    /**
     * Añadir película a la lista personal (General)
     */
    public function store(Request $request)
    {
        $request->validate(['movie_id' => 'required|exists:movies,id']);

        auth()->user()->movies()->syncWithoutDetaching([
            $request->movie_id => ['status' => 'pending']
        ]);

        return back()->with('success', 'Movie added to your list!');
    }

    public function updateStatus(Request $request, Movie $movie)
    {
        $status = $request->input('status', 'watched');
        auth()->user()->movies()->updateExistingPivot($movie->id, ['status' => $status]);
        return back()->with('success', 'Status updated!');
    }

    public function destroy(Movie $movie)
    {
        auth()->user()->movies()->detach($movie->id);
        return back()->with('success', 'Movie removed.');
    }

    public function storeReview(Request $request, Movie $movie)
    {
        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:10',
            'comment' => 'nullable|string|max:1000',
        ]);

        auth()->user()->reviews()->updateOrCreate(
            ['movie_id' => $movie->id],
            ['rating' => $validated['rating'], 'comment' => $validated['comment'], 'is_visible' => true]
        );

        return back()->with('success', 'Rating saved!');
    }

    public function toggleReview(Review $review)
    {
        if (!auth()->user()->can('admin'))
            abort(403);
        $review->update(['is_visible' => !$review->is_visible]);
        return back()->with('success', 'Review status updated.');
    }

    public function globalDestroy(Movie $movie)
    {
        if (!auth()->user()->can('admin'))
            abort(403);
        $movie->delete();
        return back()->with('success', 'Movie deleted globally.');
    }
}