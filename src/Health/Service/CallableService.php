<?php

declare (strict_types = 1);

namespace Health\Service;

use Health\Service;
use Health\Status;
use Health\Status\MinimalStatus;

final class CallableService implements Service
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var callable
     */
    private $callback;

    /**
     * @var bool
     */
    private $isEssential;

    /**
     * CallbackService constructor.
     *
     * @param string   $name
     * @param callable $callback
     * @param bool     $isEssential
     */
    public function __construct(string $name, callable $callback, bool $isEssential = true)
    {
        $this->name = $name;
        $this->callback = $callback;
        $this->isEssential = $isEssential;
    }

    /**
     * {@inheritdoc}
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * {@inheritdoc}
     */
    public function status(): Status
    {
        $health = false;
        $latency = null;
        $error = null;

        try {
            $start = microtime(true);
            ($this->callback)();
            $latency = (float) number_format(($start - microtime(true)) / 1000, 6);
            $health = true;
        } catch (\Exception $e) {
            $error = (string) $e;
        }

        return new MinimalStatus($this->name, $health, $this->isEssential, $latency, $error);
    }
}
