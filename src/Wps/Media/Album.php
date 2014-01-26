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

    protected $previewMediaId = null;

    protected $path = "";

    protected $userName = null;

    protected $fileCount = 0;

    protected $addedDate = null;

    protected $updatedDate = null;

    protected $userBeginDate = null;

    protected $userEndDate = null;

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
     * Get preview media identifier
     *
     * @return scalar
     */
    public function getPreviewMediaId()
    {
        return $this->previewMediaId;
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
     * Alias of getUserBeginDate()
     *
     * @return \DateTime
     */
    public function getUserDate()
    {
      return $this->userBeginDate;
    }

    /**
     * Get user date
     *
     * @return \DateTime
     */
    public function getUserBeginDate()
    {
        return $this->userBeginDate;
    }

    /**
     * Get user date
     *
     * @return \DateTime
     */
    public function getUserEndDate()
    {
        return $this->userEndDate;
    }

    public function toArray()
    {
        return array(
            'id'             => $this->id,
            'accountId'      => $this->accountId,
            'previewMediaId' => $this->previewMediaId,
            'path'           => $this->path,
            'userName'       => $this->userName,
            'fileCount'      => $this->fileCount,
            'addedDate'      => $this->addedDate,
            'updatedDate'    => $this->updatedDate,
            'userBeginDate'  => $this->userBeginDate,
            'userEndDate'    => $this->userEndDate,
        );
    }

    public function fromArray(array $array)
    {
        $array += $this->toArray();

        $this->id             = (int)$array['id'];
        $this->accountId      = (int)$array['accountId'];
        $this->previewMediaId = (int)$array['previewMediaId'];
        $this->path           = $array['path'];
        $this->userName       = $array['userName'];
        $this->fileCount      = $array['fileCount'];
        $this->addedDate      = $array['addedDate'];
        $this->updatedDate    = $array['updatedDate'];
        $this->userBeginDate  = $array['userBeginDate'];
        $this->userEndDate    = $array['userEndDate'];
    }
}
