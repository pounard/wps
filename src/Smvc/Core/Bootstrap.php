<?php

namespace Smvc\Core;

use Smvc\Dispatch\RequestInterface;
use Smvc\Error\ConfigError;

use Config\Impl\Memory\MemoryBackend;
use Doctrine\Common\Cache\RedisCache;
use Smvc\Security\AccountProviderInterface;

/**
 * OK this is far from ideal nevertheless it works
 */
class Bootstrap
{
    /**
     * Tell if the current environment has been prepared
     */
    static private $environmentPrepared = false;

    /**
     * Prepare the environement
     */
    static public function prepareEnvironment()
    {
        if (self::$environmentPrepared) {
            return;
        }

        self::$environmentPrepared = true;

        mb_internal_encoding("UTF-8");
        date_default_timezone_set('CET');
    }

    /**
     * Bootstrap core application
     */
    static public function bootstrap(
        ContainerAwareInterface $component,
        $config)
    {
        self::prepareEnvironment();

        $container = new Container($config);
        $component->setContainer($container);

        $pimple = $container->getInternalContainer();

        // Set some various services
        foreach ($config['services'] as $key => $value) {
            // Spawn the service factory
            if (is_callable($value)) {
                $pimple[$key] = function () use ($container, $value) {
                    call_user_func($value, $container);
                };
            } else if (class_exists($value)) {
                $pimple[$key] = function () use ($container, $value) {
                    $service = new $value();
                    if ($service instanceof ContainerAwareInterface) {
                        $service->setContainer($container);
                    }
                    return $service;
                };
            } else {
                throw new ConfigError(sprintf("Invalid service definition '%s'", $key));
            }
        }

        // FIXME: Handle multiple connections
        // FIXME: Move this out of the generic framework
        if (isset($config['db'])) {
            foreach ($config['db'] as $key => $info) {
                $dsn = $info['driver'] . ':' . 'dbname=' . $info['database'] . ';host=' . $info['hostname'];
                $username = $info['username'];
                $password = $info['password'];
                $pimple['db.' . $key] = function () use ($dsn, $username, $password) {
                    return new \PDO($dsn, $username, $password);
                };
            }
        }

        // Handle session and security
        // FIXME: This need some love
        if (isset($config['security']['auth'])) {
            if (!class_exists($config['security']['auth'])) {
                throw new ConfigError(sprintf("Class does not exist: '%s'", $config['security']['auth']));
            }
            $authProvider = new $config['security']['auth']();
            if ($authProvider instanceof ContainerAwareInterface) {
                $authProvider->setContainer($container);
            }

            $pimple['auth'] = $authProvider;
            if ($authProvider instanceof AccountProviderInterface) {
                $session = new Session($authProvider);
            } else {
                $session = new Session();
            }
        } else {
            $session = new Session();
        }
        if ($session instanceof ContainerAwareInterface) {
            $session->setContainer($container);
        }
        $pimple['session'] = $session;

        // Run for it!
        $session->start();

        // @todo Rewrite this
        /*
        $cache = null;
        if (isset($config['redis'])) {
            $redis = new \Redis();
            $redis->connect($config['redis']['host']);
            $cache = new RedisCache();
            $cache->setNamespace('wps/user');
            $cache->setRedis($redis);
        }
         */
        $pimple['config'] = $prefs = new ConfigObject(
            $config['config'] /*,
            $cache,
            $container->getSession()->getAccount()->getId() */
        );

        if (!isset($prefs['charset'])) {
            $prefs['charset'] = "UTF-8";
        }
        mb_internal_encoding($prefs['charset']);
    }
}
