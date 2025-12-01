@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-white mb-2">Rich Snippets Test Results</h1>
        <p class="text-gray-400">URL: <a href="{{ $url }}" target="_blank" class="text-blue-400 hover:underline">{{ $url }}</a></p>
    </div>

    @if(!($result['success'] ?? false))
        <div class="bg-red-900 border border-red-700 rounded-lg p-6 mb-6">
            <h2 class="text-xl font-semibold text-white mb-2">Error</h2>
            <p class="text-red-200">{{ $result['error'] ?? 'Unknown error occurred' }}</p>
        </div>
    @else
        <!-- Status -->
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold text-white mb-4">Status</h2>
            <div class="flex items-center space-x-4">
                @if($result['valid'] ?? false)
                    <span class="text-3xl">✓</span>
                    <span class="text-2xl font-bold text-green-400">Valid</span>
                @else
                    <span class="text-3xl">✗</span>
                    <span class="text-2xl font-bold text-red-400">Invalid</span>
                @endif
            </div>
        </div>

        <!-- Formats Found -->
        @if(!empty($result['formats_found'] ?? []))
            <div class="bg-gray-800 rounded-lg p-6 mb-6">
                <h2 class="text-2xl font-semibold text-white mb-4">Structured Data Formats Found</h2>
                <div class="flex flex-wrap gap-2">
                    @foreach($result['formats_found'] as $format)
                        <span class="bg-blue-600 text-white px-3 py-1 rounded">{{ $format }}</span>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Schemas -->
        @if(!empty($result['schemas'] ?? []))
            <div class="bg-gray-800 rounded-lg p-6 mb-6">
                <h2 class="text-2xl font-semibold text-white mb-4">Schemas Detected</h2>
                <div class="space-y-4">
                    @foreach($result['schemas'] as $schema)
                        <div class="border border-gray-700 rounded p-4">
                            <div class="flex items-center space-x-2 mb-2">
                                @if($schema['valid'] ?? false)
                                    <span class="text-green-400">✓</span>
                                @else
                                    <span class="text-red-400">✗</span>
                                @endif
                                <span class="text-white font-semibold">{{ $schema['type'] ?? 'Unknown' }}</span>
                                <span class="text-gray-400 text-sm">({{ $schema['format'] ?? 'Unknown' }})</span>
                            </div>
                            
                            @if(!empty($schema['errors'] ?? []))
                                <div class="mt-2">
                                    <p class="text-red-400 font-semibold mb-1">Errors:</p>
                                    <ul class="list-disc list-inside text-red-300 text-sm">
                                        @foreach($schema['errors'] as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            
                            @if(!empty($schema['warnings'] ?? []))
                                <div class="mt-2">
                                    <p class="text-yellow-400 font-semibold mb-1">Warnings:</p>
                                    <ul class="list-disc list-inside text-yellow-300 text-sm">
                                        @foreach($schema['warnings'] as $warning)
                                            <li>{{ $warning }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Errors -->
        @if(!empty($result['errors'] ?? []))
            <div class="bg-red-900 border border-red-700 rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-white mb-4">Errors</h2>
                <ul class="list-disc list-inside text-red-200">
                    @foreach($result['errors'] as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Warnings -->
        @if(!empty($result['warnings'] ?? []))
            <div class="bg-yellow-900 border border-yellow-700 rounded-lg p-6 mb-6">
                <h2 class="text-xl font-semibold text-white mb-4">Warnings</h2>
                <ul class="list-disc list-inside text-yellow-200">
                    @foreach($result['warnings'] as $warning)
                        <li>{{ $warning }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Manual Testing Links -->
        <div class="bg-gray-800 rounded-lg p-6 mb-6">
            <h2 class="text-2xl font-semibold text-white mb-4">Manual Testing Tools</h2>
            <div class="space-y-2">
                <a href="{{ $googleTestUrl }}" target="_blank" class="block bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Google Rich Results Test →
                </a>
                <a href="{{ $schemaValidatorUrl }}" target="_blank" class="block bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    Schema.org Validator →
                </a>
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

