@auth
    @if(auth()->user()->isAdmin())
        <div class="flex items-center gap-3 mb-4">
            <span class="text-sm text-gray-600 dark:!text-text-secondary">{{ auth()->user()->email }}</span>
            <form method="POST" action="{{ route('admin.logout') }}" class="inline">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-semibold text-sm" style="font-family: 'Poppins', sans-serif; font-weight: 600;">
                    Logout
                </button>
            </form>
        </div>
    @endif
@endauth

