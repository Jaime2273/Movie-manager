<?php
namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Genre;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class MovieImportController extends Controller
{
    private $baseUrl = 'https://api.themoviedb.org/3';

    // Función privada para no repetir código HTTP
    private function fetchFromApi(string $endpoint, array $params = [])
    {
        $params['language'] = 'es-ES';
        return Http::withToken(env('API_TOKEN'))->get("{$this->baseUrl}/{$endpoint}", $params);
    }

    public function index()
    {
        return view('movies.import');
    }

    public function search(Request $request)
    {
        $response = $this->fetchFromApi('search/movie', ['query' => $request->input('query')]);

        if ($response->failed()) {
            return back()->with('error', 'Error al conectar con la API de películas.');
        }

        $results = $response->json()['results'] ?? [];
        return view('movies.import', compact('results'));
    }

    public function store(Request $request)
    {
        $tmdbId = $request->input('tmdb_id');
        $user = Auth::user();

        $movie = Movie::where('tmdb_id', $tmdbId)->first();

        if (!$movie) {
            $response = $this->fetchFromApi("movie/{$tmdbId}");

            if ($response->failed()) {
                return back()->with('error', 'No se pudo obtener la información de la película.');
            }
            $data = $response->json();

            $movie = Movie::create([
                'tmdb_id' => $data['id'],
                'title' => $data['title'],
                'release_date' => $data['release_date'] ?? null,
                'overview' => $data['overview'] ?? '',
                'runtime' => $data['runtime'] ?? 0,
                'poster_path' => $data['poster_path'] ? "https://image.tmdb.org/t/p/w500{$data['poster_path']}" : null,
            ]);

            if (!empty($data['genres'])) {
                foreach ($data['genres'] as $genreData) {
                    $genre = Genre::firstOrCreate(
                        ['tmdb_id' => $genreData['id']],
                        ['name' => $genreData['name']]
                    );
                    $movie->genres()->attach($genre->id);
                }
            }
        }

        // Simplificado: usamos syncWithoutDetaching igual que en MovieController
        $changes = $user->movies()->syncWithoutDetaching([$movie->id => ['status' => 'pending']]);

        $mensaje = empty($changes['attached']) 
            ? 'Esta película ya estaba en tu lista.' 
            : '¡Película añadida a "Your Movies"!';

        return redirect()->route('my.movies')->with('success', $mensaje);
    }
}