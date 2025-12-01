@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-white mb-2">SEO Tools</h1>
        <p class="text-gray-400">Manage and monitor your website's SEO performance</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Sitemap Submission -->
        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-white mb-4">Sitemap Submission</h2>
            <p class="text-gray-400 mb-4">Submit your sitemap to search engines</p>
            
            @if($lastSubmission)
                <div class="mb-4 p-3 bg-gray-700 rounded">
                    <p class="text-sm text-gray-300">Last submitted: {{ \Carbon\Carbon::parse($lastSubmission['timestamp'])->diffForHumans() }}</p>
                </div>
            @endif
            
            <form action="{{ route('admin.seo-tools.submit-sitemap') }}" method="POST" class="space-y-3">
                @csrf
                <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                    Submit to All Search Engines
                </button>
            </form>
            
            <div class="mt-4 space-y-2">
                <form action="{{ route('admin.seo-tools.submit-sitemap') }}" method="POST">
                    @csrf
                    <input type="hidden" name="engine" value="google">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                        Submit to Google
                    </button>
                </form>
                <form action="{{ route('admin.seo-tools.submit-sitemap') }}" method="POST">
                    @csrf
                    <input type="hidden" name="engine" value="bing">
                    <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded text-sm">
                        Submit to Bing
                    </button>
                </form>
            </div>
        </div>

        <!-- SEO Checker -->
        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-white mb-4">SEO Score Checker</h2>
            <p class="text-gray-400 mb-4">Analyze SEO score for any URL</p>
            
            <form action="{{ route('admin.seo-tools.check-seo') }}" method="POST" class="space-y-3">
                @csrf
                <input type="url" name="url" placeholder="https://example.com/page" 
                       class="w-full bg-gray-700 text-white px-4 py-2 rounded" required>
                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                    Check SEO
                </button>
            </form>
        </div>

        <!-- Broken Links Checker -->
        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-white mb-4">Broken Links Checker</h2>
            <p class="text-gray-400 mb-4">Find and fix broken links</p>
            
            <form action="{{ route('admin.seo-tools.check-links') }}" method="POST" class="space-y-3">
                @csrf
                <input type="url" name="url" placeholder="https://example.com/page" 
                       class="w-full bg-gray-700 text-white px-4 py-2 rounded">
                <div class="flex items-center space-x-2">
                    <input type="checkbox" name="sitemap" id="sitemap" value="1" class="rounded">
                    <label for="sitemap" class="text-gray-300 text-sm">Check all sitemap URLs</label>
                </div>
                <button type="submit" class="w-full bg-orange-600 hover:bg-orange-700 text-white px-4 py-2 rounded">
                    Check Links
                </button>
            </form>
        </div>

        <!-- Rich Snippets Tester -->
        <div class="bg-gray-800 rounded-lg p-6">
            <h2 class="text-xl font-semibold text-white mb-4">Rich Snippets Tester</h2>
            <p class="text-gray-400 mb-4">Test structured data / rich snippets</p>
            
            <form action="{{ route('admin.seo-tools.test-rich-snippets') }}" method="POST" class="space-y-3">
                @csrf
                <input type="url" name="url" placeholder="https://example.com/page" 
                       class="w-full bg-gray-700 text-white px-4 py-2 rounded" required>
                <button type="submit" class="w-full bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded">
                    Test Rich Snippets
                </button>
            </form>
        </div>
    </div>

    <!-- Artisan Commands Info -->
    <div class="mt-8 bg-gray-800 rounded-lg p-6">
        <h2 class="text-xl font-semibold text-white mb-4">Command Line Tools</h2>
        <p class="text-gray-400 mb-4">You can also use these Artisan commands:</p>
        <div class="bg-gray-900 rounded p-4 space-y-2 font-mono text-sm">
            <div class="text-green-400">php artisan seo:submit-sitemap</div>
            <div class="text-green-400">php artisan seo:check {url}</div>
            <div class="text-green-400">php artisan seo:check-links --sitemap</div>
            <div class="text-green-400">php artisan seo:test-rich-snippets {url}</div>
        </div>
    </div>
</div>
@endsection

