<x-filament::widget class="filament-widgets-table-widget">
    <div {!! ($pollingInterval = $this->getPollingInterval()) ? "wire:poll.{$pollingInterval}" : '' !!}>
        {{ $this->table }}
    </div>
</x-filament::widget>
