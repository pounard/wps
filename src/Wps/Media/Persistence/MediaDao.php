<?php

namespace Wps\Media\Persistence;

use Smvc\Core\AbstractContainerAware;
use Smvc\Error\NotImplementedError;

class MediaDao extends AbstractContainerAware implements DaoInterface
{
    public function load($id)
    {
        throw new NotImplementedError();
    }

    public function loadAll(array $idList)
    {
        throw new NotImplementedError();
    }

    public function loadAllFor(array $conditions, $limit = 100, $offset = 0)
    {
        throw new NotImplementedError();
    }

    public function loadFirst(array $conditions)
    {
        throw new NotImplementedError();
    }

    public function save($object)
    {
        throw new NotImplementedError();
    }
}
