<?php

namespace Wps\Media\Persistence;

use Wps\Media\Album;
use Wps\Util\Date;

use Smvc\Core\AbstractContainerAware;
use Smvc\Error\NotFoundError;
use Smvc\Error\NotImplementedError;
use Smvc\Model\Persistence\DaoInterface;
use Smvc\Model\Persistence\DtoInterface;

class AlbumDao extends AbstractContainerAware implements DaoInterface
{
    /**
     * Create object from database result
     *
     * @param object $res
     *   Database object result
     *
     * @return Album
     */
    protected function createObjectFrom($res)
    {
        $object = new Album();

        $object->fromArray(array(
            'id'             => $res->id,
            'accountId'      => $res->id_account,
            'path'           => $res->path,
            'userName'       => $res->user_name,
            'addedDate'      => \DateTime::createFromFormat(Date::MYSQL_DATETIME, $res->ts_added),
            'updatedDate'    => \DateTime::createFromFormat(Date::MYSQL_DATETIME, $res->ts_updated),
            'userDate'       => \DateTime::createFromFormat(Date::MYSQL_DATETIME, $res->ts_user_date),
            'previewMediaId' => $res->id_media_preview,
        ));

        return $object;
    }

    public function load($id)
    {
        $db = $this->getContainer()->getDatabase();

        $st = $db->prepare("SELECT * FROM album WHERE id = :id");
        $st->setFetchMode(\PDO::FETCH_OBJ);

        if ($st->execute(array(':id' => $id))) {
            foreach ($st as $res) {
                return $this->createObjectFrom($res);
            }
        }
        throw new NotFoundError();
    }

    public function loadAll(array $idList)
    {
        throw new NotImplementedError();
    }

    /**
     * Build where clause
     *
     * @param array $conditions
     *   Conditions
     * @param array &$args
     *   Where to push query arguments
     *
     * @return string[]
     *   Where clause statements
     */
    protected function buildWhere(array $conditions, array &$args)
    {
        $args  = array();
        $where = array();

        foreach ($conditions as $key => $values) {
            $column = null;

            switch ($key) {

              case 'id':
              case 'path':
                  $column = $key;
                  break;

              case 'accountId':
                  $column = 'id_account';
                  break;

              default:
                  trigger_error(sprintf("Unknown column '%s'", $column), E_USER_WARNING);
                  break;
            }

            if (null !== $column) {
                if (is_array($values)) {
                    $args[]  = array_merge($args, $values);
                    $where[] = $column . " IN (" . implode(', ', array_fill(0, count($values), '?')) . ")";
                } else {
                    $args[]  = $values;
                    $where[] = $column . " = ?";
                }
            }
        }

        return $where;
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

        $ret = array();
        $args = array();
        $where = $this->buildWhere($conditions, $args);

        $query = "SELECT * FROM album";
        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }

        // @todo This should be configurable
        // Giving an order make results predictable across queries
        $query .= " ORDER BY id ASC";

        if ($limit) {
            $limit = (int)$limit;
            $offset = (int)$offset;
            $query .= " LIMIT " . $limit . " OFFSET " . $offset;
        }

        $db = $this->getContainer()->getDatabase();
        $st = $db->prepare($query);
        $st->setFetchMode(\PDO::FETCH_OBJ);

        if ($st->execute($args)) {
            foreach ($st as $object) {
                $ret[] = $this->createObjectFrom($object);
            }
        }

        return $ret;
    }

    public function countFor(array $conditions)
    {
        $ret = array();
        $args = array();
        $where = $this->buildWhere($conditions, $args);

        $query = "SELECT COUNT(id) FROM album";
        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }

        $db = $this->getContainer()->getDatabase();
        $st = $db->prepare($query);
        $st->setFetchMode(\PDO::FETCH_COLUMN, 0);

        if ($st->execute($args)) {
            foreach ($st as $value) {
                return $value;
            }
        }

        return 0;
    }

    public function loadFirst(array $conditions)
    {
        $ret = $this->loadAllFor($conditions, 1, 0);

        if (!empty($ret)) {
            return reset($ret);
        }
    }

    public function save(DtoInterface $object)
    {
        if (!$object instanceof Album) {
            throw new \LogicError("Instance is not a \Wps\Media\Album instance");
        }

        $db = $this->getContainer()->getDatabase();
        $existing = null;
        $now = new \DateTime();

        if ($id = $object->getId()) {
            if (!$existing = $this->load($id)) {
                // Do not allow insert with an already set identifier
                throw new \LogicError("Cannot insert or update album with an non existing identitifer in database");
            }
        }

        if ($existing) {

            if (!$userDate = $existing->getUserDate()) {
                $userDate = $existing->getAddedDate();
            }

            // Update
            $st = $db->prepare("
                UPDATE album
                SET
                    id_account = ?,
                    id_media_preview = ?,
                    path = ?,
                    user_name = ?,
                    ts_added = ?,
                    ts_updated = ?,
                    ts_user_date = ?
                WHERE id = ?
            ");
            $st->execute(array(
                (int)$object->getAccountId(),
                (int)$object->getPreviewMediaId(),
                $object->getPath(),
                $object->getUserName(),
                $existing->getAddedDate()->format(Date::MYSQL_DATETIME),
                $now->format(Date::MYSQL_DATETIME),
                $userDate->format(Date::MYSQL_DATETIME),
                $object->getId(),
            ));

            $object->fromArray(array(
                'updatedDate' => $now,
            ));

        } else {

            if (!$addedDate = $object->getAddedDate()) {
                $addedDate = $now;
            }
            if (!$userDate = $object->getUserDate()) {
                $userDate = $addedDate;
            }

            // Insert
            $st = $db->prepare("
                INSERT INTO album (
                    id_account,
                    id_media_preview,
                    path,
                    user_name,
                    ts_added,
                    ts_updated,
                    ts_user_date
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            $st->execute(array(
                (int)$object->getAccountId(),
                (int)$object->getPreviewMediaId(),
                $object->getPath(),
                $object->getUserName(),
                $addedDate->format(Date::MYSQL_DATETIME),
                $addedDate->format(Date::MYSQL_DATETIME),
                $userDate->format(Date::MYSQL_DATETIME),
            ));

            $st = $db->prepare("SELECT LAST_INSERT_ID()");
            $st->setFetchMode(\PDO::FETCH_COLUMN, 0);

            if ($st->execute()) {
                foreach ($st as $id) {
                    $object->fromArray(array(
                        'id'          => $id,
                        'addedDate'   => $addedDate,
                    ));
                }
            }
        }
    }
}
