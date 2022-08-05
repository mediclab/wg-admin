<?php

namespace App\Http\Resources\UserResource\Pages;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\ServerService;
use Filament\Notifications\Notification;
use Filament\Pages\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    public function createUser(array $data, ServerService $service): void
    {
        try {
            \DB::transaction(static function () use ($service, $data) {
                $user = User::create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'password' => \Hash::make($data['password']),
                    'is_admin' => (bool) $data['is_admin']
                ]);

                $service->create($user, ['address' => $data['server']['address'], 'port' => $data['server']['port']]);
            });

            Notification::make()
                ->title(__('User successfully created'))
                ->success()
                ->send()
            ;
        } catch (\Throwable $e) {
            \Log::error('Error user creating', ['exception' => $e]);

            Notification::make()
                ->title(__('Error occurred. User is not created'))
                ->danger()
                ->body($e->getMessage())
                ->persistent()
                ->send()
            ;
        }
    }

    /**
     * @throws \Exception
     */
    protected function getActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->disableCreateAnother()
                ->action('createUser')
                ->icon('heroicon-o-user-add'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            UserResource\Widgets\ListUsersDescription::class,
        ];
    }
}
