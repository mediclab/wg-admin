<?php

namespace App\Http\Widgets;

use App\Helpers\Util;
use App\Models\Device;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
    {
        $devices = collect();

        try {
            $devices = Device::with('server')->get();
            $totalDownload = $devices->pluck('transferRx')->sum();
            $totalUpload = $devices->pluck('transferTx')->sum();
            $connected = $devices->pluck('isOnline')->filter()->count();
            $status = __('active');
        } catch (\Throwable $e) {
            \Log::warning('Cannot recalculate admin cards', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }

        return [
            Card::make(__('VPN Status'), strtoupper($status ?? __('inactive')))
                ->icon('heroicon-o-shield-check'),

            Card::make(__('Users'), User::count('user_id'))
                ->icon('heroicon-o-users'),

            Card::make(__('Devices'), $devices->count())
                ->icon('heroicon-o-device-mobile'),

            Card::make(__('Connected'), $connected ?? 0)
                ->icon('heroicon-o-link'),

            Card::make(__('Total upload'), Util::bytesToHuman($totalUpload ?? 0))
                ->icon('heroicon-o-cloud-upload'),

            Card::make(__('Total download'), Util::bytesToHuman($totalDownload ?? 0))
                ->icon('heroicon-o-cloud-download'),
        ];
    }
}
