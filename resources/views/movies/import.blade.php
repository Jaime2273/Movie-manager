<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Search Global Movies') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            
            {{-- Formulario de Búsqueda --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <form action="{{ route('movies.import.search') }}" method="POST" class="flex flex-col sm:flex-row gap-4">
                    @csrf
                    <input type="text" name="query" 
                           placeholder="Type a movie title (e.g., Inception, Avatar...)" 
                           class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           required>
                    <x-primary-button class="justify-center">
                        {{ __('Search TMDB') }}
                    </x-primary-button>
                </form>
            </div>

            {{-- Resultados de la Búsqueda --}}
            @if(isset($results))
                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
                    @foreach($results as $movie)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-4 flex flex-col justify-between border border-gray-100">
                            <div>
                                {{-- Manejo de imagen (por si TMDB no tiene póster) --}}
                                @if($movie['poster_path'])
                                    <img src="https://image.tmdb.org/t/p/w342{{ $movie['poster_path'] }}" 
                                         alt="{{ $movie['title'] }}" 
                                         class="rounded-md w-full mb-3 shadow-sm">
                                @else
                                    <div class="bg-gray-200 h-64 flex items-center justify-center rounded-md mb-3 text-gray-400 text-xs text-center p-2">
                                        No Image Available
                                    </div>
                                @endif

                                <h4 class="font-bold text-sm text-gray-900 leading-tight mb-1">{{ $movie['title'] }}</h4>
                                <p class="text-xs text-gray-500 mb-4">
                                    {{ isset($movie['release_date']) ? substr($movie['release_date'], 0, 4) : 'N/A' }}
                                </p>
                            </div>

                            {{-- Botón para Importar y Añadir a "Your Movies" --}}
                            <form action="{{ route('movies.import.store') }}" method="POST">
                                @csrf
                                <input type="hidden" name="tmdb_id" value="{{ $movie['id'] }}">
                                <button type="submit" 
                                        class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-[10px] text-white uppercase tracking-widest hover:bg-indigo-700 transition">
                                    + Add to My List
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</x-app-layout>