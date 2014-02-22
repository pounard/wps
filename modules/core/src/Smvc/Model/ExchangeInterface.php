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
