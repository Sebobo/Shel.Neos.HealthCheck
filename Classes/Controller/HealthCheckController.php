<?php

declare(strict_types=1);

namespace Shel\Neos\HealthCheck\Controller;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Mvc\Controller\ActionController;
use Neos\Flow\Mvc\View\JsonView;
use Neos\Flow\Reflection\ReflectionService;
use Shel\Neos\HealthCheck\Checks\HealthCheckInterface;

class HealthCheckController extends ActionController
{
    protected $defaultViewObjectName = JsonView::class;

    public function statusAction(): void
    {
        $checks = $this->reflectionService->getAllImplementationClassNamesForInterface(HealthCheckInterface::class);

        $hasErrors = false;
        $hasWarnings = false;
        $checkResults = [];
        foreach ($checks as $checkClassName) {
            /** @var HealthCheckInterface $check */
            $check = new $checkClassName();
            if ($check->isError()) {
                $hasErrors = true;
            }
            if ($check->isWarning()) {
                $hasWarnings = true;
            }
            $checkResults[$check->getName()] = $check->getValue();
        }

        $this->view->assign('value', [
            'status' => match (true) {
                $hasErrors => 'error',
                $hasWarnings => 'warning',
                default => 'ok',
            },
            'message' => $hasErrors ? 'The system is not healthy.' : 'The system is healthy.',
            'timestamp' => time(),
            ...$checkResults,
        ]);
    }
}
