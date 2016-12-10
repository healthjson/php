<?php

declare(strict_types = 1);

namespace unit\Health;

use Health\Health;
use Health\Service\CallableService;

class HealthTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \DateTimeImmutable
     */
    private $startup;

    /**
     * @var Health
     */
    private $health;

    public function setUp()
    {
        $this->startup = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->health = new Health('service', 'host.com', '1.2.3', $this->startup);
    }

    public function testSummaryIncludesVars()
    {
        $this->assertEquals($this->health->status()->name(), 'service');
        $this->assertEquals($this->health->status()->hostname(), 'host.com');
        $this->assertEquals($this->health->status()->version(), '1.2.3');
        $this->assertEquals($this->health->status()->startup(), $this->startup->format('c'));
    }

    public function testAddedServiceIsIncludedInSummary()
    {
        $this->health->addService('database', new CallableService('postgres', function () { }, true));

        $this->assertEquals(
            $this->health->status()->group('database'),
            [['name' => 'postgres', 'health' => true, 'essential' => true, 'latency' => 0.0, 'error' => null]]
        );
    }
}
