<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Panel Control') }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                   @can('admin')
    <ul>
        @foreach($users as $user)
            <li>Nombre:{{ $user->name }} | Username:{{ $user->username }} | Email: {{ $user->email }} | Role: {{$user->role}} | Profile Photo: <img src="{{ asset(Storage::url($user->profile_photo)) }}" alt="Foto de {{ $user->name }}" width="150"></li>
        @endforeach
    </ul>
        @endcan
        @cannot('admin')
        <h2>No eres admin</h2>
        @endcannot
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
