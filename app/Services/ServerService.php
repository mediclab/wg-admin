<?php

declare(strict_types=1);

namespace App\Services;

use App\DTO\EnrichDevice;
use App\Exceptions\Exception\WireguardException;
use App\Exceptions\Exception\WireguardNoSuchDeviceException;
use App\Models\Server;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class ServerService
{
    public function __construct(private WireguardService $wgService) {}

    /**
     * @throws \App\Exceptions\Exception\WireguardException
     */
    public function create(User $user, array $data): Server
    {
        if ($user->server()->exists()) {
            throw new WireguardException('User already have his own server');
        }

        $privateKey = $this->wgService->generatePrivateKey();
        $publicKey = $this->wgService->generatePublicKey($privateKey);

        return Server::create(array_merge($data, [
            'user_id' => $user->getKey(),
            'private_key' => Crypt::encryptString($privateKey),
            'public_key' => $publicKey,
        ]));
    }

    /**
     * @param \App\Models\Server $server
     *
     * @return Collection<string, EnrichDevice>
     */
    public function getWgDevicesInfo(Server $server): Collection
    {
        $result = collect();
        $tag = "dump-wg{$server->getKey()}";

        try {
            try {
                if (!\Cache::driver('array')->has($tag)) {
                    \Cache::driver('array')->put($tag, $this->wgService->getDump("wg{$server->getKey()}"));
                }

                $data = \Cache::driver('array')->get($tag);
            } catch (\Throwable $e) {
                \Log::warning('Unable to use cache. Please select another driver', [
                    'message' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);

                $data = $this->wgService->getDump("wg{$server->getKey()}");
            }
        } catch (WireguardException $e) {
            return $result;
        }

        foreach ($data as $line) {
            [
                $publicKey, $presharedKey, $endpoint, $allowedIps,
                $latestHandshakeAt, $transferRx, $transferTx, $persistentKeepalive,
            ] = explode("\t", $line);

            $data = compact(
                'publicKey', 'presharedKey', 'endpoint', 'allowedIps',
                'latestHandshakeAt', 'transferRx', 'transferTx', 'persistentKeepalive'
            );

            $result->put($publicKey, (new EnrichDevice($data)));
        }

        return $result;
    }

    /**
     * @throws \App\Exceptions\Exception\WireguardException
     * @throws \App\Exceptions\Exception\UnableUseWireguardException
     */
    public function updateConfig(Server $server): void
    {
        $serverReplacing = [
            '{SERVER_PRIVATE_KEY}' => Crypt::decryptString($server->private_key),
            '{SERVER_ADDRESS}' => $server->address,
            '{SERVER_PORT}' => $server->port,
            '{INTERFACE}' => "wg{$server->server_id}",
        ];

        try {
            $serverTemplate = \File::get(config('app.templates') . DIRECTORY_SEPARATOR . 'server');
            $peerTemplate = \File::get(config('app.templates') . DIRECTORY_SEPARATOR . 'peer');
        } catch (\Throwable $e) {
            throw new WireguardException('Templates not found');
        }

        $content[] = str_replace(array_keys($serverReplacing), array_values($serverReplacing), $serverTemplate);

        foreach ($server->devices as $device) {
            $peerReplacing = [
                '{IS_ACTIVE}' => !$device->is_active ? '#' : '',
                '{DEVICE_NAME}' => $device->name,
                '{DEVICE_ID}' => $device->device_id,
                '{DEVICE_PUBLIC_KEY}' => $device->public_key,
                '{DEVICE_PRESHARED_KEY}' => Crypt::decryptString($device->preshared_key),
                '{DEVICE_ALLOWED_IPS}' => "{$device->address}/32",
            ];

            $content[] = str_replace(array_keys($peerReplacing), array_values($peerReplacing), $peerTemplate);
        }

        $filename = "wg{$server->getKey()}.conf";
        $disk = Storage::disk('config');

        if ($disk->exists($filename)) {
            $disk->delete($filename);
        }

        $disk->put($filename, implode(PHP_EOL, $content));

        try {
            $this->wgService->getDump("wg{$server->getKey()}");
        } catch (WireguardNoSuchDeviceException) {
            $this->wgService->initInterface($disk->path($filename));
        }

        $this->wgService->synchronizeConfig("wg{$server->getKey()}", $disk->path($filename));
    }

    /**
     * @throws \App\Exceptions\Exception\WireguardException
     */
    public function delete(Server $server): void
    {
        $server->load('devices');

        foreach ($server->devices as $device) {
            $device->delete();
        }

        $this->wgService->downInterface("wg{$server->getKey()}");

        $server->delete();
    }
}
