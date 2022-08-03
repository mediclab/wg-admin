<?php

declare(strict_types=1);

return [
    'host' => env('WG_HOST', '127.0.0.1'),
    'allowed_ports' => explode('-', env('WG_ALLOWED_PORTS', '51800-52000')),
    'allowed_subnet' => env('WG_ALLOWED_SUBNETS', '10.0.0.0/8'),
    'default_dns_1' => env('WG_DEFAULT_DNS_1', '8.8.8.8'),
    'default_dns_2' => env('WG_DEFAULT_DNS_2', '8.8.4.4'),
];
