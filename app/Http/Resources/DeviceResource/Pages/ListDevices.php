<?php

namespace App\Http\Resources\DeviceResource\Pages;

use App\Http\Resources\DeviceResource;
use App\Models\Device;
use App\Services\DeviceService;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ListDevices extends ListRecords
{
    protected static string $resource = DeviceResource::class;

    public function createDevice(array $data, DeviceService $service): void
    {
        try {
            $service->addDeviceByCurrentUser($data);

            Notification::make()
                ->title(__('Device successfully added to server'))
                ->success()
                ->send()
            ;
        } catch (\Throwable $e) {
            Notification::make()
                ->title(__('Error occurred. Device is not added to server'))
                ->danger()
                ->body($e->getMessage())
                ->persistent()
                ->send()
            ;
        }
    }

    public function showQrCode(HasTable $livewire, Model $record): void
    {
        $livewire->emit('openModal', 'qr-code-modal', ['deviceId' => $record->getKey()]);
    }

    public function downloadConfig(Device $record): StreamedResponse
    {
        return response()->streamDownload(static function () use ($record) {
            echo app(DeviceService::class)->getClientConfig($record);
        }, "{$record->name}.conf");
    }

    /**
     * @throws \Exception
     */
    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make('create-device')
                ->icon('heroicon-o-view-grid-add')
                ->action('createDevice'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            DeviceResource\Widgets\ListDevicesDescription::class,
        ];
    }
}
