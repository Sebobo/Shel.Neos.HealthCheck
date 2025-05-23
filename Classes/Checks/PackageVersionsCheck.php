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

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Core\Bootstrap;
use Neos\Flow\Package\Exception\UnknownPackageException;
use Neos\Flow\Package\PackageManager;

class PackageVersionsCheck implements HealthCheckInterface
{
    #[Flow\InjectConfiguration('checks.packageVersions.include', 'Shel.Neos.HealthCheck')]
    protected ?array $includedPackages = [];

    #[Flow\Inject]
    protected PackageManager $packageManager;

    public function getName(): string
    {
        return 'packageVersions';
    }

    public function getValue(): array
    {
        $result = [];
        foreach ($this->includedPackages as $packageName) {
            try {
                $package = $this->packageManager->getPackage($packageName);
                $result[$packageName] = $package->getInstalledVersion();
            } catch (UnknownPackageException $e) {
                $result[$packageName] = $e->getMessage();
                continue;
            }
        }
        return $result;
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
