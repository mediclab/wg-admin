<?php

namespace App\Http\Components;

use App\Models\Device;
use App\Services\DeviceService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\View\View;
use LivewireUI\Modal\ModalComponent;

class QrCodeModal extends ModalComponent
{
    use AuthorizesRequests;

    public string $deviceId;

    /**
     * @throws \App\Exceptions\Exception\WireguardException
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function render(DeviceService $service): ?View
    {
        $device = Device::findOrFail($this->deviceId);

        $this->authorize('view', $device);

        return view('modals.qr-code-modal', [
            'qrCode' => $service->getClientQrCode($device)
        ]);
    }

    public static function modalMaxWidthClass(): string
    {
        return 'modal-max-width-3xl';
    }
}
