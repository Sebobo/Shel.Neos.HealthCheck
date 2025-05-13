<?php

declare(strict_types=1);

namespace Shel\Neos\HealthCheck\Checks;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Core\Bootstrap;

#[Flow\Proxy(false)]
class ReleaseDateCheck implements HealthCheckInterface
{
    public function getName(): string
    {
        return 'releaseDate';
    }

    public function getValue(): string
    {
        return (string)Bootstrap::getEnvironmentConfigurationSetting('FLOW_RELEASE_DATE');
    }

    public function isError(): bool
    {
        return false;
    }

    public function isWarning(): bool
    {
        return false;
    }
}
