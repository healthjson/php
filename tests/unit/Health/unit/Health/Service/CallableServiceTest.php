<?php

declare (strict_types = 1);

namespace unit\Health\Service;

use Health\Service\CallableService;
use PhpSpec\Exception\Exception;

class CallableServiceTest extends \PHPUnit_Framework_TestCase
{
    public function testName()
    {
        $service = new CallableService('postgres', function () { }, false);

        $this->assertEquals($service->name(), 'postgres');
    }

    public function testStatus()
    {
        $service = new CallableService('postgres', function () { }, false);

        $this->assertEquals(
            $service->status()->summary(),
            [
                'name' => 'postgres',
                'health' => true,
                'essential' => false,
                'latency' => 0.0,
                'error' => null
            ]
        );
    }

    public function testHealthyStatus()
    {
        $service = new CallableService('postgres', function () { }, false);

        $this->assertTrue($service->status()->isHealthy());
    }

    public function testUnhealthyStatus()
    {
        $service = new CallableService(
            'postgres',
            function () {
                throw new Exception('Forced');
            },
            false
        );

        $this->assertFalse($service->status()->isHealthy());
    }

    public function testHealthyLatency()
    {
        $service = new CallableService(
            'postgres',
            function () {
                usleep(50000);
            },
            false
        );

        $this->assertGreaterThan($service->status()->latency(), 50);
    }
}
