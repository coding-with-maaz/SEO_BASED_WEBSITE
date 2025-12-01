@extends('layouts.app')

@section('title', 'Admin - Public Pages SEO Management')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('admin.dashboard') }}" class="text-gray-600 hover:text-accent dark:!text-text-secondary dark:!hover:text-accent transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    ← Dashboard
                </a>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Public Pages SEO Management
            </h1>
            <p class="text-gray-600 dark:!text-text-secondary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                Manage SEO metadata for all public-facing pages
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.dashboard') }}" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-lg transition-colors dark:!bg-bg-card dark:!text-white dark:!hover:bg-bg-card-hover" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Dashboard
            </a>
            <a href="{{ route('admin.page-seo.create') }}" class="px-4 py-2 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                Add New Page SEO
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg dark:!bg-green-900/20 dark:!border-green-700 dark:!text-green-400">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg dark:!bg-red-900/20 dark:!border-red-700 dark:!text-red-400">
        {{ session('error') }}
    </div>
    @endif

    <!-- Available Pages Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
        @foreach(\App\Models\PageSeo::getAvailablePageKeys() as $pageKey => $pageName)
        <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-4">
            <div class="flex items-center justify-between mb-2">
                <h3 class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    {{ $pageName }}
                </h3>
                @php
                    $seo = $pageSeos->firstWhere('page_key', $pageKey);
                @endphp
                @if($seo)
                    @if($seo->is_active)
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs dark:!bg-green-900/20 dark:!text-green-400">Active</span>
                    @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs dark:!bg-gray-800 dark:!text-gray-400">Inactive</span>
                    @endif
                @else
                    <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded text-xs dark:!bg-yellow-900/20 dark:!text-yellow-400">Not Configured</span>
                @endif
            </div>
            <p class="text-sm text-gray-600 dark:!text-text-secondary mb-3" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                Page Key: <code class="text-xs bg-gray-100 dark:!bg-gray-800 px-2 py-1 rounded">{{ $pageKey }}</code>
            </p>
            <div class="flex gap-2">
                @if($seo)
                    <a href="{{ route('admin.page-seo.edit', $seo) }}" class="flex-1 px-3 py-2 bg-accent hover:bg-accent-light text-white text-center rounded-lg transition-colors text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Edit SEO
                    </a>
                @else
                    <a href="{{ route('admin.page-seo.create') }}?page_key={{ $pageKey }}" class="flex-1 px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-center rounded-lg transition-colors text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                        Configure SEO
                    </a>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Configured Pages Table -->
    @if($pageSeos->count() > 0)
    <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary overflow-hidden">
        <div class="p-6 border-b border-gray-200 dark:!border-border-secondary">
            <h2 class="text-xl font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Configured Pages ({{ $pageSeos->count() }})
            </h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50 dark:!bg-bg-card-hover">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Page</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Meta Title</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Updated</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-900 dark:!text-white uppercase tracking-wider" style="font-family: 'Poppins', sans-serif; font-weight: 600;">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:!divide-border-secondary">
                    @foreach($pageSeos as $seo)
                    <tr class="hover:bg-gray-50 dark:!hover:bg-bg-card-hover">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="font-semibold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                {{ $seo->page_name }}
                            </div>
                            <div class="text-sm text-gray-500 dark:!text-text-tertiary">
                                {{ $seo->page_key }}
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900 dark:!text-white max-w-xs truncate" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                                {{ $seo->meta_title ?: 'Not set' }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($seo->is_active)
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded text-xs dark:!bg-green-900/20 dark:!text-green-400">Active</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs dark:!bg-gray-800 dark:!text-gray-400">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:!text-text-tertiary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                            {{ $seo->updated_at->diffForHumans() }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex gap-2">
                                <a href="{{ route('admin.page-seo.edit', $seo) }}" class="text-accent hover:text-accent-light" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                    Edit
                                </a>
                                <form action="{{ route('admin.page-seo.destroy', $seo) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this SEO configuration?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection

