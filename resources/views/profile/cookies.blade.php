@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-2xl font-bold mb-6">Gestion des cookies</h1>

    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Préférences des cookies</h2>

        <p class="text-gray-600 mb-6">
            @if($user->cookies_consent === true)
                Vous avez <strong>accepté</strong> les cookies le {{ $user->cookies_consent_at->format('d/m/Y à H:i') }}.
            @elseif($user->cookies_consent === false)
                Vous avez <strong>refusé</strong> les cookies le {{ $user->cookies_consent_at->format('d/m/Y à H:i') }}.
            @else
                Vous n'avez pas encore fait de choix concernant les cookies.
            @endif
        </p>

        <div class="flex gap-4">
            <form method="POST" action="{{ route('cookies.accept') }}">
                @csrf
                <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded">
                    Accepter les cookies
                </button>
            </form>

            <form method="POST" action="{{ route('cookies.deny') }}">
                @csrf
                <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded">
                    Refuser les cookies
                </button>
            </form>
        </div>

        @if(session('status'))
            <p class="mt-4 text-sm text-green-600">{{ session('status') }}</p>
        @endif
    </div>
</div>
@endsection