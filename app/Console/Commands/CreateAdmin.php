<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ServerService;
use Illuminate\Console\Command;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates admin user';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ServerService $service): int
    {
        $name = $this->ask('Enter admin name');
        $email = $this->ask('Enter admin email');

        $password = $this->secret('Enter password');
        $passwordConfirm = $this->secret('Confirm password');

        if ($password !== $passwordConfirm) {
            $this->error('Password does not match');

            return 1;
        }

        $user = \DB::transaction(static function () use ($service, $name, $email, $password) {
            $user = User::create([
                'name' => $name,
                'email' => $email,
                'password' => \Hash::make($password),
                'is_admin' => true
            ]);

            $service->create($user, ['address' => '10.8.0.1', 'port' => 51820]);

            return $user;
        });

        if (null === $user) {
            $this->error('User creation failed');

            return 1;
        }

        $this->info('User successfully created');

        return 0;
    }
}
