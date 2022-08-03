<div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:align-middle sm:w-full sm:p-6 dark:bg-gray-800">
    <div>
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
            <svg class="w-6 h-6 text-green-600 filament-icon-button-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"></path>
            </svg>
        </div>
        <div class="mt-3 text-center sm:mt-5">
            <h2 class="text-xl font-bold tracking-tight filament-modal-heading" id="modal-title">{{ __('Your QrCode') }}</h2>
            <div class="mt-2">
                <h3 class="text-gray-500 filament-modal-subheading">
                    {{ __('You can add configuration directly from your phone.') }}
                    <br />
                    {{ __('Use "Import from QR" in your application settings.') }}
                </h3>
                <div class="mt-2 mx-auto flex items-center justify-center h-512 w-512 rounded-xs">
                    {!! $qrCode !!}
                </div>
            </div>
        </div>
    </div>
    <div class="mt-5 sm:mt-6">
        <button wire:click="$emit('closeModal')" class="inline-flex items-center justify-center font-medium rounded-lg border transition-colors focus:outline-none focus:ring-offset-2 focus:ring-2 focus:ring-inset filament-button dark:focus:ring-offset-0 h-9 w-full px-4 text-sm text-white shadow focus:ring-white border-transparent bg-danger-600 hover:bg-danger-500 focus:bg-danger-700 focus:ring-offset-danger-700 filament-tables-modal-button-action">
            <span>{{ __('Close') }}</span>
        </button>
    </div>
</div>
