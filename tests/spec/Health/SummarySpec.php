<?php

namespace spec\Health;

use Health\Status\MinimalStatus;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class SummarySpec extends ObjectBehavior
{
    public function let()
    {
        $services = [
            'database' => [
                new MinimalStatus('postgres', true, true, 0.01, null),
                new MinimalStatus('legacy-db', false, false, null, 'Can\'t connect')
            ]
        ];

        $this->beConstructedWith(
            'local-service',
            'localhost',
            '1.2.3',
            $services,
            new \DateTimeImmutable('2016-11-07T17:21:38+00:00'),
            new \DateTimeImmutable('2016-12-07T11:30:41+00:00')
        );
    }

    public function it_knows_startup_time()
    {
        $this->startup()->shouldReturn('2016-11-07T17:21:38+00:00');
    }

    public function it_knows_uptime()
    {
        $this->uptime()->shouldReturn('29 days, 18:09:03');
    }

    public function it_knows_datetime_at_creation()
    {
        $this->datetime()->shouldReturn('2016-12-07T11:30:41+00:00');
    }

    public function it_lists_groups_with_services()
    {
        $this->groups()->shouldReturn(
            [
                'database' => [
                    [
                        'name' => 'postgres',
                        'health' => true,
                        'essential' => true,
                        'latency' => 0.01,
                        'error' => null
                    ],
                    [
                        'name' => 'legacy-db',
                        'health' => false,
                        'essential' => false,
                        'latency' => null,
                        'error' => 'Can\'t connect',
                    ]
                ]
            ]
        );
    }

    public function it_lists_only_groups_with_essential_services()
    {
        $this->groups(true)->shouldReturn(
            [
                'database' => [
                    [
                        'name' => 'postgres',
                        'health' => true,
                        'essential' => true,
                        'latency' => 0.01,
                        'error' => null
                    ]
                ]
            ]
        );
    }

    public function it_lists_group_services()
    {
        $this->group('database')->shouldReturn(
            [
                [
                    'name' => 'postgres',
                    'health' => true,
                    'essential' => true,
                    'latency' => 0.01,
                    'error' => null
                ],
                [
                    'name' => 'legacy-db',
                    'health' => false,
                    'essential' => false,
                    'latency' => null,
                    'error' => 'Can\'t connect',
                ]
            ]
        );
    }

    public function it_lists_groups_essential_services()
    {
        $this->group('database', true)->shouldReturn(
            [
                [
                    'name' => 'postgres',
                    'health' => true,
                    'essential' => true,
                    'latency' => 0.01,
                    'error' => null
                ]
            ]
        );
    }

    public function it_knows_if_it_is_healthy()
    {
        $this->isHealthy()->shouldReturn(false);
    }

    public function it_knows_if_essential_services_are_healthy()
    {
        $this->isHealthy(true)->shouldReturn(true);
    }

    public function it_shows_summary()
    {
        $this->summary()->shouldReturn(
            [
                'application' => [
                    'name' => 'local-service',
                    'hostname' => 'localhost',
                    'version' => '1.2.3',
                    'startup' => '2016-11-07T17:21:38+00:00',
                    'uptime' => '29 days, 18:09:03',
                    'datetime' => '2016-12-07T11:30:41+00:00',
                ],
                'health' => ['database' => false],
                'database' => [
                    [
                        'name' => 'postgres',
                        'health' => true,
                        'essential' => true,
                        'latency' => 0.01,
                        'error' => null
                    ],
                    [
                        'name' => 'legacy-db',
                        'health' => false,
                        'essential' => false,
                        'latency' => null,
                        'error' => 'Can\'t connect',
                    ]
                ]
            ]
        );
    }
}
