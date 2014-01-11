<?php

namespace Wps\Media;

use Wps\Util\FileSystem;

use Smvc\Error\NotImplementedError;
use Smvc\Model\Persistence\DtoInterface;

/**
 * Media representation
 */
class Media implements DtoInterface
{
    /**
     * Create instance from file
     *
     * @param string $filename
     *   Physical file name
     * @param string $workingDirectory
     *   Working directory to strip from file name
     */
    static public function createInstanceFromFile($filename, $workingDirectory = null)
    {
        if (!is_file($filename)) {
            throw new \RuntimeException("File does not exists or is not a regular file");
        }
        if (!is_readable($filename)) {
            throw new \RuntimeException("File is not readable");
        }

        // Detect mime
        if (function_exists('finfo_open')) {
            $res = finfo_open(FILEINFO_MIME_TYPE);
            $mimetype = finfo_file($res, $filename);
            finfo_close($res);
        } else if (function_exists('mime_content_type')) {
            $mimetype = mime_content_type($filename);
        } else {
            $mimetype = 'application/octet-stream';
        }

        if ($workingDirectory && 0 === strpos($filename, $workingDirectory)) {
            $relativePath = substr($filename, len($workingDirectory) + 1);
        } else {
            $relativePath = $filename;
        }

        $data = array(
            'name' => basename($relativePath),
            'path' => dirname($relativePath),
            'size' => filesize($filename),
            'mimetype' => $mimetype,
            'addedDate' => new \DateTime(),
            'md5Hash' => md5_file($filename),
        );

        // @todo Handle other attributes

        $instance = new self();
        $instance->fromArray($data);

        return $instance;
    }

    protected $id = null;

    protected $albumId = null;

    protected $accountId = 0;

    protected $name = "";

    protected $path = "";

    protected $realPath = null;

    protected $size = 0;

    protected $width = null;

    protected $height = null;

    protected $userName = null;

    protected $md5Hash = null;

    protected $mimetype = 'application/octect-stream';

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
            return $this->name;
        } else {
            return $this->userName;
        }
    }

    /**
     * Get album identifier
     *
     * @return int
     */
    public function getAlbumId()
    {
        return $this->albumId;
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
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get file path relative to user library
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Get physical file path relative to public data directory
     *
     * @return string
     */
    public function getRealPath()
    {
      return $this->realPath;
    }
    

    /**
     * Get path and name relative to user library
     *
     * @return string
     */
    public function getPathName()
    {
        return FileSystem::pathJoin($this->path, $this->name);
    }

    /**
     * Get file size in bytes
     *
     * @return int
     */
    public function getSize()
    {
        return $this->size;
    }

    /**
     * Get width
     *
     * @return int
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Get height
     *
     * @return int
     */
    public function getHeight()
    {
        return $this->height;
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
     * Get md5 hash
     *
     * @return string
     */
    public function getMd5Hash()
    {
        return $this->md5Hash;
    }

    /**
     * Get mime type
     *
     * @return string
     */
    public function getMimetype()
    {
        return $this->mimetype;
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
            'albumId'     => $this->albumId,
            'accountId'   => $this->accountId,
            'name'        => $this->name,
            'path'        => $this->path,
            'realPath'    => $this->realPath,
            'size'        => $this->size,
            'width'       => $this->width,
            'height'      => $this->height,
            'userName'    => $this->userName,
            'md5Hash'     => $this->md5Hash,
            'mimetype'    => $this->mimetype,
            'addedDate'   => $this->addedDate,
            'updatedDate' => $this->updatedDate,
            'userDate'    => $this->userDate,
        );
    }

    public function fromArray(array $array)
    {
        $array += $this->toArray();

        $this->id          = (int)$array['id'];
        $this->albumId     = (int)$array['albumId'];
        $this->accountId   = $array['accountId'];
        $this->name        = $array['name'];
        $this->path        = $array['path'];
        $this->realPath    = $array['realPath'];
        $this->size        = (int)$array['size'];
        $this->width       = (int)$array['width'];
        $this->height      = (int)$array['height'];
        $this->userName    = $array['userName'];
        $this->md5Hash     = $array['md5Hash'];
        $this->mimetype    = $array['mimetype'];
        $this->addedDate   = $array['addedDate'];
        $this->updatedDate = $array['updatedDate'];
        $this->userDate    = $array['userDate'];
    }
}
