<?php

declare(strict_types=1);

namespace Shel\Neos\HealthCheck\Checks;

interface HealthCheckInterface
{
    public function getName(): string;

    public function getValue(): \JsonSerializable|array|string|int|float|null;

    public function isError(): bool;

    public function isWarning(): bool;
}
