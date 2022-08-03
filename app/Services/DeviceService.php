<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\Exception\WireguardException;
use App\Models\Device;
use App\Models\Server;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\HtmlString;

class DeviceService
{
    public function __construct(private WireguardService $wgService) {}

    /**
     * @throws \App\Exceptions\Exception\WireguardException
     */
    public function addDeviceByCurrentUser(array $data): Device
    {
        $server = \Auth::user()->server;
        $data['dns'] = array_values(
            array_filter([$data['dns_1'] ?? null, $data['dns_2'] ?? null])
        );

        return $this->addDeviceToServer($server, $data);
    }

    /**
     * @throws \App\Exceptions\Exception\WireguardException
     */
    public function addDeviceToServer(Server $server, array $data): Device
    {
        $privateKey = $this->wgService->generatePrivateKey();
        $publicKey = $this->wgService->generatePublicKey($privateKey);
        $presharedKey = $this->wgService->generatePresharedKey();

        return Device::create(array_merge($data, [
            'server_id' => $server->getKey(),
            'public_key' => $publicKey,
            'private_key' => Crypt::encryptString($privateKey),
            'preshared_key' => Crypt::encryptString($presharedKey),
        ]));
    }

    /**
     * @throws \App\Exceptions\Exception\WireguardException
     */
    public function getClientQrCode(Device $device): HtmlString
    {
        return \QrCode::format('svg')
            ->size(512)
            ->errorCorrection('H')
            ->generate($this->getClientConfig($device));
    }

    /**
     * @throws \App\Exceptions\Exception\WireguardException
     */
    public function getClientConfig(Device $device): string
    {
        try {
            $clientTemplate = \File::get(config('app.templates') . DIRECTORY_SEPARATOR . 'client_config');
        } catch (\Throwable $e) {
            throw new WireguardException('Template not found');
        }

        $additional = count($device->dns) > 0 ? 'DNS = ' . implode(',', $device->dns) . PHP_EOL : '';
        $additional .= "MTU = {$device->mtu}";

        $clientReplacing = [
            '{CLIENT_PRIVATE_KEY}' => Crypt::decryptString($device->private_key),
            '{CLIENT_ADDRESS}' => $device->address,
            '{SERVER_PUBLIC_KEY}' => $device->server->public_key,
            '{CLIENT_PRESHARED_KEY}' => Crypt::decryptString($device->preshared_key),
            '{ADDITIONAL_FIELDS}' => $additional,
            '{WG_ALLOWED_IPS}' => '0.0.0.0/0,::/0',
            '{WG_PERSISTENT_KEEPALIVE}' => $device->keep_alive,
            '{WG_HOST}' => config('wg.host', '127.0.0.1'),
            '{WG_PORT}' => $device->server->port,
        ];

        return str_replace(array_keys($clientReplacing), array_values($clientReplacing), $clientTemplate);
    }
}
