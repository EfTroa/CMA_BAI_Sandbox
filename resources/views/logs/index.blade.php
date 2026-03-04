@extends('layouts.app')

@section('content')

    <div class="max-w-5xl mx-auto space-y-6">

        <h1 class="text-2xl font-bold">Logs</h1>

        {{-- Formulaire de filtre --}}
        <form method="GET" action="{{ route('logs.index') }}" class="flex gap-2 items-center">
            <select name="action" class="border rounded px-3 py-1 text-sm">
                <option value="">— Toutes les actions —</option>
                @foreach($actions as $action)
                    <option value="{{ $action }}" @selected($filter === $action)>
                        {{ $action }}
                    </option>
                @endforeach
            </select>
            <input type="date" name="date" value="{{ $date }}"
                   class="border rounded px-3 py-1 text-sm">
            <button type="submit" class="px-3 py-1 bg-gray-800 text-white text-sm rounded">Filtrer</button>
            @if($filter || $date)
                <a href="{{ route('logs.index') }}" class="px-3 py-1 text-sm text-gray-500 underline">Effacer</a>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead class="bg-gray-100 text-left">
                    <tr>
                        <th class="p-2 border">Date</th>
                        <th class="p-2 border">Action</th>
                        <th class="p-2 border">User</th>
                        <th class="p-2 border">IP</th>
                        <th class="p-2 border">Idea</th>
                        <th class="p-2 border">Comment</th>
                        <th class="p-2 border">Données</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                            <tr class="border-b hover:bg-gray-50">
                                <td class="p-2 border whitespace-nowrap">{{ $log->created_at }}</td>
                                <td class="p-2 border font-mono">{{ $log->action }}</td>
                                <td class="p-2 border">{{ $log->user?->name ?? 'Guest' }}</td>
                                <td class="p-2 border font-mono">{{ $log->ip_address }}</td>
                                <td class="p-2 border">{{ $log->idea_id ?? '—' }}</td>
                                <td class="p-2 border">{{ $log->comment_id ?? '—' }}</td>
                                <td class="p-2 border">
                                    @if($log->data_before || $log->data_after)
                                        <details>
                                            <summary class="cursor-pointer text-blue-600">Voir</summary>
                                            <pre class="text-xs mt-1">Avant: {{ $log->data_before }}
            Après: {{ $log->data_after }}</pre>
                                        </details>
                                    @else
                                        —
                                    @endif
                                </td>
                            </tr>
                    @empty
                        <tr><td colspan="7" class="p-4 text-center text-gray-500">Aucun log.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

@endsection