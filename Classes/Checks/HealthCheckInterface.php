<?php

declare(strict_types=1);

namespace Shel\Neos\HealthCheck\Checks;

/**
 * This file is part of the Shel.Neos.HealthCheck package.
 *
 * (c) 2025 Sebastian Helzle
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

interface HealthCheckInterface
{
    public function getName(): string;

    public function getValue(): \JsonSerializable|array|string|int|float|null;

    public function isError(): bool;

    public function isWarning(): bool;
}
