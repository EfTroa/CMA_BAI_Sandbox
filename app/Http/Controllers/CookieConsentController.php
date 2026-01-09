<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CookieConsentController extends Controller
{
    // Met à jour cookies_consent = true + cookies_consent_at = now()
    public function accept(Request $request) {
        $user = $request->user();
        $user->update([
            'cookies_consent' => true,
            'cookies_consent_at' => now(),
        ]);

        return redirect()->back()->with('status', 'Cookies acceptés');
    }

    // Met à jour cookies_consent = false + cookies_consent_at = now()
    public function deny(Request $request) {
        $user = $request->user();
        $user->update([
            'cookies_consent' => false,
            'cookies_consent_at' => now(),
        ]);

        return redirect()->back()->with('status', 'Cookies refusés');
    }
}
