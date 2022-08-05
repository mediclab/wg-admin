<x-filament::page>
    <form wire:submit.prevent="submit">
        {{ $this->form }}

        <button type="submit" class="inline-flex items-center justify-center mt-3 py-1 gap-1 font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset filament-button dark:focus:ring-offset-0 min-h-[2.25rem] px-4 text-sm text-white shadow focus:ring-white border-transparent bg-primary-600 hover:bg-primary-500 focus:bg-primary-700 focus:ring-offset-primary-700 filament-page-button-action">
            {{ __('Save changes') }}
        </button>
    </form>
</x-filament::page>
