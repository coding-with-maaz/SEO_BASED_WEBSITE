@extends('layouts.app')

@section('title', 'How to Download - Nazaarabox')

@section('content')
<div class="w-full px-4 sm:px-6 lg:px-8 xl:px-12 py-8">
    <div class="max-w-4xl mx-auto">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:!text-white mb-8" style="font-family: 'Poppins', sans-serif; font-weight: 700;">
            How to Download
        </h1>

        <div class="bg-white dark:!bg-bg-card border border-gray-200 dark:!border-border-secondary rounded-lg p-6 md:p-8 space-y-6" style="font-family: 'Poppins', sans-serif;">
            <section>
                <h2 class="text-2xl font-bold text-gray-900 dark:!text-white mb-4" style="font-weight: 700;">Step-by-Step Download Guide</h2>
                <p class="text-gray-700 dark:!text-text-secondary leading-relaxed mb-6" style="font-weight: 400;">
                    Follow these simple steps to download your favorite movies and TV shows from Nazaarabox.
                </p>
            </section>

            <section class="space-y-6">
                <div class="border-l-4 border-accent pl-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:!text-white mb-2" style="font-weight: 700;">Step 1: Browse Content</h3>
                    <p class="text-gray-700 dark:!text-text-secondary leading-relaxed" style="font-weight: 400;">
                        Navigate to the <a href="{{ route('movies.index') }}" class="text-accent hover:text-accent-light underline" style="font-weight: 600;">Movies</a> or <a href="{{ route('tv-shows.index') }}" class="text-accent hover:text-accent-light underline" style="font-weight: 600;">TV Shows</a> section to find the content you want to download.
                    </p>
                </div>

                <div class="border-l-4 border-accent pl-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:!text-white mb-2" style="font-weight: 700;">Step 2: Select Content</h3>
                    <p class="text-gray-700 dark:!text-text-secondary leading-relaxed" style="font-weight: 400;">
                        Click on the movie or TV show you want to download. This will take you to the detail page where you can see all available download options.
                    </p>
                </div>

                <div class="border-l-4 border-accent pl-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:!text-white mb-2" style="font-weight: 700;">Step 3: Choose Download Link</h3>
                    <p class="text-gray-700 dark:!text-text-secondary leading-relaxed" style="font-weight: 400;">
                        On the content detail page, scroll down to find the "Download" section. You'll see multiple download options with different quality levels (HD, Full HD, etc.). Click on the download button that matches your preferred quality.
                    </p>
                </div>

                <div class="border-l-4 border-accent pl-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:!text-white mb-2" style="font-weight: 700;">Step 4: Download Process</h3>
                    <p class="text-gray-700 dark:!text-text-secondary leading-relaxed" style="font-weight: 400;">
                        After clicking the download button, you may be redirected to a download page or the file may start downloading directly. Some links may require you to wait a few seconds before the download begins. Please be patient and avoid clicking multiple times.
                    </p>
                </div>

                <div class="border-l-4 border-accent pl-6">
                    <h3 class="text-xl font-bold text-gray-900 dark:!text-white mb-2" style="font-weight: 700;">Step 5: Save File</h3>
                    <p class="text-gray-700 dark:!text-text-secondary leading-relaxed" style="font-weight: 400;">
                        Once the download starts, your browser will prompt you to save the file. Choose a location on your device where you want to save the file and click "Save". The download time will depend on your internet connection speed and the file size.
                    </p>
                </div>
            </section>

            <section class="mt-8 p-6 bg-gray-50 dark:!bg-bg-card-hover rounded-lg">
                <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-weight: 700;">Tips for Better Downloads</h2>
                <ul class="space-y-3 text-gray-700 dark:!text-text-secondary" style="font-weight: 400;">
                    <li class="flex items-start">
                        <span class="text-accent mr-2">•</span>
                        <span>Use a stable internet connection for faster and uninterrupted downloads.</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-accent mr-2">•</span>
                        <span>Make sure you have enough storage space on your device before downloading.</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-accent mr-2">•</span>
                        <span>If a download link doesn't work, try another quality option or server.</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-accent mr-2">•</span>
                        <span>For TV shows, you may need to download episodes individually.</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-accent mr-2">•</span>
                        <span>Some download links may require you to disable ad-blockers temporarily.</span>
                    </li>
                </ul>
            </section>

            <section class="mt-8 p-6 bg-yellow-50 dark:!bg-yellow-900/20 border border-yellow-200 dark:!border-yellow-800 rounded-lg">
                <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-3" style="font-weight: 700;">⚠️ Important Notice</h2>
                <p class="text-gray-700 dark:!text-text-secondary leading-relaxed" style="font-weight: 400;">
                    This website does not host any files on its server. All download links are provided by third-party sources. We are not responsible for the content of external links. Please ensure you comply with your local copyright laws when downloading content.
                </p>
            </section>

            <section class="mt-8">
                <h2 class="text-xl font-bold text-gray-900 dark:!text-white mb-4" style="font-weight: 700;">Need Help?</h2>
                <p class="text-gray-700 dark:!text-text-secondary leading-relaxed mb-4" style="font-weight: 400;">
                    If you're experiencing issues with downloads, please check the following:
                </p>
                <ul class="space-y-2 text-gray-700 dark:!text-text-secondary" style="font-weight: 400;">
                    <li class="flex items-start">
                        <span class="text-accent mr-2">•</span>
                        <span>Your internet connection is stable</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-accent mr-2">•</span>
                        <span>You have sufficient storage space</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-accent mr-2">•</span>
                        <span>Your browser allows downloads</span>
                    </li>
                    <li class="flex items-start">
                        <span class="text-accent mr-2">•</span>
                        <span>Try a different browser if issues persist</span>
                    </li>
                </ul>
            </section>
        </div>
    </div>
</div>
@endsection

