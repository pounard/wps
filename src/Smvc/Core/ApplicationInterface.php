<?php

namespace Smvc\Core;

/**
 * The application object is the center of your application: it acts as a
 * registry of services.
 *
 * Internally a dependency injection container exists and will be used in
 * the default implementation, but in real life you don't care about how
 * services are registered in this or not: application object is a business
 * object that is defined by the high level software.
 */
interface ApplicationInterface
{
    /**
     * Get raw service from the internal services registry by name
     *
     * @param string $name
     */
    public function get($name);

    /**
     * Get parameter value
     *
     * @param string $name
     * @param mixed $default
     */
    public function getParameter($name, $default);

    /**
     * Get configuration
     *
     * @return \ArrayAccess
     */
    public function getConfig();

    /**
     * Get default charset
     */
    public function getDefaultCharset();

    /**
     * Get session
     *
     * @return \Smvc\Core\Session
     */
    public function getSession();

    /**
     * Get messager
     *
     * @return \Smvc\Core\Messager
     */
    public function getMessager();

    /**
     * Get database connection
     *
     * @return \PDO
     */
    public function getDatabase($target = 'default');

    /**
     * Get DAO
     *
     * @param string $key
     *
     * @return \Smvc\Model\Persistence\DaoInterface
     */
    public function getDao($name);

    /**
     * Get factory
     *
     * @param string $name
     *
     * @return \Smvc\Plugin\FactoryInterface
     */
    public function getFactory($name);
}
