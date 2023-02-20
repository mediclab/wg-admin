@php
    $user = \Filament\Facades\Filament::auth()->user();
@endphp
<x-filament::page>
    <div class="mx-auto max-w-3xl px-4 sm:px-6 md:flex md:items-center md:justify-between md:space-x-5 lg:max-w-7xl lg:px-8">
        <div class="flex items-center space-x-5">
            <div class="flex-shrink-0">
                <div class="relative">
                    <img class="h-16 w-16 rounded-full" src="{{ \Filament\Facades\Filament::getUserAvatarUrl($user) }}" alt="">
                    <span class="absolute inset-0 rounded-full shadow-inner" aria-hidden="true"></span>
                </div>
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-300">{{ $user->name }}</h1>
                <p class="text-sm font-medium text-gray-500">{{ $user->email }}</p>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="submit">
        {{ $this->form }}

        <button type="submit" class="inline-flex items-center justify-center mt-3 py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset filament-button dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action">
            {{ __('Save changes') }}
        </button>
    </form>
</x-filament::page>
