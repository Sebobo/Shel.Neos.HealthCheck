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

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Configuration\Exception\InvalidConfigurationTypeException;
use Neos\Flow\Security\Account;
use Neos\Flow\Security\Authentication\Provider\AbstractProvider;
use Neos\Flow\Security\Authentication\TokenInterface;
use Neos\Flow\Security\Exception;
use Neos\Flow\Security\Exception\InvalidAuthenticationStatusException;
use Neos\Flow\Security\Exception\NoSuchRoleException;
use Neos\Flow\Security\Exception\UnsupportedAuthenticationTokenException;
use Neos\Flow\Security\Policy\PolicyService;

#[Flow\Scope("singleton")]
class TokenProvider extends AbstractProvider
{
    #[Flow\InjectConfiguration('token')]
    protected ?string $token = null;

    #[Flow\Inject]
    protected PolicyService $policyService;

    /**
     * @return string[]
     */
    public function getTokenClassNames(): array
    {
        return [Token::class];
    }

    /**
     * @throws UnsupportedAuthenticationTokenException
     * @throws InvalidConfigurationTypeException
     * @throws Exception
     * @throws NoSuchRoleException
     * @throws InvalidAuthenticationStatusException
     */
    public function authenticate(TokenInterface $authenticationToken): void
    {
        if (!$authenticationToken instanceof Token) {
            throw new UnsupportedAuthenticationTokenException(
                'This provider cannot authenticate the given token.',
                1747840757
            );
        }

        $credentials = $authenticationToken->getCredentials();
        $password = $credentials['password'] ?? null;
        if (!$this->token || $password !== $this->token) {
            $authenticationToken->setAuthenticationStatus(TokenInterface::NO_CREDENTIALS_GIVEN);
            return;
        }

        $authenticationToken->setAuthenticationStatus(TokenInterface::AUTHENTICATION_SUCCESSFUL);
        $account = new Account();
        $account->setAccountIdentifier(sha1($this->token));
        $roles = [$this->policyService->getRole('Shel.Neos.HealthCheck:Client')];
        $account->setRoles($roles);
        $authenticationToken->setAccount($account);
    }
}
