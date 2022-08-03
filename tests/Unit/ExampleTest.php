<?php

namespace Tests\Unit;

use App\Helpers\Process;
use App\Services\WireguardService;
use PHPUnit\Framework\TestCase;
use \Mockery;

class ExampleTest extends TestCase
{
    protected Process $processHelper;

    public function setUp(): void
    {
        $this->processHelper = Mockery::mock(Process::class, static function (Mockery\MockInterface $mock) {
            $mock->shouldReceive('exec')->andReturn(
                trim(file_get_contents(__DIR__ . '/../Mocks/wgDump'))
            );
        });
    }

    public function testDumpClientInfo(): void
    {
        $service = new WireguardService($this->processHelper);

        dd($service->getDump('wg0'));
    }
}
