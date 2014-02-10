<?php

namespace Contact\Model;

class ContactDao extends AbstractApplicationAware implements DaoInterface
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
        ));

        return $object;
    }

    public function load($id)
    {
        $db = $this->getApplication()->getDatabase();

        $st = $db->prepare("SELECT * FROM media WHERE id = :id");
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
        $db = $this->getApplication()->getDatabase();

        $st = $db->prepare("SELECT * FROM media WHERE id IN (" . implode(', ', array_fill(0, count($idList), '?')) .")");
        $st->setFetchMode(\PDO::FETCH_OBJ);

        $ret = array();

        if ($st->execute($idList)) {
            foreach ($st as $res) {
                $ret[] = $this->createObjectFrom($res);
            }
        }

        return $ret;
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
                case 'name':
                case 'path':
                case 'size':
                case 'mimetype':
                    $column = $key;
                    break;

                case 'albumId':
                    $column = 'id_album';
                    break;

                case 'accountId':
                    $column = 'id_account';
                    break;

                case 'md5Hash':
                    $column = 'md5_hash';
                    break;

                case 'realPath':
                    $column = 'physical_path';
                    break;

                default:
                    trigger_error(sprintf("Unknown column '%s'", $column), E_USER_WARNING);
                    break;
            }

            if (null !== $column) {
                if (is_array($values)) {
                    $args  = array_merge($args, $values);
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

        $query = "SELECT * FROM media";
        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }
        // @todo This should be configurable
        // Giving an order make results predictable across queries
        $query .= " ORDER BY ts_user_date ASC, id ASC";

        if ($limit) {
            $limit = (int)$limit;
            $offset = (int)$offset;
            $query .= " LIMIT " . $limit . " OFFSET " . $offset;
        }

        $db = $this->getApplication()->getDatabase();
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

        $query = "SELECT COUNT(id) FROM media";
        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }

        $db = $this->getApplication()->getDatabase();
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
        if (!$object instanceof Media) {
            throw new \LogicError("Instance is not a \Wps\Media\Media instance");
        }

        $db = $this->getApplication()->getDatabase();
        $existing = null;
        $now = new \DateTime();

        if ($id = $object->getId()) {
            if (!$existing = $this->load($id)) {
                // Do not allow insert with an already set identifier
                throw new \LogicError("Cannot insert or update media with an non existing identitifer in database");
            }
        }

        if ($existing) {

            if (!$userDate = $existing->getUserDate()) {
                $userDate = $existing->getAddedDate();
            }

            // Update
            $st = $db->prepare("
                UPDATE media
                SET
                    id_album = ?,
                    id_account = ?,
                    name = ?,
                    path = ?,
                    physical_path = ?,
                    size = ?,
                    width = ?,
                    height = ?,
                    user_name = ?,
                    md5_hash = ?,
                    mimetype = ?,
                    ts_added = ?,
                    ts_updated = ?,
                    ts_user_date = ?
                WHERE id = ?
            ");
            $st->execute(array(
                $object->getAlbumId(),
                $object->getAccountId(),
                $object->getName(),
                $object->getPath(),
                $object->getRealPath(),
                $object->getSize(),
                $object->getWidth(),
                $object->getHeight(),
                $object->getUserName(),
                $object->getMd5Hash(),
                $object->getMimetype(),
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
                INSERT INTO media (
                    id_album,
                    id_account,
                    name,
                    path,
                    physical_path,
                    size,
                    width,
                    height,
                    user_name,
                    md5_hash,
                    mimetype,
                    ts_added,
                    ts_updated,
                    ts_user_date
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");

            $st->execute(array(
                $object->getAlbumId(),
                $object->getAccountId(),
                $object->getName(),
                $object->getPath(),
                $object->getRealPath(),
                $object->getSize(),
                $object->getWidth(),
                $object->getHeight(),
                $object->getUserName(),
                $object->getMd5Hash(),
                $object->getMimetype(),
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
