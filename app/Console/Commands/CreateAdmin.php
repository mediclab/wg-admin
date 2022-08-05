<?php

namespace App\Console\Commands;

use App\Helpers\Util;
use App\Models\User;
use App\Services\ServerService;
use App\Validators\ServerValidator;
use App\Validators\UserValidator;
use Illuminate\Console\Command;
use Illuminate\Validation\ValidationException;

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
        $ports = Util::getAllowedPorts();
        $addresses = Util::getAllowedAddresses();

        $this->info(__('Allowed server subnet:') . " {$addresses['subnet']}");
        $this->info(__('Minimal port:') . " {$ports['min']}");
        $this->info(__('Maximum port:') . " {$ports['max']}");

        do {
            $userData = [
                'name' => $this->ask(__('Enter admin name')),
                'email' => $this->ask(__('Enter admin email')),
                'password' => $this->secret(__('Enter password')),
                'password_confirmation' => $this->secret(__('Confirm password')),
            ];

            $validator = UserValidator::getValidator($userData);

            try {
                $validator->validate();
            } catch (ValidationException $e) {
                $this->error('Validation errors:');

                foreach ($e->errors() as $errors) {
                    $this->warn(implode(PHP_EOL, $errors));
                }
            }

        } while ($validator->fails());

        do {
            $serverData = [
                'address' => $this->ask(__('Enter user server address')),
                'port' => $this->ask(__('Enter user server port')),
            ];

            $validator = ServerValidator::getValidator($serverData, $addresses, $ports);

            try {
                $validator->validate();
            } catch (ValidationException $e) {
                $this->error('Validation errors:');

                foreach ($e->errors() as $errors) {
                    $this->warn(implode(PHP_EOL, $errors));
                }
            }

        } while ($validator->fails());

        $user = \DB::transaction(static function () use ($service, $userData, $serverData) {
            $userData['password'] = \Hash::make($userData['password']);

            $user = User::create($userData + ['is_admin' => true]);
            $service->create($user, $serverData);

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
