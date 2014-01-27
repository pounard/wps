<?php

namespace Wps\Controller\App;

use Smvc\Controller\AbstractController;
use Smvc\Dispatch\RequestInterface;
use Smvc\View\View;
use Smvc\Dispatch\Http\RedirectResponse;

class TimelineController extends AbstractController
{
    public function getAction(RequestInterface $request, array $args)
    {
        $container = $this->getContainer();
        $albumDao = $container->getDao('album');
        $mediaDao = $container->getDao('media');
        $account = $container->getSession()->getAccount();

        // @todo Order form/links
        // @todo Filtering facet links (missing metadata in base)

        // FIXME: Order desc per default
        $query = $this->getQueryFromRequest($request);
        $albums = $albumDao->loadAllFor(
            array(
                'accountId' => $account->getId(),
            ),
            $query->getLimit(),
            $query->getOffset()
        );

        // Existing user set preview identifiers
        $previewIdList = array();
        $previewMediaMap = array();
        foreach ($albums as $album) {
            if ($id = $album->getPreviewMediaId()) {
                $previewIdList[] = $id;
            } else {
                // Find first media of this album
                // This is worst case scenario and I hop this won't happen
                $albumId = $album->getId();
                if ($media = $mediaDao->loadFirst(array('albumId' => $albumId))) {
                    $previewMediaMap[$albumId] = $media;
                }
            }
        }
        if (!empty($previewIdList)) {
            foreach ($mediaDao->loadAll($previewIdList) as $media) {
                // Preview can only be a media of the selected album
                $previewMediaMap[$media->getAlbumId()] = $media;
            }
        }

        return new View(array(
            'albums'   => $albums,
            'owner'    => $account,
            'previews' => $previewMediaMap,
        ), 'app/timeline');
    }
}
