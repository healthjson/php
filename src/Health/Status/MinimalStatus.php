<?php

declare (strict_types = 1);

namespace Health\Status;

use Health\Status;

final class MinimalStatus implements Status
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var bool
     */
    private $isHealthy;

    /**
     * @var bool
     */
    private $isEssential;

    /**
     * @var float
     */
    private $latency;

    /**
     * @var string
     */
    private $error;

    public function __construct(
        string $name,
        bool $isHealthy,
        bool $isEssential,
        float $latency = null,
        string $error = null
    ) {
        $this->name = $name;
        $this->isHealthy = $isHealthy;
        $this->isEssential = $isEssential;
        $this->latency = $latency;
        $this->error = $error;
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
    public function isHealthy(): bool
    {
        return $this->isHealthy;
    }

    /**
     * {@inheritdoc}
     */
    public function isEssential(): bool
    {
        return $this->isEssential;
    }

    /**
     * {@inheritdoc}
     */
    public function latency(): ?float
    {
        return $this->latency;
    }

    /**
     * {@inheritdoc}
     */
    public function error(): ?string
    {
        return $this->error;
    }

    /**
     * {@inheritdoc}
     */
    public function summary(): array
    {
        return [
            'name' => $this->name,
            'health' => $this->isHealthy,
            'essential' => $this->isEssential,
            'latency' => $this->latency,
            'error' => $this->error
        ];
    }
}
