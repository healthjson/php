<?php

namespace spec\Health\Service;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CallableServiceSpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith('callback-service', function() { }, true);
    }

    public function it_has_name()
    {
        $this->name()->shouldReturn('callback-service');
    }

    public function it_checks_its_status()
    {
        $this->status()->name()->shouldReturn('callback-service');
        $this->status()->isHealthy()->shouldReturn(true);
        $this->status()->isEssential()->shouldReturn(true);
        $this->status()->latency()->shouldReturn(0.0000);
        $this->status()->error()->shouldReturn(null);
    }
}
