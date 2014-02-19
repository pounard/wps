<?php

namespace Wps\Controller\App\Albums;

use Smvc\Controller\AbstractController;
use Smvc\Core\Message;
use Smvc\Dispatch\RequestInterface;
use Smvc\Dispatch\Http\RedirectResponse;
use Smvc\Error\NotFoundError;
use Smvc\View\View;

class ShareController extends AbstractController
{
    public function getAction(RequestInterface $request, array $args)
    {
        $app = $this->getApplication();
        $albumDao = $app->getDao('album');
        $contactDao = $app->getDao('contact');
        $album = $albumDao->load($args[0]);

        // Already shared too

        // Share potentials

        return new View(array(
            'album'  => $album,
        ), 'app/album/share');
    }

    public function postAction(RequestInterface $request, array $args)
    {
        if (1 !== count($args)) {
            throw new NotFoundError();
        }
/*
        $app = $this->getApplication();
        $albumDao = $app->getDao('album');
        $album = $albumDao->load($args[0]);
        $values = $request->getContent();

        $data = array();

        // @todo Filtering and other stuff
        if (empty($values['userName'])) {
            $data['userName'] = null;
        } else {
            $data['userName'] = $values['userName'];
        }

        $album->fromArray($data);
        $albumDao->save($album);
*/
        $app->getMessager()->addMessage("Album details have been updated", Message::TYPE_SUCCESS);

        return new RedirectResponse('app/albums/' . $album->getId());
    }
}
