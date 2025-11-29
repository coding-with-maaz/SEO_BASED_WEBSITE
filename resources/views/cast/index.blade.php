@extends('layouts.app')

@section('title', 'Cast Members - Nazaarabox')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:!text-white mb-2" style="font-family: 'Poppins', sans-serif; font-weight: 800;">
            Cast Members
        </h1>
        <p class="text-gray-600 dark:!text-text-secondary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
            Browse all actors and actresses
        </p>
    </div>

    <!-- Search Form -->
    <div class="mb-8">
        <form action="{{ route('cast.index') }}" method="GET" class="max-w-md">
            <div class="relative">
                <input 
                    type="text" 
                    name="search" 
                    placeholder="Search cast members..." 
                    value="{{ $search }}"
                    class="w-full px-6 py-3 pr-14 text-gray-900 bg-gray-100 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-accent/50 dark:!bg-bg-card dark:!border-border-primary dark:!text-white transition-all"
                    style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                <button 
                    type="submit" 
                    class="absolute right-2 top-1/2 transform -translate-y-1/2 px-4 py-2 bg-accent hover:bg-accent-light text-white rounded-full font-semibold transition-all"
                    style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>

    <!-- Cast Grid -->
    @if($casts->count() > 0)
    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4 md:gap-6">
        @foreach($casts as $cast)
        @php
            $profileUrl = null;
            $profilePath = $cast->profile_path ?? null;
            
            if ($profilePath) {
                if (str_starts_with($profilePath, 'http')) {
                    $profileUrl = $profilePath;
                } elseif (str_starts_with($profilePath, '/')) {
                    $profileUrl = app(\App\Services\TmdbService::class)->getImageUrl($profilePath, 'w185');
                } else {
                    $profileUrl = $profilePath;
                }
            }
        @endphp
        <article class="group cursor-pointer">
            <a href="{{ route('cast.show', $cast->slug ?? $cast->id) }}" class="block">
                <div class="relative overflow-hidden rounded-lg bg-gray-200 dark:bg-gray-800 aspect-[2/3] mb-3">
                    @if($profileUrl)
                    <img src="{{ $profileUrl }}" 
                         alt="{{ $cast->name }}" 
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="w-full h-full items-center justify-center hidden">
                        <span class="text-gray-400 text-xs">No Photo</span>
                    </div>
                    @else
                    <div class="w-full h-full flex items-center justify-center">
                        <span class="text-gray-400 text-xs">No Photo</span>
                    </div>
                    @endif
                </div>
                <h3 class="text-sm font-semibold text-gray-900 dark:!text-white group-hover:text-accent transition-colors text-center line-clamp-2 mb-1" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    {{ $cast->name }}
                </h3>
                @if($cast->contents_count > 0)
                <p class="text-xs text-gray-500 dark:!text-text-secondary text-center" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                    {{ $cast->contents_count }} {{ Str::plural('Title', $cast->contents_count) }}
                </p>
                @endif
            </a>
        </article>
        @endforeach
    </div>
    @else
    <div class="text-center py-16">
        <p class="text-gray-600 dark:!text-text-secondary text-lg" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
            No cast members found.
        </p>
    </div>
    @endif

    <!-- Pagination -->
    @if($totalPages > 1)
    <div class="mt-8 flex justify-center items-center gap-2 flex-wrap">
        @if($currentPage > 1)
        <a href="{{ route('cast.index', array_merge(request()->query(), ['page' => $currentPage - 1])) }}" 
           class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-900 rounded-lg transition-all dark:!bg-bg-card dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white" 
           style="font-family: 'Poppins', sans-serif; font-weight: 500;">
            Previous
        </a>
        @endif
        
        @for($i = 1; $i <= $totalPages; $i++)
            @if($i == 1 || $i == $totalPages || ($i >= $currentPage - 2 && $i <= $currentPage + 2))
            <a href="{{ route('cast.index', array_merge(request()->query(), ['page' => $i])) }}" 
               class="px-4 py-2 rounded-lg transition-all {{ $i == $currentPage ? 'bg-accent text-white' : 'bg-white hover:bg-gray-50 text-gray-900 dark:!bg-bg-card dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white' }}" 
               style="font-family: 'Poppins', sans-serif; font-weight: 500;">
                {{ $i }}
            </a>
            @elseif($i == $currentPage - 3 || $i == $currentPage + 3)
            <span class="px-2 text-gray-500">...</span>
            @endif
        @endfor
        
        @if($currentPage < $totalPages)
        <a href="{{ route('cast.index', array_merge(request()->query(), ['page' => $currentPage + 1])) }}" 
           class="px-4 py-2 bg-white hover:bg-gray-50 text-gray-900 rounded-lg transition-all dark:!bg-bg-card dark:!text-text-secondary dark:!hover:bg-bg-card-hover dark:!hover:text-white" 
           style="font-family: 'Poppins', sans-serif; font-weight: 500;">
            Next
        </a>
        @endif
    </div>
    @endif
</div>
@endsection

