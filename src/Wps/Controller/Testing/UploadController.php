<?php

namespace Wps\Controller\Testing;

use Smvc\Controller\AbstractController;
use Smvc\Core\Message;
use Smvc\Dispatch\Http\RedirectResponse;
use Smvc\Dispatch\RequestInterface;
use Smvc\View\View;

class UploadController extends AbstractController
{
    public function getAction(RequestInterface $request, array $args)
    {
        $container = $this->getContainer();
        $account   = $container->getSession()->getAccount();
        $config    = $container->getConfig();
        $uploadDir = $config['directory/upload'] . '/' . $account->getId();

        $iterator = new \CallbackFilterIterator(
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator(
                    $uploadDir,
                    \FilesystemIterator::KEY_AS_PATHNAME |
                    \FilesystemIterator::CURRENT_AS_FILEINFO |
                    \FilesystemIterator::SKIP_DOTS
               ),
               \RecursiveIteratorIterator::SELF_FIRST
            ),
            function (\SplFileInfo $current, $key, $iterator) {
                return $current->isDir();
            }
        );

        $directories = array();
        foreach ($iterator as $key => $file) {
            if ($file instanceof \SplFileInfo) {
                $files = new \CallbackFilterIterator(
                    new \FilesystemIterator(
                        $file->getPathname(),
                        \FilesystemIterator::CURRENT_AS_FILEINFO |
                        \FilesystemIterator::SKIP_DOTS
                    ),
                    function (\SplFileInfo $current, $key, $iterator) {
                        return !$current->isDir();
                    }
                );
                if ($count = iterator_count($files)) {
                    $directories[] = array(
                        'filename' => $file->getFilename(),
                        'path'     => substr($file->getPathname(), strlen($uploadDir)),
                        'label'    => $file->getFilename() . ' (' . $count . ')',
                    );
                }
            }
        }

        if (empty($directories)) {
            $messager = $this
                ->getContainer()
                ->getMessager()
                ->addMessage("No files to import", Message::TYPE_WARNING);

            return new RedirectResponse('app/index');
        }

        return new View(array('directories' => $directories), 'testing/upload');
    }

    public function postAction(RequestInterface $request, array $args)
    {
        $form = $this->getForm();
        $form->setValues($request->getContent());

        if ($form->validate($form->getValues())) {

            $this
                ->getContainer()
                ->getConfig()
                ->offsetSet('identity', $form->getValues());

            $this
                ->getContainer()
                ->getMessager()
                ->addMessage("Your preferences have been saved", Message::TYPE_SUCCESS);

            return new RedirectResponse($request->getResource());

        } else {
            $messager = $this->getContainer()->getMessager();
            if ($messages = $form->getValidationMessages()) {
                foreach ($messages as $message) {
                    $messager->addMessage($message, Message::TYPE_ERROR);
                }
            } else {
                $messager->addMessage("Validation errors", Message::TYPE_ERROR);
            }

            return new View(array(
                'defaults' => $form->getValues(),
                'placeholders' => $form->getPlaceHolders(),
            ), 'app/settings/index');
        }
    }
}
