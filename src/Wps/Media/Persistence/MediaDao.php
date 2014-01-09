<?php

namespace Wps\Media\Persistence;

use Wps\Media\Media;
use Wps\Util\Date;

use Smvc\Core\AbstractContainerAware;
use Smvc\Error\NotFoundError;
use Smvc\Error\NotImplementedError;

class MediaDao extends AbstractContainerAware implements DaoInterface
{
    /**
     * Create object from database result
     *
     * @param object $res
     *   Database object result
     *
     * @return Media
     */
    protected function createObjectFrom($res)
    {
        $object = new Media();

        $object->fromArray(array(
            'id'          => $res->id,
            'albumId'     => $res->id_album,
            'accountId'   => $res->id_account,
            'name'        => $res->name,
            'path'        => $res->path,
            'size'        => $res->size,
            'width'       => $res->width,
            'height'      => $res->height,
            'userName'    => $res->user_name,
            'md5Hash'     => $res->md5_hash,
            'mimetype'    => $res->mimetype,
            'addedDate'   => Date::fromTimestamp($res->ts_added),
            'updatedDate' => Date::fromTimestamp($res->ts_updated),
            'userDate'    => Date::fromTimestamp($res->ts_user_date),
        ));

        return $object;
    }

    public function load($id)
    {
        $db = $this->getContainer()->getDatabase();

        $st = $db->prepare("SELECT * FROM media WHERE id = :id");
        $st->setFetchMode(PDO::FETCH_OBJ);
        $res = $st->execute(array(':id' => $id));

        if (!$res) {
            throw new NotFoundError();
        }

        return $this->createObjectFrom($res);
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
        $ret = $this->loadAllFor($conditions, 1, 0);

        if (!empty($ret)) {
            return reset($ret);
        }
    }

    public function save($object)
    {
        throw new NotImplementedError();
    }
}
