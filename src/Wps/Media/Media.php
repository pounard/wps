<?php

namespace Wps\Media;

use Smvc\Error\NotImplementedError;
use Smvc\Model\ExchangeInterface;

/**
 * Media representation
 */
class Media implements ExchangeInterface
{
    /**
     * Create instance from file
     *
     * @param string $filename
     *   Physical file
     * @param string $destination
     *   Where to copy the file, if nothing is given file will be kept
     *   where it is and nothing will changed on the file system
     */
    static public function createInstanceFromFile($filename, $destination = null)
    {
        if (!is_file($filename)) {
            throw new \RuntimeException("File does not exists or is not a regular file");
        }
        if (!is_readable($filename)) {
            throw new \RuntimeException("File is not readable");
        }

        // Copy file if necessary
        if (null !== $destination) {
            if (!is_dir($destination)) {
                throw new \RuntimeException("Destination does not exist or is a no directory");
            }
            if (!is_writable($filename)) {
                throw new \RuntimeException("Destination is not writable");
            }
            // Trust PHP for using the underlaying OS better than us to
            // copy the file, trust the OS to be very efficient for this
            if (!copy($filename, $destination . '/' . basename($filename))) {
                throw new \RuntimeException("Could not copy file to destination");
            }

            $filename = $destination . '/' . basename($filename);
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

        $data = array(
            'name' => basename($filename),
            'path' => dirname($filename),
            'size' => filesize($filename),
            'mimetype' => $mimetype,
            'addedDate' => new \DateTime(),
            'md5hash' => md5_file($filename),
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

    protected $size = 0;

    protected $width = null;

    protected $height = null;

    protected $userName = null;

    protected $md5Hash = null;

    protected $mimetype = 'application/octect-stream';

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
