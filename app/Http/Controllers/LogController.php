<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class LogController extends Controller
{
    public function index()
    {
        $filter = request('action');
        $date   = request('date');

        $actions = \App\Models\ActionLog::distinct()->pluck('action')->sort()->values();

        $logs = \App\Models\ActionLog::with('user')
            ->when($filter, fn($query) => $query->where('action', $filter))
            ->when($date,   fn($query) => $query->whereDate('created_at', $date))
            ->latest()
            ->get();

        return view('logs.index', compact('logs', 'actions', 'filter', 'date'));
    }
}
