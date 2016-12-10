<?php

namespace spec\Health;

use Health\Health;
use Health\Service;
use Health\Status\MinimalStatus;
use Health\Summary;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class HealthSpec extends ObjectBehavior
{
    private $startup;

    public function it_is_initializable()
    {
        $this->shouldHaveType(Health::class);
    }

    public function let()
    {
        $this->startup = new \DateTimeImmutable();
        $this->beConstructedWith('test', 'localhost', '1.2.3', $this->startup);
    }

    public function it_checks_status_of_aggregated_services(Service $service)
    {
        $service->name()->willReturn('test-service');
        $service->status()->willReturn(new MinimalStatus('postgres', true, true, 0.001));

        $this->addService('test-services', $service);
        $this->status()->shouldReturnAnInstanceOf(Summary::class);
    }
}
