<?php

namespace Smvc\Core;

abstract class AbstractApplicationAware implements ApplicationAwareInterface
{
    /**
     * @var ApplicationInterface
     */
    private $application;

    public function setApplication(ApplicationInterface $application)
    {
        $this->application = $application;
    }

    public function getApplication()
    {
        return $this->application;
    }
}
