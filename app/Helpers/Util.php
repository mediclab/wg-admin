<?php

declare(strict_types=1);

namespace App\Helpers;

use IPv4\SubnetCalculator;

class Util
{
    public static function bytesToHuman($bytes): string
    {
        $units = [__('B'), __('KiB'), __('MiB'), __('GiB'), __('TiB'), __('PiB')];

        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    public static function getAllowedPorts(): array
    {
        $ports = config('wg.allowed_ports', [50000, 55000]);

        if (!is_array($ports) || !(count($ports) === 2)) {
            \Log::warning('Environment "WG_ALLOWED_PORTS" is invalid. Using standard 50000-55000');
            $ports = [50000, 55000];
        }

        $ports = array_combine(['min', 'max'], $ports);

        $portsValidator = \Validator::make($ports, [
            'min' => 'required|int|min:10000',
            'max' => 'required|int|max:60000',
        ]);

        if ($portsValidator->fails()) {
            \Log::warning('Environment "WG_ALLOWED_PORTS" is invalid. Using standard 50000-55000');
            $ports = ['min' => 50000, 'max' => 55000];
        }

        return $ports;
    }

    public static function getAllowedAddresses(): array
    {
        $subnet = explode('/', config('wg.allowed_subnet', '10.0.0.0/8'));

        if (!is_array($subnet) || !(count($subnet) === 2)) {
            \Log::warning('Environment "WG_ALLOWED_SUBNET" is invalid. Using standard 10.0.0.0/8');
            $subnet = ['10.0.0.0', 8];
        }

        $subnet = array_combine(['ip', 'mask'], $subnet);

        $subnetValidator = \Validator::make($subnet, [
            'ip' => 'required|ipv4',
            'mask' => 'required|int|max:30|min:8',
        ]);

        if ($subnetValidator->fails()) {
            \Log::warning('Environment "WG_ALLOWED_SUBNET" is invalid. Using standard 10.0.0.0/8');
            $subnet = ['ip' => '10.0.0.0', 'mask' => 8];
        }

        $subnetCalc = new SubnetCalculator($subnet['ip'], (int) $subnet['mask']);

        return [
            'min' => $subnetCalc->getMinHost(),
            'max' => $subnetCalc->getMaxHost(),
            'subnet' => "{$subnet['ip']}/{$subnet['mask']}",
        ];
    }
}
