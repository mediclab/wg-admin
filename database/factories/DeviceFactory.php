<?php

namespace Database\Factories;

use App\Services\WireguardService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Device>
 */
class DeviceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $service = app(WireguardService::class);
        $privateKey = $service->generatePrivateKey();

        return [
            'name' => $this->faker->userName(),
            'private_key' => \Crypt::encryptString($privateKey),
            'public_key' => $service->generatePublicKey($privateKey),
            'preshared_key' => \Crypt::encryptString($service->generatePresharedKey()),
            'address' => $this->faker->ipv4(),
            'keep_alive' => $this->faker->boolean(),
            'is_active' => $this->faker->boolean(),
        ];
    }
}
