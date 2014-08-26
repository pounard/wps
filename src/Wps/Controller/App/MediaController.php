<?php

namespace Wps\Controller\App;

use Wps\Media\Media;
use Wps\Util\Date;
use Wps\Util\FileSystem;

use Smvc\Controller\AbstractController;
use Smvc\Dispatch\DefaultRequest;
use Smvc\Dispatch\RequestInterface;
use Smvc\Error\NotFoundError;
use Smvc\View\View;

class MediaController extends AbstractController
{
    public function getAction(RequestInterface $request, array $args)
    {
        if (count($args) < 1) {
            throw new NotFoundError();
        }

        $app = $this->getApplication();
        $config = $app->getConfig();
        $albumDao = $app->getDao('album');
        $mediaDao = $app->getDao('media');

        $query = $this->getQueryFromRequest($request);
        $media = $mediaDao->load($args[0]);
        $album = $albumDao->load($media->getAlbumId());

        // Find previous and next entry by album
        $prev = null;
        $next = null;
        $db = $app->getDatabase();
        $queryArgs = array(
            $media->getAlbumId(),
            $media->getUserDate()->format(Date::MYSQL_DATETIME),
            $media->getId(),
        );
        $st = $db->prepare("
            SELECT id FROM media
            WHERE id_album = ? AND ts_user_date <= ? AND id NOT IN (?)
            ORDER BY ts_user_date DESC, id DESC
            LIMIT 1
        ");
        $st->setFetchMode(\PDO::FETCH_COLUMN, 0);
        if ($st->execute($queryArgs)) {
            foreach ($st as $value) {
                $prev = $mediaDao->load($value);
            }
        }
        $st = $db->prepare("
            SELECT id FROM media
            WHERE id_album = ? AND ts_user_date >= ? AND id NOT IN (?)
            ORDER BY ts_user_date ASC, id ASC
            LIMIT 1
        ");
        $st->setFetchMode(\PDO::FETCH_COLUMN, 0);
        if ($st->execute($queryArgs)) {
            foreach ($st as $value) {
                $next = $mediaDao->load($value);
            }
        }

        return new View(array(
            'base'    => DefaultRequest::createUrlFromRequest($request, ""),
            'cdn'     => DefaultRequest::createUrlFromRequest($request, $config['directory/web']),
            'album'   => $album,
            'media'   => $media,
            'prev'    => $prev,
            'next'    => $next,
            'size'    => isset($args[1]) ? $args[1] : 'w600',
            'owner'   => $app->getAccountProvider()->getAccountById($media->getAccountId()),
        ), 'app/media');
    }
}
