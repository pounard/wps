<?php

namespace Smvc\Model;

/**
 * Represent any object that can be serialized
 */
interface ExchangeInterface
{
    /**
     * Convert the current object to array
     *
     * @return array
     */
    public function toArray();

    /**
     * Convert the current object to an array without any confidential
     * information (for frontend usage or external systems exchange)
     *
     * @return array
     */
    public function toSecureArray();

    /**
     * Populate the object from array
     *
     * This method is not supposed to create a full object but rather edit
     * and existing one: for example folder objects will only accept a name
     * change and nothing more
     *
     * @param array $array
     */
    public function fromArray(array $array);
}
