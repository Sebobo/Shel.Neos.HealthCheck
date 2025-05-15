<?php

declare(strict_types=1);

namespace Shel\Neos\HealthCheck\Service;

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
use Neos\Flow\Http\Client\Browser;
use Neos\Flow\Http\Client\CurlEngine;
use Neos\Flow\Http\Client\InfiniteRedirectionException;
use Neos\Flow\Reflection\ReflectionService;
use Shel\Neos\HealthCheck\Checks\HealthCheckInterface;

#[Flow\Scope('singleton')]
class HealthCheckService
{

    #[Flow\Inject]
    protected ReflectionService $reflectionService;

    /**
     * @var array<string, {label: string, icon: string, url: string}>[]
     */
    #[Flow\InjectConfiguration('environments', 'Shel.Neos.HealthCheck')]
    protected array $environments = [];

    /**
     * @return array{status: string, message: string, timestamp: int, ...}
     */
    public function getStatus(): array
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

        return [
            'status' => match (true) {
                $hasErrors => 'error',
                $hasWarnings => 'warning',
                default => 'ok',
            },
            'message' => $hasErrors ? 'The system is not healthy.' : 'The system is healthy.',
            'timestamp' => time(),
            ...$checkResults,
        ];
    }

    /**
     * @return array{status: string, message: string, timestamp: int, ...}
     */
    public function getEnvironmentHealth(string $environmentName): array
    {
        $browser = $this->getBrowser();
        $environment = $this->environments[$environmentName] ?? null;

        if (!$environment) {
            return [
                'status' => 'error',
                'message' => 'Environment "' . $environmentName . '" not found',
                'timestamp' => time(),
            ];
        }

        // Fetch health check data from the environment URL
        $browser->addAutomaticRequestHeader('Content-Type', 'application/json');
        if ($environment['headers'] ?? false) {
            foreach ($environment['headers'] as ['name' => $name, 'value' => $value]) {
                $browser->addAutomaticRequestHeader($name, $value);
            }
        }
        try {
            $response = $browser->request($environment['url']);
            $content = $response->getBody()->getContents();
            return json_decode(
                $content,
                true,
                512,
                JSON_THROW_ON_ERROR
            );
        } catch (\JsonException $e) {
            return [
                'status' => 'error',
                'message' => 'Failed to parse response: ' . $e->getMessage(),
                'timestamp' => time(),
            ];
        } catch (InfiniteRedirectionException) {
            return [
                'status' => 'error',
                'message' => 'Infinite redirection detected',
                'timestamp' => time(),
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Request failed: ' . $e->getMessage(),
                'timestamp' => time(),
            ];
        }
    }

    protected function getBrowser(): Browser
    {
        $browser = new Browser();
        $browser->setRequestEngine(new CurlEngine());
        return $browser;
    }
}
