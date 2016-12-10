<?php

namespace spec\Health\Status;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class MinimalStatusSpec extends ObjectBehavior
{
    public function let() {
        $this->beConstructedWith('postgres', true, true, 0.001, null);
    }

    public function it_has_name()
    {
        $this->name()->shouldReturn('postgres');
    }

    public function it_knows_if_service_is_healthy()
    {
        $this->isHealthy()->shouldReturn(true);
    }

    public function it_knows_if_service_is_essential()
    {
        $this->isEssential()->shouldReturn(true);
    }

    public function it_knows_what_latency_service_has()
    {
        $this->latency()->shouldReturn(0.001);
    }

    public function it_knows_what_what_error_occurred()
    {
        $this->error()->shouldReturn(null);
    }

    public function it_creates_summary()
    {
        $this->summary()->shouldReturn(
            [
                'name' => 'postgres',
                'health' => true,
                'essential' => true,
                'latency' => 0.001,
                'error' => null
            ]
        );
    }
}
