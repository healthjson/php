<?php

declare (strict_types = 1);

namespace Health;

interface Status
{
    /**
     * Service name
     *
     * @return string
     */
    public function name(): string;

    /**
     * If service was healthy at snapshot creation
     *
     * @return bool
     */
    public function isHealthy(): bool;

    /**
     * If service is essential
     *
     * @return bool
     */
    public function isEssential(): bool;

    /**
     * Health check latency in ms
     *
     * @return float|null
     */
    public function latency(): ?float;

    /**
     * Error message when unhealthy
     *
     * @return null|string
     */
    public function error(): ?string;

    /**
     * Summary
     *
     * @return array
     */
    public function summary(): array;
}
