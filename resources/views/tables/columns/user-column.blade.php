@if(($user = $getState()) instanceof \App\Models\User)
    <div class="flex items-center py-3">
        <div class="h-10 w-10 flex-shrink-0">
            <img class="h-10 w-10 rounded-full" src="{{ $user->gravatar() }}" alt="">
        </div>
        <div class="ml-4">
            <div class="font-medium filament-tables-text-column">{{ $user->name }}</div>
            <div class="text-gray-500">{{ $user->email }}</div>
        </div>
    </div>
@else
    <div class="font-medium filament-tables-text-column">{{ __('Not a user') }}</div>
@endif
