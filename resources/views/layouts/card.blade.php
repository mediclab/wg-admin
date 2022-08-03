@props([
    'title' => null,
    'width' => 'md',
])

<x-filament::layouts.base :title="$title">
    <div @class([
        'flex items-center justify-center min-h-screen filament-login-page bg-gray-100 text-gray-900 pb-12',
        'dark:bg-gray-900 dark:text-white' => config('filament.dark_mode'),
    ])>
        <div @class([
            'w-screen px-6 -mt-16 space-y-8 md:mt-0 md:px-2',
            match($width) {
                'xs' => 'max-w-xs',
                'sm' => 'max-w-sm',
                'md' => 'max-w-md',
                'lg' => 'max-w-lg',
                'xl' => 'max-w-xl',
                '2xl' => 'max-w-2xl',
                '3xl' => 'max-w-3xl',
                '4xl' => 'max-w-4xl',
                '5xl' => 'max-w-5xl',
                '6xl' => 'max-w-6xl',
                '7xl' => 'max-w-7xl',
                default => $width,
            },
        ])>
            <div class="flex justify-center"
                 x-data="{
                     theme: null,

                     init: function () {
                         this.theme = localStorage.getItem('theme') || (this.isSystemDark() ? 'dark' : 'light')
                         localStorage.setItem('theme', this.theme)
                     },

                     isSystemDark: function () {
                         return window.matchMedia('(prefers-color-scheme: dark)').matches
                     },
            }">
                <div
                    x-data="{ mode: this.theme = localStorage.getItem('theme') }"
                >
                    <span x-show="mode === 'light'" style="display: none;">
                        <img width="300" src="{{ mix('img/svg/wg-admin-light.svg') }}" style="height: 70px" alt="WG-ADMIN">
                    </span>

                    <span x-show="mode === 'dark'" style="display: none;">
                        <img width="300" src="{{ mix('img/svg/wg-admin-dark.svg') }}" style="height: 70px" alt="WG-ADMIN">
                    </span>
                </div>
            </div>
            <div @class([
                'p-8 space-y-4 bg-white/50 backdrop-blur-xl border border-gray-200 shadow-2xl rounded-2xl relative',
                'dark:bg-gray-900/50 dark:border-gray-700' => config('filament.dark_mode'),
            ])>
                @if (filled($title))
                    <h2 class="text-2xl font-bold tracking-tight text-center mb-12">
                        {{ $title }}
                    </h2>
                @endif

                <div {{ $attributes }}>
                    {{ $slot }}
                </div>
            </div>
        </div>
    </div>
</x-filament::layouts.base>
