<?php

declare(strict_types=1);

namespace Shel\Neos\HealthCheck\DataSource;

/**
 * This file is part of the Shel.Neos.HealthCheck package.
 *
 * (c) 2025 Sebastian Helzle
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\ContentRepository\Domain\Model\NodeInterface;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Security\Authorization\PrivilegeManagerInterface;
use Neos\Flow\Security\Context;
use Neos\Neos\Service\DataSource\AbstractDataSource;
use Shel\Neos\HealthCheck\Service\HealthCheckService;

class EnvironmentsDataSource extends AbstractDataSource
{

    /**
     * @var array<string, {label: string, icon: string, url: string}>[]
     */
    #[Flow\InjectConfiguration('environments', 'Shel.Neos.HealthCheck')]
    protected array $environments = [];

    #[Flow\Inject]
    protected HealthCheckService $healthCheckService;

    #[Flow\Inject]
    protected Context $securityContext;

    /**
     * @var PrivilegeManagerInterface
     */
    #[Flow\Inject]
    protected $privilegeManager;

    /**
     * @var string
     */
    protected static $identifier = 'shel-neos-healthcheck-environments';

    /**
     *
     */
    public function getData(
        NodeInterface $node = null,
        array $arguments = []
    ): array {
        $results = [];

        if (!$this->securityContext->canBeInitialized() || !$this->privilegeManager->isPrivilegeTargetGranted(
                'Shel.Neos.HealthCheck:Status'
            )) {
            return [
                'success' => false,
                'message' => 'You do not have permission to access this data source.',
                'environments' => [],
            ];
        }

        foreach ($this->environments as $environmentName => $environment) {
            if (!is_array($environment)) {
                continue;
            }
            $url = $environment['url'];
            $results[$environmentName] = [
                'label' => $environment['label'],
                'icon' => $environment['icon'],
                'isCurrent' => $url === '<current>',
                'data' => match ($url) {
                    '' => [
                        'status' => 'unavailable',
                    ],
                    '<current>' => $this->healthCheckService->getStatus(),
                    default => $this->healthCheckService->getEnvironmentHealth($environmentName),
                },
            ];
        }

        return [
            'success' => true,
            'message' => '',
            'environments' => $results,
        ];
    }
}
