@extends('layouts.app')

@section('title', 'Create Page SEO - Admin')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <div class="flex items-center gap-4 mb-2">
                <a href="{{ route('admin.page-seo.index') }}" class="text-gray-600 hover:text-accent dark:!text-text-secondary dark:!hover:text-accent transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    ← Back to SEO Management
                </a>
            </div>
            <h1 class="text-3xl font-bold text-gray-900 dark:!text-white" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Create Page SEO Settings
            </h1>
            <p class="text-gray-600 dark:!text-text-secondary mt-1" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                Configure SEO metadata for a public page
            </p>
        </div>
    </div>

    @if(session('success'))
    <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg dark:!bg-green-900/20 dark:!border-green-700 dark:!text-green-400">
        {{ session('success') }}
    </div>
    @endif

    <div class="bg-white dark:!bg-bg-card rounded-lg border border-gray-200 dark:!border-border-secondary p-6">
        <form action="{{ route('admin.page-seo.store') }}" method="POST">
            @csrf
            
            @if(empty($selectedPageKey))
            <div class="mb-6">
                <label class="block text-sm font-semibold text-gray-700 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    Select Page <span class="text-red-500">*</span>
                </label>
                <select name="page_key" id="page_key_select" required onchange="updatePageName()" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-accent focus:border-transparent dark:!bg-bg-card-hover dark:!border-border-primary dark:!text-white">
                    <option value="">Select a page...</option>
                    @foreach($availablePageKeys as $key => $name)
                        <option value="{{ $key }}" data-name="{{ $name }}">{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            @else
            <input type="hidden" name="page_key" value="{{ $selectedPageKey }}">
            @endif
            
            @php
                $pageSeo = null;
                if ($selectedPageKey ?? null) {
                    $pageSeo = new \App\Models\PageSeo();
                    $pageSeo->page_key = $selectedPageKey;
                    $availableKeys = \App\Models\PageSeo::getAvailablePageKeys();
                    $pageSeo->page_name = $availableKeys[$selectedPageKey] ?? '';
                }
            @endphp
            
            @include('admin.page-seo._form', ['pageSeo' => $pageSeo, 'selectedPageKey' => $selectedPageKey ?? null])
            
            <script>
            function updatePageName() {
                const select = document.getElementById('page_key_select');
                const option = select.options[select.selectedIndex];
                const pageNameInput = document.querySelector('input[name="page_name"]');
                if (option && option.dataset.name && pageNameInput) {
                    pageNameInput.value = option.dataset.name;
                }
            }
            </script>
            
            <div class="flex gap-3 mt-8">
                <button type="submit" class="px-6 py-2 bg-accent hover:bg-accent-light text-white rounded-lg transition-colors" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    Create SEO Settings
                </button>
                <a href="{{ route('admin.page-seo.index') }}" class="px-6 py-2 bg-gray-100 hover:bg-gray-200 text-gray-900 rounded-lg transition-colors dark:!bg-bg-card dark:!text-white dark:!hover:bg-bg-card-hover" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

