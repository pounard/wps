<?php

namespace Smvc\Model\Persistence;

use Smvc\Model\ExchangeInterface;

interface DtoInterface extends ExchangeInterface
{
    /**
     * Get identifier
     *
     * @return scalar
     */
    public function getId();

    /**
     * Get display name
     *
     * @return string
     *   Computed string ready for display
     */
    public function getDisplayName();
}
