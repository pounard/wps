<?php

namespace Smvc\Core;

use Smvc\Dispatch\RequestInterface;
use Smvc\Error\ConfigError;

use Config\Impl\Memory\MemoryBackend;
use Doctrine\Common\Cache\RedisCache;

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

        $pimple['session']->start();

        // From that point we need at least to compute a default
        // email address from the default domain
        if ($name = $container->getSession()->getAccount()->getUsername()) {
            $pimple['defaultAddress'] = $name . '@' .  $config['config']['domain'];
        } else {
            $pimple['defaultAddress'] = '';
        }

        // @todo Rewrite this
        $cache = null;
        if (isset($config['redis'])) {
            $redis = new \Redis();
            $redis->connect($config['redis']['host']);
            $cache = new RedisCache();
            $cache->setNamespace('wps/user');
            $cache->setRedis($redis);
        }
        $pimple['config'] = $prefs = new ConfigObject(
            $config['config'],
            $cache,
            $container->getSession()->getAccount()->getId()
        );

        if (!isset($prefs['charset'])) {
            $prefs['charset'] = "UTF-8";
        }
        mb_internal_encoding($prefs['charset']);
    }
}
