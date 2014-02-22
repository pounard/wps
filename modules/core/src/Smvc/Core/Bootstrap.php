<?php

namespace Smvc\Core;

use Smvc\Dispatch\RequestInterface;
use Smvc\Error\ConfigError;
use Smvc\Security\AccountProviderInterface;

use Config\Impl\Memory\MemoryBackend;

use Doctrine\Common\Cache\RedisCache;
use Contact\Module;

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
        ApplicationAwareInterface $component,
        $config)
    {
        self::prepareEnvironment();

        $app = new DefaultApplication($config);
        $component->setApplication($app);
        $pimple = $app->getServiceRegistry();

        // Find modules before registering any services: some services might
        // be overriden or defined by any module
        $modules = array();
        if (!empty($config['modules'])) {
            foreach ($config['modules'] as $name => $namespace) {
                $class = $namespace . "\Module";
                // @todo Find a better way to register path
                $path = getcwd() . '/modules/' . $name;
                if (class_exists($class)) {
                    $module = new $class($name, $path, $namespace);
                } else {
                    $module = new Module($name, $path, $namespace);
                }
                $modules[$name] = $module;

                // Merge module config
                if ($mConfig = $module->getConfig()) {
                    $config = ConfigObject::arrayMergeRecursive($config, $mConfig);
                }
            }
        }
        $app->setModules($modules);

        // Set some various services
        foreach ($config['services'] as $key => $value) {
            // Spawn the service factory
            if (is_callable($value)) {
                $pimple[$key] = function () use ($app, $value) {
                    call_user_func($value, $app);
                };
            } else if (class_exists($value)) {
                $pimple[$key] = function () use ($app, $value) {
                    $service = new $value();
                    if ($service instanceof ApplicationAwareInterface) {
                        $service->setApplication($app);
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
                    $instance = new \PDO($dsn, $username, $password);
                    $instance->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                    return $instance;
                };
            }
        }

        // Handle session and security
        // FIXME: This need some love
        if (isset($config['security']['accountprovider'])) {
            if (!class_exists($config['security']['accountprovider'])) {
                throw new ConfigError(sprintf("Class does not exist: '%s'", $config['security']['accountprovider']));
            }
            $accountProvider = new $config['security']['accountprovider']();
            if ($accountProvider instanceof ApplicationAwareInterface) {
                $accountProvider->setApplication($app);
            }
            $session = new Session($accountProvider);
        } else {
            $session = new Session();
        }
        if ($session instanceof ApplicationAwareInterface) {
            $session->setApplication($app);
        }
        $pimple['session'] = $session;
        $pimple['accountprovider'] = $session->getAccountProvider();

        // Everything is in place we can let modules bootstrap now
        foreach ($modules as $module) {
            $module->bootstrap($config, $app);
        }

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
            $app->getSession()->getAccount()->getId() */
        );

        if (!isset($prefs['charset'])) {
            $prefs['charset'] = "UTF-8";
        }
        mb_internal_encoding($prefs['charset']);
    }
}
