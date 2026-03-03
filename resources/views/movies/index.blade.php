<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Your Movies') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- SECCIÓN DE FILTROS --}}
            <div class="bg-white p-6 rounded-lg shadow-sm mb-6 border border-gray-100">
                <form action="{{ route('my.movies') }}" method="GET" class="space-y-4">
                    <div class="flex flex-wrap gap-4">
                        {{-- Búsqueda por Título --}}
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Buscar por título..."
                            class="rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 flex-1 min-w-[200px]">

                        {{-- Filtro por Género --}}
                        <select name="genre" class="rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 text-sm">
                            <option value="">All Genres</option>
                            @foreach($genres as $genre)
                                <option value="{{ $genre->id }}" {{ request('genre') == $genre->id ? 'selected' : '' }}>
                                    {{ $genre->name }}
                                </option>
                            @endforeach
                        </select>

                        {{-- Filtro por Año --}}
                        <select name="year" class="rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 text-sm">
                            <option value="">Year</option>
                            @for ($i = date('Y'); $i >= 1900; $i--)
                                <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                            @endfor
                        </select>

                        {{-- Filtro por Estado --}}
                        <select name="status"
                            class="rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 text-sm">
                            <option value="">Status</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending
                            </option>
                            <option value="watched" {{ request('status') == 'watched' ? 'selected' : '' }}>Watched
                            </option>
                        </select>

                        {{-- Ordenación --}}
                        <select name="sort" class="rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 text-sm">
                            <option value="title" {{ request('sort') == 'title' ? 'selected' : '' }}>Order by: Title
                            </option>
                            <option value="year" {{ request('sort') == 'year' ? 'selected' : '' }}>Order by: Year</option>
                            <option value="rating" {{ request('sort') == 'rating' ? 'selected' : '' }}>Order by: Rating
                            </option>
                        </select>

                        <x-primary-button>Apply Filters</x-primary-button>
                        <a href="{{ route('my.movies') }}"
                            class="text-xs text-red-500 self-center hover:underline">Clear</a>
                    </div>
                </form>
            </div>

            {{-- LISTADO DE PELÍCULAS --}}
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                @forelse($myMovies as $movie)
                    <div
                        class="bg-white p-3 rounded-lg shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-md transition duration-300">

                        <div>
                            {{-- ENLACE A LA FICHA TÉCNICA (Clickable) --}}
                            <a href="{{ route('movies.show', $movie->id) }}" class="block group">
                                <div class="relative overflow-hidden rounded mb-2">
                                    <img src="{{ $movie->poster_path }}"
                                        class="w-full h-auto transform group-hover:scale-105 transition duration-500">
                                    <div
                                        class="absolute inset-0 bg-black opacity-0 group-hover:opacity-10 transition duration-300">
                                    </div>
                                </div>
                                <h4 class="font-bold text-xs truncate group-hover:text-indigo-600 transition"
                                    title="{{ $movie->title }}">
                                    {{ $movie->title }}
                                </h4>
                            </a>

                            <span
                                class="text-[9px] mt-2 px-2 py-0.5 rounded-full inline-block w-fit {{ $movie->pivot->status == 'watched' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ strtoupper($movie->pivot->status) }}
                            </span>
                        </div>

                        {{-- SECCIÓN DE RESEÑA PERSONAL --}}
                        <div class="mt-4 pt-3 border-t border-gray-50">
                            @php
                                // Cargamos la reseña del usuario para esta película específica
                                $review = $movie->reviews->first(); 
                            @endphp

                            <form action="{{ route('movies.review.store', $movie->id) }}" method="POST" class="space-y-2">
                                @csrf
                                <div>
                                    <select name="rating"
                                        class="text-[10px] p-1 border-gray-200 rounded w-full focus:ring-indigo-500">
                                        <option value="">Rating...</option>
                                        @for ($i = 10; $i >= 1; $i--)
                                            <option value="{{ $i }}" {{ ($review && $review->rating == $i) ? 'selected' : '' }}>
                                                {{ $i }} ⭐
                                            </option>
                                        @endfor
                                    </select>
                                </div>

                                <textarea name="comment" rows="2" placeholder="Your opinion..."
                                    class="w-full text-[10px] border-gray-200 rounded focus:ring-indigo-500 resize-none">{{ $review->comment ?? '' }}</textarea>

                                <button type="submit"
                                    class="w-full text-[9px] bg-indigo-600 text-white py-1 rounded hover:bg-indigo-700 transition font-bold uppercase">
                                    {{ $review ? 'Update' : 'Rate' }}
                                </button>
                            </form>
                        </div>

                        {{-- BOTONES DE ACCIÓN (Estado y Eliminar) --}}
                        <div class="mt-4 space-y-2">
                            @if($movie->pivot->status === 'pending')
                                <form action="{{ route('my.movies.status', $movie->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="watched">
                                    <button type="submit"
                                        class="w-full text-[10px] bg-green-50 text-green-700 border border-green-200 py-1 rounded hover:bg-green-100 transition font-semibold">
                                        MARK AS VIEW
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('my.movies.status', $movie->id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="status" value="pending">
                                    <button type="submit"
                                        class="w-full text-[10px] bg-yellow-50 text-yellow-700 border border-yellow-200 py-1 rounded hover:bg-yellow-100 transition font-semibold">
                                        UNMARK AS VIEW
                                    </button>
                                </form>
                            @endif

                            <form action="{{ route('my.movies.destroy', $movie->id) }}" method="POST"
                                onsubmit="return confirm('¿Seguro que quieres quitar esta película?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-full text-[10px] text-red-400 hover:text-red-600 font-bold py-1 transition rounded">
                                    DELETE
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full py-12 text-center">
                        <p class="text-gray-500">No movies found in your list.</p>
                    </div>
                @endforelse
            </div>

            {{-- PAGINACIÓN --}}
            <div class="mt-8">
                {{ $myMovies->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</x-app-layout>