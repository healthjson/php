<?php

declare (strict_types = 1);

namespace Health;

final class Summary
{
    /**
     * @var string
     */
    private $name;

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
    private $serviceGroups;

    /**
     * @var \DateTimeInterface
     */
    private $startup;

    /**
     * @var \DateTimeImmutable
     */
    private $current;

    public function __construct(
        string $name,
        string $hostname,
        string $version,
        array $services,
        \DateTimeImmutable $startup,
        \DateTimeImmutable $current
    ) {
        $this->assertServices($services);

        $this->name = $name;
        $this->hostname = $hostname;
        $this->version = $version;
        $this->serviceGroups = $services;
        $this->startup = $startup->setTimezone(new \DateTimeZone('UTC'));
        $this->current = $current->setTimezone(new \DateTimeZone('UTC'));
    }

    /**
     * @param array $groups
     *
     * @throws \TypeError
     */
    private function assertServices(array $groups)
    {
        foreach ($groups as $services) {
            foreach ($services as $service) {
                if (!$service instanceof Status) {
                    throw new \TypeError(sprintf('Service must be instance of %s', Status::class));
                }
            }
        }
    }

    /**
     * Application name
     *
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Application host name
     *
     * @return string
     */
    public function hostname(): string
    {
        return $this->hostname;
    }

    /**
     * Application running version
     *
     * @return string
     */
    public function version(): string
    {
        return $this->version;
    }

    /**
     * Date of deployment
     *
     * @return string
     */
    public function startup(): string
    {
        return $this->startup->format('c');
    }

    /**
     * Time since last deployment
     *
     * @return string
     */
    public function uptime(): string
    {
        $diff = $this->current->diff($this->startup);

        if ($diff->days === 1) {
            return $diff->format('%a day, %H:%I:%S');
        }

        return $diff->format('%a days, %H:%I:%S');
    }

    /**
     * Current date time
     *
     * @return string
     */
    public function datetime(): string
    {
        return $this->current->format('c');
    }

    /**
     * All services summaries
     *
     * @param bool $essentialOnly filter essential services only
     *
     * @return array
     */
    public function groups(bool $essentialOnly = false): array
    {
        $result = [];
        foreach ($this->serviceGroups as $name => $services) {
            $result[$name] = $this->groupSummary($services, $essentialOnly);
        }

        return $result;
    }

    /**
     * Summaries of services in group
     *
     * @param string $group
     * @param bool   $essentialOnly filter essential services only
     *
     * @return array
     */
    public function group(string $group, bool $essentialOnly = false): array
    {
        if (!array_key_exists($group, $this->serviceGroups)) {
            return [];
        }

        return $this->groupSummary($this->serviceGroups[$group], $essentialOnly);
    }

    /**
     * @param Status[] $services
     * @param bool     $essentialOnly
     *
     * @return array
     */
    private function groupSummary(array $services, bool $essentialOnly): array
    {
        $result = [];
        foreach ($services as $service) {
            if ($essentialOnly && !$service->isEssential()) {
                continue;
            }

            $result[] = $service->summary();
        }

        return $result;
    }

    /**
     * Application health
     *
     * @param bool $essentialOnly filter essential services only
     *
     * @return bool
     */
    public function isHealthy(bool $essentialOnly = false): bool
    {
        foreach ($this->serviceGroups as $services) {
            if (!$this->isGroupHealthy($services, $essentialOnly)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param Status[] $services
     * @param bool     $essentialOnly
     *
     * @return bool
     */
    private function isGroupHealthy(array $services, bool $essentialOnly): bool
    {
        foreach ($services as $service) {
            if ($essentialOnly && !$service->isEssential()) {
                continue;
            }

            if (!$service->isHealthy()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Summary
     *
     * @return array
     */
    public function summary(): array
    {
        $result = [
            'application' => [
                'name' => $this->name(),
                'hostname' => $this->hostname(),
                'version' => $this->version(),
                'startup' => $this->startup(),
                'uptime' => $this->uptime(),
                'datetime' => $this->datetime()
            ],
            'health' => [],
        ];

        foreach ($this->serviceGroups as $group => $services) {
            $result['health'][$group] = $this->isGroupHealthy($services, false);
            $result[$group] = $this->groupSummary($services, false);
        }

        return $result;
    }
}
