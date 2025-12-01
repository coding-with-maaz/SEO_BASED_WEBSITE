@extends('layouts.app')

@section('title', '419 - Page Expired')

@section('content')
<div class="min-h-[60vh] flex items-center justify-center px-4 sm:px-6 lg:px-8 py-12">
    <div class="max-w-2xl mx-auto text-center">
        <!-- Error Code -->
        <div class="mb-8">
            <h1 class="text-9xl md:text-[12rem] font-bold text-orange-600 dark:!text-orange-500 leading-none" style="font-family: 'Poppins', sans-serif; font-weight: 800;">
                419
            </h1>
        </div>
        
        <!-- Error Message -->
        <div class="mb-8">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 dark:!text-white mb-4" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
                Page Expired
            </h2>
            <p class="text-lg md:text-xl text-gray-600 dark:!text-text-secondary mb-6" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                Your session has expired due to inactivity. This is a security measure to protect your account.
            </p>
        </div>
        
        <!-- Action Buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            <button onclick="window.history.back()" 
                    class="inline-flex items-center px-6 py-3 bg-accent hover:bg-accent-light text-white font-semibold rounded-lg transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl"
                    style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Go Back
            </button>
            <a href="{{ route('home') }}" 
               class="inline-flex items-center px-6 py-3 bg-white dark:!bg-bg-card border-2 border-gray-300 dark:!border-border-primary hover:border-accent text-gray-700 dark:!text-white hover:text-accent font-semibold rounded-lg transition-all duration-300 transform hover:scale-105"
               style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                </svg>
                Go to Homepage
            </a>
        </div>
        
        <!-- Help Text -->
        <div class="mt-12 pt-8 border-t border-gray-200 dark:!border-border-primary">
            <div class="bg-blue-50 dark:!bg-blue-900/20 border border-blue-200 dark:!border-blue-800 rounded-lg p-4 mb-4">
                <p class="text-sm text-blue-800 dark:!text-blue-300" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                    <strong>Tip:</strong> Please refresh the page and try again. If you were submitting a form, you may need to fill it out again.
                </p>
            </div>
            <p class="text-sm text-gray-500 dark:!text-text-tertiary" style="font-family: 'Poppins', sans-serif; font-weight: 400;">
                This usually happens when a page is left open for too long. Simply refresh and try again.
            </p>
        </div>
    </div>
</div>
@endsection

