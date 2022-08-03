<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\Exception\UnableUseWireguardException;
use App\Exceptions\Exception\WireguardException;
use App\Exceptions\Exception\WireguardNoSuchDeviceException;
use App\Helpers\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class WireguardService
{
    public function __construct(private Process $process) {}

    /**
     * @throws \App\Exceptions\Exception\WireguardException
     */
    public function generatePrivateKey(): string
    {
        try {
            return $this->process->exec('wg genkey');
        } catch (ProcessFailedException $e) {
            \Log::channel('wireguard')->error('Process failed', [
                'command' => $e->getProcess()->getCommandLine(),
                'output' => $e->getProcess()->getOutput(),
                'errorOutput' => $e->getProcess()->getErrorOutput(),
            ]);

            throw new WireguardException('Unable to generate private key', $e->getCode(), $e->getPrevious());
        } catch (\Throwable $e) {
            \Log::error($e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            throw new WireguardException('Unable to generate private key', $e->getCode(), $e->getPrevious());
        }
    }

    /**
     * @throws \App\Exceptions\Exception\WireguardException
     */
    public function generatePresharedKey(): string
    {
        try {
            return $this->process->exec('wg genpsk');
        } catch (ProcessFailedException $e) {
            \Log::channel('wireguard')->error('Process failed', [
                'command' => $e->getProcess()->getCommandLine(),
                'output' => $e->getProcess()->getOutput(),
                'errorOutput' => $e->getProcess()->getErrorOutput(),
            ]);

            throw new WireguardException('Unable to generate preshared key', $e->getCode(), $e->getPrevious());
        } catch (\Throwable $e) {
            \Log::error($e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            throw new WireguardException('Unable to generate preshared key', $e->getCode(), $e->getPrevious());
        }
    }

    /**
     * @throws \App\Exceptions\Exception\WireguardException
     */
    public function generatePublicKey(string $privateKey): string
    {
        try {
            return $this->process->exec('echo $KEY | wg pubkey', ['KEY' => $privateKey]);
        } catch (ProcessFailedException $e) {
            \Log::channel('wireguard')->error('Process failed', [
                'command' => $e->getProcess()->getCommandLine(),
                'output' => $e->getProcess()->getOutput(),
                'errorOutput' => $e->getProcess()->getErrorOutput(),
            ]);

            throw new WireguardException('Unable to generate public key', $e->getCode(), $e->getPrevious());
        } catch (\Throwable $e) {
            \Log::error($e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            throw new WireguardException('Unable to generate public key', $e->getCode(), $e->getPrevious());
        }
    }

    /**
     * @throws \App\Exceptions\Exception\WireguardException
     * @throws \App\Exceptions\Exception\UnableUseWireguardException
     */
    public function initInterface(string $configPath): void
    {
        if (config('app.env') !== 'production') {
            \Log::channel('wireguard')->info('WireGuard is not in production. Return nothing.');
            return;
        }

        try {
            $this->process->exec('sudo wg-quick down $CONFIG', ['CONFIG' => $configPath]);
        } catch (\Throwable $e) {}

        try {
            $this->process->exec('sudo wg-quick up $CONFIG', ['CONFIG' => $configPath]);
        } catch (ProcessFailedException $e) {
            \Log::channel('wireguard')->error('Process failed', [
                'command' => $e->getProcess()->getCommandLine(),
                'output' => $e->getProcess()->getOutput(),
                'errorOutput' => $e->getProcess()->getErrorOutput(),
            ]);

            if (str_contains($e->getMessage(), "Cannot find device")) {
                throw new UnableUseWireguardException('Unable to initialize interface. You can\'t use WireGuard');
            }

            throw new WireguardException('Unable to initialize interface', $e->getCode(), $e->getPrevious());
        }  catch (\Throwable $e) {
            \Log::error($e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            throw new WireguardException('Unable to initialize interface', $e->getCode(), $e->getPrevious());
        }
    }

    /**
     * @throws \App\Exceptions\Exception\WireguardException
     */
    public function downInterface(string $interfaceName): void
    {
        if (config('app.env') !== 'production') {
            \Log::channel('wireguard')->info('WireGuard is not in production. Return nothing.');
            return;
        }

        try {
            $this->process->exec('sudo wg-quick down $INTERFACE', ['INTERFACE' => $interfaceName]);
            $this->process->exec('sudo rm $ETC_CONFIG', ['ETC_CONFIG' => "/etc/wireguard/$interfaceName.conf"]);
        } catch (ProcessFailedException $e) {
            \Log::channel('wireguard')->error('Process failed', [
                'command' => $e->getProcess()->getCommandLine(),
                'output' => $e->getProcess()->getOutput(),
                'errorOutput' => $e->getProcess()->getErrorOutput(),
            ]);

            throw new WireguardException('Unable to remove interface', $e->getCode(), $e->getPrevious());
        }  catch (\Throwable $e) {
            \Log::error($e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            throw new WireguardException('Unable to remove interface', $e->getCode(), $e->getPrevious());
        }
    }

    /**
     * @throws \App\Exceptions\Exception\WireguardException
     */
    public function synchronizeConfig(string $interfaceName, string $configPath): void
    {
        if (config('app.env') !== 'production') {
            \Log::channel('wireguard')->info('WireGuard is not in production. Return nothing.');
            return;
        }

        if (!\File::exists($configPath)) {
            throw new WireguardException("Config file {$configPath} does not exists");
        }

        if (0 === preg_match('#^wg\d+$#', $interfaceName)) {
            throw new WireguardException("Interface name is invalid");
        }

        try {
            $this->process->exec(
                'sudo wg-quick strip "$CONFIG" | sudo tee "$ETC_CONFIG"',
                ['CONFIG' => $configPath, 'ETC_CONFIG' => "/etc/wireguard/$interfaceName.conf"]
            );

            $this->process->exec('sudo wg syncconf $INTERFACE "$ETC_CONFIG"',
                ['INTERFACE' => $interfaceName, 'ETC_CONFIG' => "/etc/wireguard/$interfaceName.conf"]
            );
        } catch (ProcessFailedException $e) {
            \Log::channel('wireguard')->error('Process failed', [
                'command' => $e->getProcess()->getCommandLine(),
                'output' => $e->getProcess()->getOutput(),
                'errorOutput' => $e->getProcess()->getErrorOutput(),
            ]);

            throw new WireguardException('Unable to synchronize config', $e->getCode(), $e->getPrevious());
        } catch (\Throwable $e) {
            \Log::error($e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            throw new WireguardException('Unable to synchronize config', $e->getCode(), $e->getPrevious());
        }
    }

    /**
     * @return array<int, string>
     * @throws \App\Exceptions\Exception\WireguardException
     * @throws \App\Exceptions\Exception\WireguardNoSuchDeviceException
     */
    public function getDump(string $interfaceName): array
    {
        if (config('app.env') !== 'production') {
            \Log::channel('wireguard')->info('WireGuard is not in production. Return mock.');

            return [];
        }

        try {
            $dumpString = $this->process->exec('sudo wg show $INTERFACE dump', ['INTERFACE' => $interfaceName]);
        } catch (ProcessFailedException $e) {
            if (str_contains($e->getMessage(), 'No such device')) {
                throw new WireguardNoSuchDeviceException("No such device {$interfaceName}");
            }

            \Log::channel('wireguard')->error('Process failed', [
                'command' => $e->getProcess()->getCommandLine(),
                'output' => $e->getProcess()->getOutput(),
                'errorOutput' => $e->getProcess()->getErrorOutput(),
            ]);

            throw new WireguardException('Unable get interface dump', $e->getCode(), $e->getPrevious());
        }  catch (\Throwable $e) {
            \Log::error($e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            throw new WireguardException('Unable get interface dump', $e->getCode(), $e->getPrevious());
        }

        return array_slice(explode(PHP_EOL, $dumpString), 1);
    }
}
