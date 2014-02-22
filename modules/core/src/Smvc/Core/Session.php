<?php

namespace Smvc\Core;

use Smvc\Error\LogicError;
use Smvc\Security\AccountInterface;
use Smvc\Security\AccountProviderInterface;
use Smvc\Security\DefaultAccountProvider;

/**
 * Very simple session component
 *
 * @todo Make it backendable
 */
class Session
{
    /**
     * @var AccountInterface
     */
    private $account;

    /**
     * @var boolean
     */
    private $started = false;

    /**
     * @var boolean
     */
    private $destroyed = false;

    /**
     * @var boolean
     */
    private $regenerated = false;

    /**
     * @var \ArrayAccess
     */
    private $storage;

    /**
     * @var AccountProviderInterface
     */
    private $accountProvider;

    /**
     * Default constructor
     */
    public function __construct(AccountProviderInterface $accountProvider = null)
    {
        if (null === $accountProvider) {
            $this->accountProvider = new DefaultAccountProvider();
        } else {
            $this->accountProvider = $accountProvider;
        }

        $this->storage = new NativeSessionStorage();
    }

    /**
     * Get account provider
     *
     * @return AccountProviderInterface
     */
    public function getAccountProvider()
    {
        return $this->accountProvider;
    }

    /**
     * Is the session started
     *
     * @return boolean
     */
    public function isStarted()
    {
        return $this->started;
    }

    /**
     * Start session
     *
     * @return boolean
     */
    public function start()
    {
        if ($this->destroyed) {
            throw new LogicError("Cannot start as destroyed session");
        }
        if ($this->regenerated) {
            throw new LogicError("Cannot start as regenerated session");
        }

        if (!$this->started) {
            $this->started = session_start();
        }

        return $this->started;
    }

    /**
     * Regenerate session
     *
     * @param string $username
     *
     * @return boolean
     */
    public function regenerate($username = null)
    {
        if (!$this->regenerated) {
            $this->regenerated = session_regenerate_id(true);

            if (null !== $username) {
                $this->setAccount($this->accountProvider->getAccount($username));
            } else {
                $this->setAccount(null);
            }
        }

        return $this->regenerated;
    }

    /**
     * Destroy session
     *
     * @return boolean
     */
    public function destroy()
    {
        if (!$this->destroyed) {
            $this->destroyed = session_destroy();
            $this->started = false;
        }

        return $this->destroyed;
    }

    /**
     * Commit session
     */
    public function commit()
    {
        $this->started = false;
        session_write_close();
    }

    /**
     * Get storage
     *
     * @return \ArrayAccess
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Set logged in account
     *
     * @param AccountInterface $account
     */
    public function setAccount(AccountInterface $account = null)
    {
        $this->account = $account;

        if (null === $account) {
            unset($this->storage['accountId']);
        } else {
            $this->storage['accountId'] = $account->getId();
        }
    }

    /**
     * Get logged in account
     *
     * @return AccountInterface
     */
    public function getAccount()
    {
        if (null === $this->account) {
            if (isset($this->storage['accountId'])) {
                $this->account = $this->accountProvider->getAccountById($this->storage['accountId']);
            } else {
                $this->account = $this->accountProvider->getAnonymousAccount();
            }
        }

        return $this->account;
    }

    /**
     * Is current account authenticated
     */
    public function isAuthenticated()
    {
        return 0 != $this->getAccount()->getId();
    }
}
