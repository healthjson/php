<?php

declare (strict_types = 1);

namespace unit\Health;

use Health\Service;
use Health\Service\CallableService;
use Health\Status\MinimalStatus;
use Health\Summary;

class SummaryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \DateTimeImmutable
     */
    private $startup;

    /**
     * @var \DateTimeImmutable
     */
    private $current;

    /**
     * @var Service[]
     */
    private $services;

    /**
     * @var Summary
     */
    private $summary;

    public function setUp()
    {
        $this->startup = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->sub(new \DateInterval('P2DT12H'));
        $this->current = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $this->services = [
            'database' => [
                new MinimalStatus('postgres', true, true, 0.001, null),
                new MinimalStatus('legacy', false, false, null, 'Forced')
            ]
        ];

        $this->summary = new Summary('application', 'host.com', '1.2.3', [], $this->startup, $this->current);
    }

    public function testName()
    {
        $this->assertEquals($this->summary->name(), 'application');
    }

    public function testHostname()
    {
        $this->assertEquals($this->summary->hostname(), 'host.com');
    }

    public function testVersion()
    {
        $this->assertEquals($this->summary->version(), '1.2.3');
    }

    public function testStartupTime()
    {
        $this->assertEquals($this->summary->startup(), $this->startup->format('c'));
    }

    public function testUpTimeLessThan1Day()
    {
        $startup = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->sub(new \DateInterval('PT12H'));
        $current = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $summary = new Summary('application', 'host.com', '1.2.3', [], $startup, $current);

        $this->assertEquals($summary->uptime(), '0 days, 12:00:00');
    }

    public function testUpTimeLessThan2Days()
    {
        $startup = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->sub(new \DateInterval('P1DT12H'));
        $current = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $summary = new Summary('application', 'host.com', '1.2.3', [], $startup, $current);

        $this->assertEquals($summary->uptime(), '1 day, 12:00:00');
    }

    public function testUpTimeMoreThan2Days()
    {
        $startup = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))->sub(new \DateInterval('P2DT12H'));
        $current = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
        $summary = new Summary('application', 'host.com', '1.2.3', [], $startup, $current);

        $this->assertEquals($summary->uptime(), '2 days, 12:00:00');
    }

    public function testCurrentTime()
    {
        $this->assertEquals($this->summary->datetime(), $this->current->format('c'));
    }

    public function testGroupSummaryWithoutServices()
    {
        $this->assertEquals($this->summary->groups(), []);
    }

    public function testGroupsSummary()
    {
        $summary = new Summary('application', 'host.com', '1.2.3', $this->services, $this->startup, $this->current);
        $this->assertEquals(
            $summary->groups(),
            [
                'database' => [
                    [
                        'name' => 'postgres',
                        'health' => true,
                        'essential' => true,
                        'latency' => 0.001,
                        'error' => null
                    ],
                    [
                        'name' => 'legacy',
                        'health' => false,
                        'essential' => false,
                        'latency' => null,
                        'error' => 'Forced'
                    ]
                ]
            ]
        );
    }

    public function testGroupsEssentialSummary()
    {
        $summary = new Summary('application', 'host.com', '1.2.3', $this->services, $this->startup, $this->current);
        $this->assertEquals(
            $summary->groups(true),
            [
                'database' => [
                    [
                        'name' => 'postgres',
                        'health' => true,
                        'essential' => true,
                        'latency' => 0.001,
                        'error' => null
                    ]
                ]
            ]
        );
    }

    public function testEmptyGroupSummary()
    {
        $this->assertEquals($this->summary->group('does-not-exist'), []);
    }

    public function testGroupSummary()
    {
        $summary = new Summary('application', 'host.com', '1.2.3', $this->services, $this->startup, $this->current);
        $this->assertEquals(
            $summary->group('database'),
            [
                [
                    'name' => 'postgres',
                    'health' => true,
                    'essential' => true,
                    'latency' => 0.001,
                    'error' => null
                ],
                [
                    'name' => 'legacy',
                    'health' => false,
                    'essential' => false,
                    'latency' => null,
                    'error' => 'Forced'
                ]
            ]
        );
    }

    public function testGroupEssentialSummary()
    {
        $summary = new Summary('application', 'host.com', '1.2.3', $this->services, $this->startup, $this->current);
        $this->assertEquals(
            $summary->group('database', true),
            [
                [
                    'name' => 'postgres',
                    'health' => true,
                    'essential' => true,
                    'latency' => 0.001,
                    'error' => null
                ]
            ]
        );
    }

    public function testIsHealthy()
    {
        $summary = new Summary('application', 'host.com', '1.2.3', $this->services, $this->startup, $this->current);

        $this->assertFalse($summary->isHealthy());
    }

    public function testIsHealthyWithOnlyEssentials()
    {
        $summary = new Summary('application', 'host.com', '1.2.3', $this->services, $this->startup, $this->current);

        $this->assertTrue($summary->isHealthy(true));
    }

    public function testIsHealthyWithoutServices()
    {
        $this->assertTrue($this->summary->isHealthy());
    }

    public function testSummaryApplication()
    {
        $this->assertEquals(
            $this->summary->summary()['application'],
            [
                'name' => 'application',
                'hostname' => 'host.com',
                'version' => '1.2.3',
                'startup' => $this->startup->format('c'),
                'uptime' => '2 days, 12:00:00',
                'datetime' => $this->current->format('c')
            ]
        );
    }

    public function testSummaryGroupsHealth()
    {
        $summary = new Summary('application', 'host.com', '1.2.3', $this->services, $this->startup, $this->current);

        $this->assertEquals($summary->summary()['health'], ['database' => false]);
    }

    public function testSummaryGroups()
    {
        $summary = new Summary('application', 'host.com', '1.2.3', $this->services, $this->startup, $this->current);

        $this->assertEquals(
            $summary->summary()['database'],
            [
                [
                    'name' => 'postgres',
                    'health' => true,
                    'essential' => true,
                    'latency' => 0.001,
                    'error' => null
                ],
                [
                    'name' => 'legacy',
                    'health' => false,
                    'essential' => false,
                    'latency' => null,
                    'error' => 'Forced'
                ]
            ]
        );
    }
}
