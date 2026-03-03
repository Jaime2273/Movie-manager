<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use Illuminate\Http\Request;

class CollectionController extends Controller
{
   public function index()
    {
        // 1. Mis propias colecciones
        $myCollections = auth()->user()->collections()
            ->with('movies') 
            ->withCount('movies')
            ->get();

        // 2. Colecciones públicas de OTROS usuarios
        $otherCollections = Collection::with(['movies', 'user'])
            ->withCount('movies')
            ->where('is_public', true)
            ->where('user_id', '!=', auth()->id())
            ->get();

        return view('movies.collections', compact('myCollections', 'otherCollections'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        auth()->user()->collections()->create([
            'name' => $request->name,
            'description' => $request->description,
            'is_public' => $request->has('is_public'),
        ]);

        return back()->with('success', 'Collection created!');
    }

    public function togglePrivacy(Request $request, Collection $collection)
    {
        if ($collection->user_id !== auth()->id()) abort(403);

        $collection->update(['is_public' => $request->has('is_public')]);

        $status = $collection->is_public ? 'public' : 'private';
        return back()->with('success', "Collection is now {$status}.");
    }

    public function destroy(Collection $collection)
    {
        if ($collection->user_id !== auth()->id()) abort(403);

        $collection->delete();
        return back()->with('success', 'Collection deleted.');
    }

    public function removeMovie(Collection $collection, $movieId)
    {
        if ($collection->user_id !== auth()->id()) abort(403);

        $collection->movies()->detach($movieId);
        return back()->with('success', 'Movie removed from collection.');
    }
}