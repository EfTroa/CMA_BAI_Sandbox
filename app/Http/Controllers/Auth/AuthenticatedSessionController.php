<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Services\Logging\ActionLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function __construct(
        private readonly ActionLogService $actionLog
    ) {}

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            $request->authenticate();
        } catch (ValidationException $e) {
            $this->actionLog->log(
                userId: null,
                action: 'login',
                result: 'failure',
                dataAfter: json_encode(['email' => $request->input('email')]),
                request: $request,
            );

            throw $e; // Laravel gère le redirect back avec les erreurs
        }

        $this->actionLog->log(
            userId: Auth::id(),
            action: 'login',
            result: 'success',
            request: $request,
        );

        $request->session()->regenerate();

        return redirect()->intended(route('ideas.index'));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        Auth::guard('web')->logout();

        $this->actionLog->log(
            userId: $userId,
            action: 'logout',
            result: 'success',
            request: $request,
        );

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
