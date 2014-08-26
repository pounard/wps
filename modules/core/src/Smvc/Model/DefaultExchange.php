<?php

namespace Smvc\Model;

/**
 * Base implementation for safe objects
 */
abstract class DefaultExchange implements ExchangeInterface
{
    /**
     * Get properties to remove from secure array
     */
    protected function getPrivateProperties()
    {
        return array();
    }

    public function toSecureArray()
    {
        $values = $this->toArray();
        $privateKeys = $this->getPrivateProperties();

        if (!empty($privateKeys)) {
            $values = array_diff_key($values, array_flip($privateKeys));
        }

        return $values;
    }
}
