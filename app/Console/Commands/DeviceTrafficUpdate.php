<?php

namespace App\Console\Commands;

use App\Models\Device;
use Illuminate\Console\Command;

class DeviceTrafficUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'device:traffic:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update devices traffic in redis db';

    public function handle(): int
    {
        try {
            Device::each(static function (Device $device) {
                $currentTx = \Cache::get("wg_tx_current_{$device->device_id}", 0);
                $realtimeTx = $device->transfer_tx ?? 0;

                if ($currentTx > $realtimeTx) {
                    \Cache::increment("wg_tx_memory_{$device->device_id}", $currentTx);
                }

                \Cache::set("wg_tx_current_{$device->device_id}", $realtimeTx);

                $currentRx = \Cache::get("wg_rx_current_{$device->device_id}", 0);
                $realtimeRx = $device->transfer_rx ?? 0;

                if ($currentRx > $realtimeRx) {
                    \Cache::increment("wg_rx_memory_{$device->device_id}", $currentRx);
                }

                \Cache::set("wg_rx_current_{$device->device_id}", $realtimeRx);
            }, 10);

            $this->getOutput()->info('Success update devices traffic!');
        } catch (\Throwable $e) {
            \Log::error($e->getMessage());

            $this->getOutput()->error('Unable to update! See logs to more details.');
        }

        return 0;
    }
}
