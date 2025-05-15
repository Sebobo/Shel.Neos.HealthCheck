<?php

declare(strict_types=1);

namespace Shel\Neos\HealthCheck\Controller;

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
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Mvc\View\JsonView;
use Shel\Neos\HealthCheck\Service\HealthCheckService;

class HealthCheckController extends ActionController
{
    protected $defaultViewObjectName = JsonView::class;

    #[Flow\Inject]
    protected HealthCheckService $healthCheckService;

    public function statusAction(): void
    {
        $this->view->assign('value', $this->healthCheckService->getStatus());
    }
}
