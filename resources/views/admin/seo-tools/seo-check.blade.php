@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-white mb-2">SEO Analysis Results</h1>
        <p class="text-gray-400">URL: <a href="{{ $url }}" target="_blank" class="text-blue-400 hover:underline">{{ $url }}</a></p>
    </div>

    @if(!($analysis['success'] ?? false))
        <div class="bg-red-900 border border-red-700 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-white mb-2">Error</h2>
            <p class="text-red-200">{{ $analysis['error'] ?? 'Unknown error occurred' }}</p>
        </div>
    @else
        @php
            $score = $analysis['score'] ?? 0;
            $maxScore = $analysis['max_score'] ?? 100;
            $percentage = round(($score / $maxScore) * 100);
        @endphp

        <!-- SEO Score -->
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold text-white mb-4">SEO Score</h2>
            <div class="flex items-center space-x-4">
                <div class="text-5xl font-bold {{ $percentage >= 80 ? 'text-green-400' : ($percentage >= 60 ? 'text-yellow-400' : 'text-red-400') }}">
                    {{ $score }}/{{ $maxScore }}
                </div>
                <div class="flex-1">
                    <div class="w-full bg-gray-700 rounded-full h-4">
                        <div class="bg-{{ $percentage >= 80 ? 'green' : ($percentage >= 60 ? 'yellow' : 'red') }}-500 h-4 rounded-full" 
                             style="width: {{ $percentage }}%"></div>
                    </div>
                    <p class="text-gray-400 text-sm mt-2">{{ $percentage }}%</p>
                </div>
            </div>
        </div>

        <!-- SEO Checks -->
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold text-white mb-4">SEO Checks</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b border-gray-700">
                            <th class="pb-3 text-gray-300">Check</th>
                            <th class="pb-3 text-gray-300">Status</th>
                            <th class="pb-3 text-gray-300">Score</th>
                            <th class="pb-3 text-gray-300">Issues</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($analysis['checks'] ?? [] as $checkName => $check)
                            <tr class="border-b border-gray-700">
                                <td class="py-3 text-white">{{ ucfirst(str_replace('_', ' ', $checkName)) }}</td>
                                <td class="py-3">
                                    @if($check['passed'] ?? false)
                                        <span class="text-green-400">✓ Pass</span>
                                    @else
                                        <span class="text-red-400">✗ Fail</span>
                                    @endif
                                </td>
                                <td class="py-3 text-gray-300">
                                    {{ $check['score'] ?? 0 }}/{{ $check['max_score'] ?? 0 }}
                                </td>
                                <td class="py-3">
                                    @if(!empty($check['issues'] ?? []))
                                        <ul class="list-disc list-inside text-red-400 text-sm">
                                            @foreach($check['issues'] as $issue)
                                                <li>{{ $issue }}</li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <span class="text-gray-500">None</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recommendations -->
        @if(!empty($analysis['recommendations'] ?? []))
            <div class="bg-yellow-900 border border-yellow-700 rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-white mb-4">Recommendations</h2>
                <ul class="space-y-2">
                    @foreach($analysis['recommendations'] as $rec)
                        <li class="text-yellow-200">
                            <strong>[{{ ucfirst(str_replace('_', ' ', $rec['check'])) }}]</strong> {{ $rec['issue'] }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
    @endif

    <div class="mt-6">
        <a href="{{ route('admin.seo-tools.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-6 py-2 rounded">
            ← Back to SEO Tools
        </a>
    </div>
</div>
@endsection

