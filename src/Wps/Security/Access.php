<?php

namespace Wps\Security;

final class Access
{
    /**
     * Security level private
     */
    const LEVEL_PRIVATE = 0;

    /**
     * Security level friends can see
     */
    const LEVEL_FRIEND = 50;

    /**
     * Security level public
     */
    const LEVEL_PUBLIC = 100;
}
