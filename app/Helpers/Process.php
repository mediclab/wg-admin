<?php

declare(strict_types=1);

namespace App\Helpers;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process as SymfonyProcess;

class Process
{
    public function exec(string $command, array $envs = []): string
    {
        $process = SymfonyProcess::fromShellCommandline($command);
        $process->run(null, $envs);

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return trim($process->getOutput());
    }
}
