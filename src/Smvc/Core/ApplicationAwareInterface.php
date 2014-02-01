<?php

namespace Smvc\Core;

interface ApplicationAwareInterface
{
    /**
     * Set application
     *
     * @param ApplicationInterface $app
     */
    public function setApplication(ApplicationInterface $application);

    /**
     * Get application
     *
     * @return ApplicationInterface
     */
    public function getApplication();
}
