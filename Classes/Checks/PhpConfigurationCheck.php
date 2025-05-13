<?php

declare(strict_types=1);

namespace Shel\Neos\HealthCheck\Checks;

use Neos\Flow\Annotations as Flow;

/**
 * This check verifies the PHP configuration and returns various configuration details.
 */
class PhpConfigurationCheck implements HealthCheckInterface
{
    #[Flow\InjectConfiguration('checks.phpConfiguration.include', 'Shel.Neos.HealthCheck')]
    protected ?array $includedParameters = [];

    public function getName(): string
    {
        return 'phpConfiguration';
    }

    public function getValue(): array
    {
        return array_reduce(
            $this->includedParameters,
            static function ($acc, $parameter) {
                $acc[$parameter] = ini_get($parameter);
                return $acc;
            },
            []
        );
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
