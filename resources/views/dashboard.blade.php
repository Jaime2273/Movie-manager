<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Movies') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- FILTROS PARA EL DASHBOARD GLOBAL --}}
            <div class="bg-white p-6 rounded-lg shadow-sm mb-6 border border-gray-100">
                
                <form action="{{ route('dashboard') }}" method="GET" class="space-y-4">
                    <div class="flex flex-wrap gap-4">
                        {{-- Búsqueda por Título --}}
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Buscar en el catálogo..."
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

                        {{-- Filtro por Estado (Personal) --}}
                        
                        <select name="status"
                            class="rounded-md border-gray-300 shadow-sm focus:ring-indigo-500 text-sm">
                            <option value="">My Status</option>
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

                
                        <a href="{{ route('dashboard') }}"
                            class="text-xs text-red-500 self-center hover:underline">Clear</a>
                    </div>
                </form>
            </div>

            {{-- GRID DE PELÍCULAS --}}
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6">
                @forelse($movies as $movie)
                    <div class="flex flex-col gap-2">
                        {{-- La tarjeta de la película --}}
                        <a href="{{ route('movies.show', $movie->id) }}"
                            class="group bg-white p-3 rounded-lg shadow-sm border border-gray-100 flex flex-col justify-between hover:shadow-xl transition duration-300 relative h-full">
                            <div>
                                <div class="relative overflow-hidden rounded mb-2">
                                    <img src="{{ $movie->poster_path }}"
                                        class="w-full h-auto transform group-hover:scale-105 transition duration-500">
                                    <div
                                        class="absolute top-2 right-2 bg-black/70 text-white text-[10px] px-2 py-1 rounded backdrop-blur-sm font-bold border border-white/10">
                                        ⭐
                                        {{ $movie->reviews_avg_rating ? number_format($movie->reviews_avg_rating, 1) : 'N/A' }}
                                    </div>
                                </div>

                                <h4 class="font-bold text-xs truncate group-hover:text-indigo-600 transition"
                                    title="{{ $movie->title }}">
                                    {{ $movie->title }}
                                </h4>
                                <div class="mt-2">
                                    @if($movie->users->first()?->pivot->status == 'watched')
                                        <span
                                            class="text-[8px] px-2 py-0.5 rounded-full font-black uppercase tracking-tighter bg-green-100 text-green-700 border border-green-200">Watched</span>
                                    @elseif($movie->users->first()?->pivot->status == 'pending')
                                        <span
                                            class="text-[8px] px-2 py-0.5 rounded-full font-black uppercase tracking-tighter bg-yellow-100 text-yellow-700 border border-yellow-200">Pending</span>
                                    @else
                                        <span
                                            class="text-[8px] px-2 py-0.5 rounded-full font-black uppercase tracking-tighter bg-gray-100 text-gray-400 border border-gray-200">Not
                                            in list</span>
                                    @endif
                                </div>
                            </div>
                            <div class="mt-3 text-[10px] text-gray-400 font-bold uppercase tracking-tighter">
                                {{ substr($movie->release_date, 0, 4) }}
                            </div>
                        </a>

                        {{-- BOTÓN ELIMINAR GLOBAL (Solo Admin) --}}
                        @can('admin')
                            <form action="{{ route('movies.global.destroy', $movie->id) }}" method="POST"
                                onsubmit="return confirm('Delete this movie ?')">
                                @csrf @method('DELETE')
                                <button type="submit"
                                    class="w-full text-[9px] bg-red-600 text-white py-1.5 rounded-md hover:bg-red-700 transition font-black uppercase tracking-widest shadow-sm">
                                    Delete Film
                                </button>
                            </form>
                        @endcan
                    </div>
                @empty
                    <div class="col-span-full py-20 text-center">
                        <p class="text-gray-500 italic uppercase tracking-widest text-xs font-bold">No movies available</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-8">
                {{ $movies->links() }}
            </div>
        </div>
    </div>
</x-app-layout>