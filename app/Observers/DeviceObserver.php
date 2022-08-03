<?php

namespace App\Observers;

use App\Models\Device;
use App\Services\ServerService;
use Ramsey\Uuid\Uuid;

class DeviceObserver
{
    public function __construct(private ServerService $service) {}

    public function creating(Device $device): void
    {
        $device->setAttribute($device->getKeyName(), Uuid::uuid4());
    }

    /**
     * @throws \App\Exceptions\Exception\WireguardException
     */
    public function saved(Device $device): void
    {
        $device->fresh();
        $this->service->updateConfig($device->server);
    }
}
