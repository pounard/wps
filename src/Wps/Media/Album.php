<?php

namespace Wps\Media;

/**
 * Album representation
 */
class Album implements ExchangeInterface
{
    protected $id = null;

    protected $accountId = 0;

    protected $path = "";

    protected $userName = null;

    protected $addedDate = null;

    protected $updatedDate = null;

    protected $userDate = null;

    /**
     * Get identifier
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get owner account identifier
     *
     * @return scalar
     */
    public function getAccountId()
    {
        return $this->scalar;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get user name
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->userName;
    }

    /**
     * Get added date
     *
     * @return \DateTime
     */
    public function getAddedDate()
    {
        return $this->addedDate;
    }

    /**
     * Get update date
     *
     * @return \DateTime
     */
    public function getUpdatedDate()
    {
        return $this->updatedDate;
    }

    /**
     * Get user date
     *
     * @return \DateTime
     */
    public function getUserDate()
    {
        return $this->userDate;
    }

    public function toArray()
    {
        return array(
            'id'          => $this->id,
            'accountId'   => $this->accountId,
            'path'        => $this->path,
            'addedDate'   => $this->addedDate,
            'updatedDate' => $this->updatedDate,
            'userDate'    => $this->userDate,
        );
    }

    public function fromArray(array $array)
    {
        $array += $this->toArray();

        $this->id          = (int)$array['id'];
        $this->accountId   = $array['accountId'];
        $this->path        = $array['path'];
        $this->userName    = $array['userName'];
        $this->addedDate   = $array['addedDate'];
        $this->updatedDate = $array['updatedDate'];
        $this->userDate    = $array['userDate'];
    }
}
