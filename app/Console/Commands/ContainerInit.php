<?php

namespace App\Console\Commands;

use App\Models\Server;
use App\Services\ServerService;
use App\Services\WireguardService;
use Illuminate\Console\Command;

class ContainerInit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'container:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initialize a container';

    public function handle(ServerService $serverService): int
    {
        try {
            Server::each(static function (Server $server) use ($serverService) {
                $serverService->updateConfig($server);
            }, 100);

            $this->getOutput()->info('Success initialized!');
        } catch (\Throwable $e) {
            \Log::error($e->getMessage());

            $this->getOutput()->error('Unable to initialize! See logs to more details.');
        }

        return 0;
    }
}
