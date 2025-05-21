<?php

declare(strict_types=1);

namespace Shel\Neos\HealthCheck\Security;

/**
 * This file is part of the Shel.Neos.HealthCheck package.
 *
 * (c) 2025 Sebastian Helzle
 *
 * This package is Open Source Software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Mvc\ActionRequest;
use Neos\Flow\Security\Authentication\Token\AbstractToken;
use Neos\Flow\Security\Authentication\Token\SessionlessTokenInterface;
use Neos\Flow\Security\Exception\InvalidAuthenticationStatusException;

/**
 * A Flow security token that authenticates based on a hash delivered without starting a session.
 */
class Token extends AbstractToken implements SessionlessTokenInterface
{
    protected $credentials;

    /**
     * @throws InvalidAuthenticationStatusException
     */
    public function updateCredentials(ActionRequest $actionRequest): void
    {
        $authenticationHashToken = $actionRequest->getHttpRequest()->getQueryParams()['token'] ?? null;
        if ($authenticationHashToken === null) {
            $authorizationHeader = $actionRequest->getHttpRequest()->getHeaderLine('Authorization');
            if (strncmp($authorizationHeader, 'Bearer ', 7) === 0) {
                $authenticationHashToken = substr($authorizationHeader, 7);
            }
        }
        if ($authenticationHashToken !== null) {
            $this->credentials['password'] = $authenticationHashToken;
            $this->setAuthenticationStatus(self::AUTHENTICATION_NEEDED);
        }
    }
}
