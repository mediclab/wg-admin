<?php

namespace App\Http\Widgets;

use App\Http\Resources\DeviceResource;
use App\Models\Device;
use App\Services\DeviceService;
use Filament\Http\Livewire\Concerns\CanNotify;
use Filament\Resources\Form;
use Filament\Tables;
use Filament\Forms;
use Filament\Tables\Contracts\HasTable;
use Filament\Widgets\Concerns\CanPoll;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DevicesTable extends BaseWidget
{
    use CanPoll;
    use CanNotify;

    protected int | string | array $columnSpan = 'full';

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

    public function createDevice(array $data, DeviceService $service): void
    {
        try {
            $service->addDeviceByCurrentUser($data);
            $this->notify('success', __('Device successfully added to server'));
        } catch (\Throwable $e) {
            $this->notify('danger', __('Error occurred. Device is not added to server'));
        }
    }

    protected function getTableQuery(): Builder
    {
        $devices = Device::with('server', 'server.user');
        $user = \Auth::user();

        if ($user && !$user->isAdmin()) {
            $devices->where('server_id', $user->server->getKey());
        }

        return $devices;
    }

    protected function getTableColumns(): array
    {
        return DeviceResource::getTableColumns();
    }

    protected function getTableActions(): array
    {
        return [
            Tables\Actions\Action::make('qr-code')
                ->iconButton()
                ->icon('heroicon-o-qrcode')
                ->action('showQrCode')
                ->visible(fn (Device $record): bool => (bool) $record->is_active),
            Tables\Actions\Action::make('condig-download')
                ->iconButton()
                ->icon('heroicon-o-download')
                ->action('downloadConfig')
                ->visible(fn (Device $record): bool => (bool) $record->is_active),
            Tables\Actions\DeleteAction::make()->iconButton(),
        ];
    }

    protected function getTableHeaderActions(): array
    {
        return [
            Tables\Actions\Action::make('create-device')
                ->icon('heroicon-o-view-grid-add')
                ->action('createDevice')
                ->form(static function (Form $form) {
                    return DeviceResource::form($form)->getSchema();
                })
                ->modalButton('Create'),
        ];
    }

    protected function paginateTableQuery(Builder $query): Paginator
    {
        return $query->paginate($this->getTableRecordsPerPage());
    }

    protected function getTableFilters(): array
    {
        return [
            Tables\Filters\Filter::make('is_active')
                ->form([
                    Forms\Components\Toggle::make('is_active_toggle')
                        ->label(__('Active devices only')),
                ])
                ->query(static function (Builder $query, array $data): Builder {
                    return $query
                        ->when(
                            $data['is_active_toggle'],
                            fn (Builder $query, $toggle): Builder => $query->where('is_active', (bool) $toggle),
                        );
                }),
        ];
    }
}
