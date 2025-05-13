<?php

declare(strict_types=1);

namespace Shel\Neos\HealthCheck\Checks;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Core\Bootstrap;

#[Flow\Proxy(false)]
class ReleaseVersionCheck implements HealthCheckInterface
{
    public function getName(): string
    {
        return 'releaseVersion';
    }

    public function getValue(): string
    {
        return (string)Bootstrap::getEnvironmentConfigurationSetting('FLOW_RELEASE');
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
