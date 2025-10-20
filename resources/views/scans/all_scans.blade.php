@extends('layouts.app')

@section('content')
    <h1 class="text-2xl font-bold mb-4">All Scans</h1>

    @if(session('error'))
        <div id="error-message" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4 flex justify-between items-center">
            <span>{{ session('error') }}</span>
            <button onclick="document.getElementById('error-message').remove();" class="ml-4 font-bold">&times;</button>
        </div>
    @endif

    <form action="{{ route('scans.run') }}" method="POST" class="mb-6">
        @csrf
        <button type="submit" class="bg-green-500 px-4 py-2 rounded text-white">
            Run New Scan
        </button>
    </form>

    <table class="min-w-full bg-white border border-gray-300 rounded shadow">
        <thead>
        <tr class="bg-gray-100 text-left border-b">
            <th class="px-4 py-2">Scan ID</th>
            <th class="px-4 py-2">Occurred At</th>
            <th class="px-4 py-2">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach($scans as $scan)
            <tr class="bg-white border-b">
                <td class="px-4 py-2 ">{{ $scan->id }}</td>
                <td class="px-4 py-2">{{ $scan->occurred_at->format('d-m-Y H:i:s') }}</td>
                <td class="px-4 py-2">
                    <a href="{{ route('scans.show', $scan->id) }}" class="bg-blue-500 px-3 py-1 rounded text-white">
                        Scan Details
                    </a>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>
@endsection
