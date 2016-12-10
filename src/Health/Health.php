<?php

declare (strict_types = 1);

namespace Health;

final class Health
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var \DateTimeImmutable
     */
    private $startup;

    /**
     * @var string
     */
    private $hostname;

    /**
     * @var string
     */
    private $version;

    /**
     * @var array
     */
    private $serviceGroups = [];

    /**
     * Health constructor.
     *
     * @param string             $name
     * @param string             $hostname
     * @param string             $version
     * @param \DateTimeImmutable $startup
     */
    public function __construct(string $name, string $hostname, string $version, \DateTimeImmutable $startup)
    {
        $this->name = $name;
        $this->hostname = $hostname;
        $this->version = $version;
        $this->startup = $startup;
    }

    /**
     * Add service to a group for monitoring
     *
     * @param string  $group
     * @param Service $service
     */
    public function addService(string $group, Service $service): void
    {
        if (!array_key_exists($group, $this->serviceGroups)) {
            $this->serviceGroups[$group] = [];
        }

        $this->serviceGroups[$group][] = $service;
    }

    /**
     * Grab service health snapshot
     *
     * @return Summary
     */
    public function status(): Summary
    {
        $summaries = [];
        foreach ($this->serviceGroups as $name => $group) {
            $summaries[$name] = $this->groupStatus($group);
        }

        return new Summary(
            $this->name,
            $this->hostname,
            $this->version,
            $summaries,
            $this->startup,
            new \DateTimeImmutable()
        );
    }

    /**
     * @param Service[] $services
     *
     * @return array
     */
    private function groupStatus(array $services): array
    {
        $result = [];
        foreach ($services as $service) {
            $result[] = $service->status();
        }

        return $result;
    }
}
