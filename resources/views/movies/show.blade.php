<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $movie->title }} ({{ substr($movie->release_date, 0, 4) }})
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-gray-100">
                <div class="p-8 md:flex gap-10">

                    {{-- Columna Izquierda: Póster y Acciones --}}
                    <div class="md:w-1/3 mb-6 md:mb-0">
                        <img src="{{ $movie->poster_path }}" class="w-full rounded-xl shadow-lg border border-gray-200">

                        <div class="mt-6 space-y-4">
                            {{-- BLOQUE: GESTIÓN EN BIBLIOTECA GENERAL --}}
                            @if($pivot)
                                <div class="p-4 rounded-lg border {{ $pivot->status == 'watched' ? 'bg-green-50 border-green-100' : 'bg-yellow-50 border-yellow-100' }}">
                                    <div class="text-center mb-4">
                                        <span class="text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full {{ $pivot->status == 'watched' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                            Status: {{ $pivot->status }}
                                        </span>
                                    </div>

                                    <form action="{{ route('my.movies.status', $movie->id) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="status" value="{{ $pivot->status == 'pending' ? 'watched' : 'pending' }}">
                                        <button type="submit" class="w-full text-xs font-bold py-2 rounded-md transition uppercase {{ $pivot->status == 'pending' ? 'bg-green-600 text-white hover:bg-green-700' : 'bg-yellow-500 text-white hover:bg-yellow-600' }}">
                                            {{ $pivot->status == 'pending' ? 'Mark as Watched' : 'Set as Pending' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('my.movies.destroy', $movie->id) }}" method="POST" class="mt-3" onsubmit="return confirm('Remove from your list?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="w-full text-[10px] text-red-500 font-bold hover:text-red-700 transition uppercase text-center">
                                            Remove from My Movies
                                        </button>
                                    </form>
                                </div>
                            @else
                                <form action="{{ route('my.movies.store') }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="movie_id" value="{{ $movie->id }}">
                                    <input type="hidden" name="title" value="{{ $movie->title }}">
                                    <input type="hidden" name="poster_path" value="{{ $movie->poster_path }}">
                                    <button type="submit" class="w-full bg-green-600 text-white font-bold py-3 rounded-xl hover:bg-green-700 transition shadow-lg shadow-green-100 uppercase tracking-widest text-sm">
                                        + Add to my list
                                    </button>
                                </form>
                            @endif

                            {{-- BLOQUE: AÑADIR A COLECCIONES ESPECÍFICAS --}}
                            <div class="mt-6 pt-6 border-t border-gray-100">
                                <h4 class="text-[10px] font-black text-gray-400 uppercase tracking-widest mb-3 text-center">Add to Collection</h4>
                                
                                @if($availableCollections->count() > 0)
                                    <form action="{{ route('movies.add-to-collection', $movie->id) }}" method="POST" class="space-y-2">
                                        @csrf
                                        <select name="collection_id" required class="w-full text-xs border-gray-200 rounded-lg focus:ring-indigo-500">
                                            <option value="" disabled selected>Select collection...</option>
                                            @foreach($availableCollections as $col)
                                                <option value="{{ $col->id }}">{{ $col->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="w-full bg-indigo-600 text-white text-[10px] font-black py-2 rounded-lg hover:bg-indigo-700 transition uppercase tracking-tighter">
                                            Save to collection
                                        </button>
                                    </form>
                                @else
                                    <div class="bg-gray-50 p-3 rounded-lg border border-gray-100 text-center">
                                        <p class="text-[10px] text-gray-500 font-medium italic">
                                            {{ $collections->count() > 0 ? 'Already in all your collections' : 'No collections created yet' }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Columna Derecha: Información --}}
                    <div class="md:w-2/3">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex items-center bg-amber-50 border border-amber-100 px-4 py-2 rounded-xl">
                                <span class="text-amber-500 text-xl mr-2">⭐</span>
                                <span class="text-amber-700 font-black text-2xl">
                                    {{ $averageRating ? number_format($averageRating, 1) : 'N/A' }}
                                </span>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest leading-none">
                                    Community Average</p>
                            </div>
                        </div>

                        <h3 class="text-4xl font-black text-gray-900 mb-4 leading-tight">{{ $movie->title }}</h3>

                        <div class="flex flex-wrap gap-2 mb-8">
                            @foreach($movie->genres as $genre)
                                <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-[10px] rounded-lg uppercase font-black tracking-wider border border-indigo-100">
                                    {{ $genre->name }}
                                </span>
                            @endforeach
                            <span class="px-3 py-1 bg-gray-50 text-gray-500 text-[10px] rounded-lg uppercase font-black tracking-wider border border-gray-100">
                                ⏱️ {{ $movie->duration ?? '120' }} min
                            </span>
                        </div>

                        <div class="mb-10">
                            <h4 class="font-black text-gray-900 uppercase text-xs tracking-[0.2em] mb-3">Synopsis</h4>
                            <p class="text-gray-600 leading-relaxed">
                                {{ $movie->overview ?? 'No synopsis available for this movie.' }}
                            </p>
                        </div>

                        <hr class="border-gray-100 mb-10">

                        {{-- SECCIÓN RESEÑAS --}}
                        <h4 class="font-black text-gray-900 uppercase text-xs tracking-[0.2em] mb-6">Community Reviews ({{ $reviews->count() }})</h4>

                        <div class="space-y-4">
                            @forelse($reviews as $r)
                                <div class="p-5 rounded-2xl border transition {{ $r->is_visible ? 'bg-gray-50 border-gray-100' : 'bg-red-50 border-red-200 opacity-80' }}">
                                    <div class="flex justify-between items-start mb-3">
                                        <div class="flex items-center gap-3">
                                            @if($r->user->profile_photo)
                                                <img src="{{ asset(Storage::url($r->user->profile_photo)) }}" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm">
                                            @else
                                                <div class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-600 font-bold text-xs uppercase border-2 border-white shadow-sm">
                                                    {{ substr($r->user->name, 0, 1) }}
                                                </div>
                                            @endif

                                            <div class="text-left">
                                                <span class="font-bold text-sm text-gray-900 block leading-none">
                                                    {{ $r->user->name }}
                                                    @if(!$r->is_visible) <span class="text-[8px] bg-red-500 text-white px-1 rounded ml-1">HIDDEN</span> @endif
                                                </span>
                                                <span class="text-amber-500 text-[10px] font-bold">{{ $r->rating }} ⭐</span>
                                            </div>
                                        </div>

                                        @can('admin')
                                            <form action="{{ route('reviews.toggle', $r->id) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="text-[9px] font-black uppercase transition {{ $r->is_visible ? 'text-red-400 hover:text-red-600' : 'text-green-500 hover:text-green-700' }}">
                                                    {{ $r->is_visible ? 'Hide Review' : 'Show Review' }}
                                                </button>
                                            </form>
                                        @endcan
                                    </div>

                                    <p class="text-sm text-gray-600 italic bg-white p-3 rounded-lg border border-gray-50 text-left">
                                        "{{ $r->comment }}"
                                    </p>
                                </div>
                            @empty
                                <div class="text-left py-4 text-gray-400 italic text-sm">No reviews yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>