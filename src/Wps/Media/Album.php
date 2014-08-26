<?php

namespace Wps\Media;

use Wps\Security\Access;

use Smvc\Model\DefaultExchange;
use Smvc\Model\Persistence\DtoInterface;

/**
 * Album representation
 */
class Album extends DefaultExchange implements DtoInterface
{
    protected $id = null;

    protected $accountId = 0;

    protected $accessLevel = Access::LEVEL_PRIVATE;

    protected $previewMediaId = null;

    protected $path = "";

    protected $userName = null;

    protected $fileCount = 0;

    protected $addedDate = null;

    protected $updatedDate = null;

    protected $userBeginDate = null;

    protected $userEndDate = null;

    protected $shareEnabled = false;

    protected $shareToken = null;

    protected $sharePassword = null;

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
     * Get access level
     *
     * @return int
     *   One of the Access::LEVEL_* constant
     */
    public function getAccessLevel()
    {
        return $this->accessLevel;
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

    /**
     * Is this album shared
     *
     * @return boolean
     */
    public function isShared()
    {
        return $this->shareEnabled;
    }

    /**
     * Get share token
     *
     * @return string
     */
    public function getShareToken()
    {
        return $this->shareToken;
    }

    /**
     * Get share token
     *
     * @return string
     */
    public function getSharePassword()
    {
        return $this->sharePassword;
    }

    protected function getPrivateProperties()
    {
        return array('sharePassword');
    }

    public function toArray()
    {
        return array(
            'id'             => $this->id,
            'accountId'      => $this->accountId,
            'accessLevel'    => $this->accessLevel,
            'previewMediaId' => $this->previewMediaId,
            'path'           => $this->path,
            'userName'       => $this->userName,
            'fileCount'      => $this->fileCount,
            'addedDate'      => $this->addedDate,
            'updatedDate'    => $this->updatedDate,
            'userBeginDate'  => $this->userBeginDate,
            'userEndDate'    => $this->userEndDate,
            'shareEnabled'   => $this->shareEnabled,
            'shareToken'     => $this->shareToken,
            'sharePassword'  => $this->sharePassword,
        );
    }

    public function fromArray(array $array)
    {
        $array += $this->toArray();

        $this->id             = (int)$array['id'];
        $this->accountId      = (int)$array['accountId'];
        $this->accessLevel    = (int)$array['accessLevel'];
        $this->previewMediaId = (int)$array['previewMediaId'];
        $this->path           = $array['path'];
        $this->userName       = $array['userName'];
        $this->fileCount      = $array['fileCount'];
        $this->addedDate      = $array['addedDate'];
        $this->updatedDate    = $array['updatedDate'];
        $this->userBeginDate  = $array['userBeginDate'];
        $this->userEndDate    = $array['userEndDate'];
        $this->shareEnabled   = (bool)$array['shareEnabled'];
        $this->shareToken     = $array['shareToken'];
        $this->sharePassword  = $array['sharePassword'];
    }
}
