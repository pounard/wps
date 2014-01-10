<?php

namespace Smvc\Model\Persistence;

/**
 * DAO common interface
 */
interface DaoInterface
{
    /**
     * Load a single object
     *
     * @param scalar $id
     *
     * @return DtoInterface
     */
    public function load($id);

    /**
     * Load object list
     *
     * @param scalar[] $idList
     *
     * @return DtoInterface[]
     */
    public function loadAll(array $idList);

    /**
     * Load all objects matching the given conditions
     *
     * @param array $conditions
     *   Array of conditions
     * @param int $limit
     *   Fetch limit
     * @param int $offset
     *   Starting offset
     *
     * @return DtoInterface[]
     */
    public function loadAllFor(array $conditions, $limit = 100, $offset = 0);

    /**
     * Load first item matching the given conditions
     *
     * Order does not matter
     *
     * @param array $conditions
     *   Array of conditions
     *
     * @return DtoInterface
     */
    public function loadFirst(array $conditions);

    /**
     * Save or update object
     *
     * @param mixed $object
     */
    public function save(DtoInterface $object);
}
