<?php

namespace Database\Factories;

use App\Models\Server;
use App\Services\WireguardService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Server>
 */
class ServerFactory extends Factory
{
    protected $model = Server::class;

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
            'private_key' => \Crypt::encryptString($privateKey),
            'public_key' => $service->generatePublicKey($privateKey),
            'address' => $this->faker->ipv4(),
            'port' => $this->faker->numberBetween(50000, 58000),
        ];
    }
}
