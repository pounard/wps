<?php

namespace Smvc\Security\Auth;

use Smvc\Security\AccountProviderInterface;
use Smvc\Security\Crypt\Crypt;

/**
 * Authentication component proxy that will set generate account keys
 * if they are missing from the logged in user 
 */
class AuthProxy implements AuthProviderInterface
{
    /**
     * @var AuthProviderInterface
     */
    private $nested;

    /**
     * @var AccountProviderInterface
     */
    private $accountProvider;

    /**
     * Default constructor
     *
     * @param AuthProviderInterface $proxy
     */
    public function __construct(
        AuthProviderInterface $nested,
        AccountProviderInterface $accountProvider)
    {
        $this->nested = $nested;
        $this->accountProvider = $accountProvider;
    }

    public function authenticate($username, $password)
    {
        if ($this->nested->authenticate($username, $password)) {

            $account = $this->accountProvider->getAccount($username);

            if (null === $account->getPublicKey()) {
                list($privateKey, $publicKey, $type) = Crypt::generateRsaKeys();
                $this->accountProvider->setAccountKeys(
                    $account->getId(),
                    $privateKey,
                    $publicKey,
                    $type
                );
            }

            return true;
        }

        return false;
    }
}
