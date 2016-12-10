<?php

declare (strict_types = 1);

namespace Health;

interface Service
{
    /**
     * Service name
     *
     * @return string
     */
    public function name(): string;

    /**
     * Service status
     *
     * @return Status
     */
    public function status(): Status;
}
