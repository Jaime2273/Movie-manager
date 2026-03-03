<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row justify-between items-center gap-4">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Collections') }}</h2>

            <form action="{{ route('collections.store') }}" method="POST" class="flex items-center gap-2 bg-white p-2 rounded-xl border border-gray-200 shadow-sm">
                @csrf
                <input type="text" name="name" placeholder="New Collection Name..." required class="text-[10px] rounded-lg border-gray-300 py-1 px-3 w-48">
                <button type="submit" class="bg-indigo-600 text-white px-4 py-1.5 rounded-lg text-[10px] font-black uppercase hover:bg-indigo-700 transition">+ Create Folder</button>
            </form>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-4">
            
            {{-- SECCIÓN: MIS COLECCIONES --}}
            @forelse($myCollections as $col)
                <div x-data="{ open: false }" class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 cursor-pointer hover:bg-gray-50 flex flex-col md:flex-row justify-between items-center gap-4" @click="open = !open">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-2xl shadow-sm">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h5l2 2h5a2 2 0 012 2v6a2 2 0 01-2 2H4a2 2 0 01-2-2V6z"></path></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-gray-900 uppercase tracking-tighter">{{ $col->name }}</h3>
                                <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $col->movies_count }} Movies</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-6">
                            <div class="flex items-center gap-3" @click.stop>
                                <form action="{{ route('collections.toggle', $col->id) }}" method="POST">
                                    @csrf
                                    @if(!$col->is_public) <input type="hidden" name="is_public" value="1"> @endif
                                    <button type="submit" class="text-[9px] font-black px-4 py-1.5 rounded-full uppercase tracking-widest transition border {{ $col->is_public ? 'bg-green-600 text-white border-green-700' : 'bg-white text-gray-400 border-gray-200' }}">
                                        {{ $col->is_public ? '● Public' : '○ Private' }}
                                    </button>
                                </form>

                                <form action="{{ route('collections.destroy', $col->id) }}" method="POST" onsubmit="return confirm('Delete folder?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="p-2 text-red-400 hover:text-red-600 transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                </form>
                            </div>
                            <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    <div x-show="open" class="p-6 bg-white border-t border-gray-50">
                        @if($col->movies->count() > 0)
                            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                @foreach($col->movies as $movie)
                                    <div class="group relative">
                                        <form action="{{ route('collections.remove-movie', [$col->id, $movie->id]) }}" method="POST" class="absolute -top-2 -right-2 z-10 opacity-0 group-hover:opacity-100 transition-opacity" onsubmit="return confirm('Remove?')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="bg-red-500 text-white p-1 rounded-full shadow-lg hover:bg-red-600">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </form>
                                        <a href="{{ route('movies.show', $movie->id) }}">
                                            <img src="{{ $movie->poster_path }}" class="w-full h-auto rounded-xl shadow-sm border border-transparent group-hover:border-indigo-200 transition">
                                        </a>
                                        <h4 class="text-[9px] font-bold uppercase mt-2 text-gray-700 truncate">{{ $movie->title }}</h4>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p class="text-[10px] text-gray-400 italic text-center py-4 uppercase font-bold tracking-widest">No movies in this folder</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-20 text-center bg-white rounded-3xl border-2 border-dashed border-gray-100">
                    <p class="text-gray-400 font-black uppercase text-sm tracking-widest">No collections created yet.</p>
                </div>
            @endforelse

            {{-- SECCIÓN: COMUNIDAD --}}
            @if($otherCollections->count() > 0)
                <div class="mt-12 pt-12 border-t border-gray-100">
                    <h3 class="text-lg font-black text-gray-900 uppercase tracking-widest mb-6">Community Collections</h3>
                    @foreach($otherCollections as $col)
                        <div x-data="{ open: false }" class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-4 opacity-90 hover:opacity-100">
                            <div class="p-6 cursor-pointer hover:bg-gray-50 flex justify-between items-center" @click="open = !open">
                                <div class="flex items-center gap-4">
                                    <div class="p-3 bg-teal-50 text-teal-600 rounded-2xl">
                                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path></svg>
                                    </div>
                                    <div>
                                        <h3 class="text-lg font-black text-gray-900 uppercase tracking-tighter">{{ $col->name }} <span class="text-[9px] bg-teal-100 text-teal-700 px-2 py-0.5 rounded-full ml-2">BY {{ $col->user->name }}</span></h3>
                                        <p class="text-[10px] text-gray-400 font-bold uppercase">{{ $col->movies_count }} Movies</p>
                                    </div>
                                </div>
                                <svg class="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                            <div x-show="open" class="p-6 bg-white border-t border-gray-50">
                                <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
                                    @foreach($col->movies as $movie)
                                        <div class="group">
                                            <a href="{{ route('movies.show', $movie->id) }}">
                                                <img src="{{ $movie->poster_path }}" class="w-full h-auto rounded-xl shadow-sm border border-transparent group-hover:border-teal-200 transition">
                                            </a>
                                            <h4 class="text-[9px] font-bold uppercase mt-2 text-gray-700 truncate">{{ $movie->title }}</h4>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>