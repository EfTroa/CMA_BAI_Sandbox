@auth
    @if(is_null(auth()->user()->cookies_consent))
        {{-- Overlay --}}
        <div class="fixed inset-0 bg-black bg-opacity-50 z-40"></div>

        {{-- Modal centré --}}
        <div class="fixed inset-0 flex items-center justify-center z-50 p-4">
            <div class="bg-gray-800 text-white p-6 rounded-lg shadow-2xl max-w-2xl w-full">
                <h2 class="text-xl font-bold mb-4">Consentement aux cookies</h2>
                <p class="mb-6">
                    Ce site utilise des cookies pour améliorer votre expérience.
                    Veuillez accepter ou refuser l'utilisation de cookies pour continuer.
                </p>

                <div class="flex gap-2 justify-end">
                    <form method="POST" action="{{ route('cookies.deny') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 rounded">
                            Refuser
                        </button>
                    </form>

                    <form method="POST" action="{{ route('cookies.accept') }}" class="inline">
                        @csrf
                        <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 rounded">
                            Accepter
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endauth