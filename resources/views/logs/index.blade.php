@extends('layouts.app')

@section('content')

    <div class="max-w-5xl mx-auto space-y-6">

        <h1 class="text-2xl font-bold">Logs</h1>

        <pre class="p-4 bg-white border rounded text-sm overflow-x-auto whitespace-pre-wrap">{{ $content }}</pre>

    </div>

@endsection