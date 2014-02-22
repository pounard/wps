<?php

namespace Contact\Model;

use Smvc\Core\AbstractApplicationAware;
use Smvc\Model\Persistence\DaoInterface;
use Smvc\Model\Persistence\DtoInterface;

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
        $object = new Contact();

        $object->fromArray(array(
            'id'          => $res->id,
            'username'    => $res->mail,
            'displayName' => $res->user_name,
            'publicKey'   => $res->key_public,
            'keyType'     => $res->key_type,
        ));

        return $object;
    }

    public function load($id)
    {
        $db = $this->getApplication()->getDatabase();

        $st = $db->prepare("SELECT * FROM account WHERE id = :id");
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

        $st = $db->prepare("SELECT * FROM account WHERE id IN (" . implode(', ', array_fill(0, count($idList), '?')) .")");
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
                    $column = $key;
                    break;

                case 'username':
                    $column = 'mail';
                    break;

                case 'displayName':
                    $column = 'user_name';
                    break;

                case 'keyType':
                    $column = 'key_type';
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

        $query = "SELECT * FROM account";
        if (!empty($where)) {
            $query .= " WHERE " . implode(" AND ", $where);
        }
        // @todo This should be configurable
        // Giving an order make results predictable across queries
        $query .= " ORDER BY id ASC, id ASC";

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

        $query = "SELECT COUNT(id) FROM account";
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
        throw new \RuntimeException("Contacts are readonly");
    }
}
