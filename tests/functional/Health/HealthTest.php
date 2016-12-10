<?php

declare (strict_types = 1);

namespace functional\Health;

use Health\Health;
use Health\Service\CallableService;
use PhpSpec\Exception\Exception;

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
        $this->startup = (new \DateTimeImmutable())->sub(new \DateInterval('PT1H'));
        $this->health = new Health('local', 'localhost', '1.2.3', $this->startup);
        $this->health->addService(
            'callbacks',
            new CallableService(
                'healthy-calback',
                function () { },
                true
            )
        );
        $this->health->addService(
            'callbacks',
            new CallableService(
                'failing-calback',
                function () { throw new Exception('Forced'); },
                false
            )
        );
    }

    public function testIsHealthy()
    {
        $this->assertFalse($this->health->status()->isHealthy());
    }

    public function testIsHealthyEssentialsOnly()
    {
        $this->assertTrue($this->health->status()->isHealthy(true));
    }

    public function testSummary()
    {
        $summary = $this->health->status()->summary();

        $this->assertEquals($summary['application']['uptime'], '0 days, 01:00:00');

        $this->assertEquals($summary['health'], ['callbacks' => false]);

        $this->assertTrue($summary['callbacks'][0]['health']);
        $this->assertFalse($summary['callbacks'][1]['health']);
    }
}
