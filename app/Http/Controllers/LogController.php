<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class LogController extends Controller
{
    public function index()
    {
        $logFile = storage_path('logs/laravel.log');

        $content = File::exists($logFile) ? File::get($logFile) : 'Aucun log disponible.';

        return view('logs.index', compact('content'));
    }
}
