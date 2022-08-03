<?php

namespace Database\Seeders;

use App\Models\Device;
use App\Models\Server;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        User::factory()
            ->count(20)
            ->has(
                Server::factory()->count(1)->has(
                    Device::factory()->count(5)
                )
            )
            ->create();
    }
}
