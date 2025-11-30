@extends('layouts.app')

@section('title', 'Create SEO Page - Admin')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6">
        <div class="flex items-center gap-4 mb-2">
            <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-accent dark:!text-text-secondary dark:!hover:text-accent transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                ← Dashboard
            </a>
            <span class="text-gray-400">|</span>
            <a href="{{ route('admin.seo-pages.index') }}" class="text-gray-600 hover:text-accent dark:!text-text-secondary dark:!hover:text-accent transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                SEO Pages
            </a>
        </div>
        <h1 class="text-3xl font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
            Create SEO Page
        </h1>
        <p class="text-gray-600 dark:!text-text-secondary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
            Add SEO settings for a new page
        </p>
    </div>

    <form action="{{ route('admin.seo-pages.store') }}" method="POST" class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
        @csrf
        
        <!-- Page Selection -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Page Key <span class="text-red-500">*</span>
            </label>
            <select name="page_key" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                {!! $pageOptions ?? '<option value="">Select a page...</option>' !!}
            </select>
            @error('page_key')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Page Name -->
        <div class="mb-6">
            <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Page Name <span class="text-red-500">*</span>
            </label>
            <input type="text" name="page_name" value="{{ old('page_name') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
            @error('page_name')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <!-- Basic Meta Tags -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Basic Meta Tags</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Meta Title</label>
                    <input type="text" name="meta_title" value="{{ old('meta_title') }}" maxlength="255" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                    <p class="mt-1 text-xs text-gray-500 dark:!text-text-secondary">Recommended: 50-60 characters</p>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Meta Description</label>
                    <textarea name="meta_description" rows="3" maxlength="500" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ old('meta_description') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:!text-text-secondary">Recommended: 150-160 characters</p>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Meta Keywords</label>
                    <input type="text" name="meta_keywords" value="{{ old('meta_keywords') }}" placeholder="keyword1, keyword2, keyword3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                    <p class="mt-1 text-xs text-gray-500 dark:!text-text-secondary">Comma-separated keywords</p>
                </div>
            </div>
        </div>

        <!-- Open Graph Tags -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Open Graph Tags</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">OG Title</label>
                    <input type="text" name="og_title" value="{{ old('og_title') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">OG Description</label>
                    <textarea name="og_description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ old('og_description') }}</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">OG Image URL</label>
                    <input type="url" name="og_image" value="{{ old('og_image') }}" placeholder="https://example.com/image.jpg" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">OG URL</label>
                    <input type="url" name="og_url" value="{{ old('og_url') }}" placeholder="https://example.com/page" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                </div>
            </div>
        </div>

        <!-- Twitter Card Tags -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Twitter Card Tags</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Twitter Card Type</label>
                    <select name="twitter_card" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                        <option value="">Select type...</option>
                        <option value="summary" {{ old('twitter_card') == 'summary' ? 'selected' : '' }}>Summary</option>
                        <option value="summary_large_image" {{ old('twitter_card') == 'summary_large_image' ? 'selected' : '' }}>Summary Large Image</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Twitter Title</label>
                    <input type="text" name="twitter_title" value="{{ old('twitter_title') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Twitter Description</label>
                    <textarea name="twitter_description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ old('twitter_description') }}</textarea>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Twitter Image URL</label>
                    <input type="url" name="twitter_image" value="{{ old('twitter_image') }}" placeholder="https://example.com/image.jpg" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                </div>
            </div>
        </div>

        <!-- Additional SEO -->
        <div class="mb-8">
            <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">Additional SEO</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Canonical URL</label>
                    <input type="url" name="canonical_url" value="{{ old('canonical_url') }}" placeholder="https://example.com/page" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Schema Markup (JSON-LD)</label>
                    <textarea name="schema_markup" rows="6" placeholder='{"@context": "https://schema.org", "@type": "WebPage", ...}' class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white font-mono text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 400;">{{ old('schema_markup') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500 dark:!text-text-secondary">Enter valid JSON-LD structured data</p>
                </div>
            </div>
        </div>

        <!-- Status -->
        <div class="mb-6">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="w-4 h-4 text-accent border-gray-300 rounded focus:ring-accent">
                <span class="text-sm font-semibold text-gray-700 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Active</span>
            </label>
        </div>

        <!-- Submit Buttons -->
        <div class="flex items-center gap-4">
            <button type="submit" class="px-6 py-2 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Create SEO Page
            </button>
            <a href="{{ route('admin.seo-pages.index') }}" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-lg transition-colors dark:!bg-bg-card-hover dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

