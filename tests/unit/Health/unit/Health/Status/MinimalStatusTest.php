<?php

declare(strict_types = 1);

namespace unit\Health\Status;

use Health\Status;
use Health\Status\MinimalStatus;

class MinimalStatusTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Status
     */
    private $status;

    public function setUp()
    {
        $this->status = new MinimalStatus('redis', true, false, 0.001, null);
    }

    public function testName()
    {
        $this->assertEquals($this->status->name(), 'redis');
    }

    public function testIsHealthy()
    {
        $this->assertTrue($this->status->isHealthy());
    }

    public function testIsEssential()
    {
        $this->assertFalse($this->status->isEssential());
    }

    public function testLatency()
    {
        $this->assertEquals($this->status->latency(), 0.001);
    }

    public function testError()
    {
        $this->assertNull($this->status->error());
    }

    public function testSummary()
    {
        $this->assertEquals(
            $this->status->summary(),
            [
                'name' => 'redis',
                'health' => true,
                'essential' => false,
                'latency' => 0.001,
                'error' => null
            ]
        );
    }
}
