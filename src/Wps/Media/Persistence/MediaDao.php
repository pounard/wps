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
        // Minor optimisation that will short-circuit the complex query
        // if only the id condition if given
        if (1 === count($conditions) && isset($conditions['id'])) {
            if (is_array($conditions['id'])) {
                return $this->loadAll($conditions['id']);
            } else {
                try {
                    return array($this->load($conditions['id']));
                } catch (NotFoundError $e) {
                    return array();
                }
            }
        }

        $ret   = array();
        $args  = array();
        $where = array();

        foreach ($conditions as $column => $values) {
            switch ($column) {

              case 'id':
              case 'albumId':
              case 'accountId':
              case 'name':
              case 'path':
              case 'size':
              case 'md5Hash':
              case 'mimetype':
                  if (is_array($values)) {
                      $args[]  = array_merge($args, $values);
                      $where[] = $column . " IN (" . implode(', ', array_fill(0, count($values), '?')) . ")";
                  } else {
                      $args[]  = $values;
                      $where[] = $column . " = ?";
                  }
                  break;

              default:
                  trigger_error(sprintf("Unknown column '%s'", $column), E_USER_WARNING);
                  break;
            }
        }

        $query = "SELECT * FROM media";
        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }
        // @todo This should be configurable
        // Giving an order make results predictable across queries
        $query .= " ORDER BY id ASC";

        if ($limit) {
            $query .= " LIMIT ? OFFSET ?";
            $args[] = $limit;
            $args[] = $offset;
        }

        $db = $this->getContainer()->getDatabase();
        $st = $db->prepare($query);
        $st->setFetchMode(\PDO::FETCH_OBJ);

        $res = $st->execute($args);

        if ($res) {
            foreach ($res as $object) {
                $ret[] = $this->createObjectFrom($object);
            }
        }

        return $ret;
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
        if (!$object instanceof Media) {
            throw new \LogicError("Instance is not a \Wps\Media\Media instance");
        }

        $db = $this->getContainer()->getDatabase();
        $exists = false;

        if ($id = $object->getId()) {

            // Ensure object really exists
            $st = $db->prepare("SELECT 1 FROM media WHERE id = ?");
            $st->setFetchMode(\PDO::FETCH_COLUMN, 0);
            $res = $st->execute(array($id));

            foreach ($res as $value) { // There can be only one
                $exists = true;
            }
            if (!$exists) {
                // Do not allow insert with an already set identifier
                throw new \LogicError("Cannot insert or update media with an non existing identitifer in database");
            }
        }

        $time = time();

        if ($exists) {

            // Update
            $st = $db->prepare("
                UPDATE media
                SET
                    id_album = ?,
                    id_account = ?,
                    name = ?,
                    path = ?,
                    size = ?,
                    width = ?,
                    height = ?,
                    user_name = ?,
                    md5_hash = ?,
                    mimetype = ?,
                    ts_added = ?,
                    ts_updated = ?,
                    ts_user_date = ?,
                WHERE id = ?
            ");
            $st->execute(array(
                $object->getAlbumId(),
                $object->getAccountId(),
                $object->getName(),
                $object->getPath(),
                $object->getSize(),
                $object->getWidth(),
                $object->getHeight(),
                $object->getUserName(),
                $object->getMd5Hash(),
                $object->getMimetype(),
                Date::toTimestamp($object->getAddedDate(), 0),
                $time,
                Date::toTimestamp($object->getUserDate(), 0),
                $object->getId(),
            ));

            $object->fromArray(array(
                'updatedDate' => $time,
            ));

        } else {

            if ($addedDate = $object->getAddedDate()) {
                $addedTs = $addedDate->getTimestamp();
            } else {
                $addedTs = $time;
            }

            // Insert
            $st = $db->prepare("
                INSERT INTO media (
                    id_album,
                    id_account,
                    name,
                    path,
                    size,
                    width,
                    height,
                    user_name,
                    md5_hash,
                    mimetype,
                    ts_added,
                    ts_updated,
                    ts_user_date
                ) VALUES (
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?,
                    ?
                )
            ");
        }
    }
}
