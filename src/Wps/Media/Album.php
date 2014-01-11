<?php

namespace Wps\Media;

use Smvc\Model\Persistence\DtoInterface;

/**
 * Album representation
 */
class Album implements DtoInterface
{
    protected $id = null;

    protected $accountId = 0;

    protected $path = "";

    protected $userName = null;

    protected $fileCount = 0;

    protected $addedDate = null;

    protected $updatedDate = null;

    protected $userDate = null;

    public function getId()
    {
        return $this->id;
    }

    public function getDisplayName()
    {
        if (empty($this->userName)) {
            $segments = explode('/', $this->path);
            return end($segments);
        } else {
            return $this->userName;
        }
    }

    /**
     * Get owner account identifier
     *
     * @return scalar
     */
    public function getAccountId()
    {
        return $this->accountId;
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
     * Get file count
     *
     * @return int
     */
    public function getFileCount()
    {
        return $this->fileCount;
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
            'userName'    => $this->userName,
            'fileCount'   => $this->fileCount,
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
        $this->fileCount   = $array['fileCount'];
        $this->addedDate   = $array['addedDate'];
        $this->updatedDate = $array['updatedDate'];
        $this->userDate    = $array['userDate'];
    }
}
