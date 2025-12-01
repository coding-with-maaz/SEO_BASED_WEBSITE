@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-white mb-2">Broken Links Check Results</h1>
    </div>

    <!-- Summary -->
    <div class="bg-gray-800 rounded-lg p-6 mb-6">
        <h2 class="text-2xl font-semibold text-white mb-4">Summary</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="text-3xl font-bold text-white">{{ $summary['total'] ?? 0 }}</div>
                <div class="text-gray-400 text-sm">Total Links</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-green-400">{{ $summary['working'] ?? 0 }}</div>
                <div class="text-gray-400 text-sm">Working</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-red-400">{{ $summary['broken'] ?? 0 }}</div>
                <div class="text-gray-400 text-sm">Broken</div>
            </div>
            <div class="text-center">
                <div class="text-3xl font-bold text-yellow-400">{{ $summary['redirects'] ?? 0 }}</div>
                <div class="text-gray-400 text-sm">Redirects</div>
            </div>
        </div>
    </div>

    <!-- Broken Links -->
    @if(!empty($summary['broken_links'] ?? []))
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold text-white mb-4">Broken Links</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="pb-3 text-gray-300">URL</th>
                            <th class="pb-3 text-gray-300">Status Code</th>
                            <th class="pb-3 text-gray-300">Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($summary['broken_links'] as $link)
                            <tr class="border-b border-gray-700">
                                <td class="py-3 text-white break-all">{{ $link['url'] ?? 'N/A' }}</td>
                                <td class="py-3 text-red-400">{{ $link['status_code'] ?? 'N/A' }}</td>
                                <td class="py-3 text-gray-300">{{ $link['message'] ?? 'Unknown error' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Redirects -->
    @if(!empty($summary['redirect_links'] ?? []))
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold text-white mb-4">Redirects</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="pb-3 text-gray-300">URL</th>
                            <th class="pb-3 text-gray-300">Status Code</th>
                            <th class="pb-3 text-gray-300">Final URL</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(array_slice($summary['redirect_links'], 0, 20) as $link)
                            <tr class="border-b border-gray-700">
                                <td class="py-3 text-white break-all">{{ $link['url'] ?? 'N/A' }}</td>
                                <td class="py-3 text-yellow-400">{{ $link['status_code'] ?? 'N/A' }}</td>
                                <td class="py-3 text-gray-300 break-all">{{ $link['final_url'] ?? $link['url'] ?? 'N/A' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="mt-6">
        <a href="{{ route('admin.seo-tools.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-2 rounded">
            ← Back to SEO Tools
        </a>
    </div>
</div>
@endsection

