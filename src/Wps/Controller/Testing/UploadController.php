<?php

namespace Wps\Controller\Testing;

use Wps\Media\Import\FilesystemImporter;
use Wps\Util\FileSystem;

use Smvc\Controller\AbstractController;
use Smvc\Core\Message;
use Smvc\Dispatch\Http\RedirectResponse;
use Smvc\Dispatch\RequestInterface;
use Smvc\View\View;

class UploadController extends AbstractController
{
    public function getUploadDir()
    {
        $container = $this->getContainer();
        $account   = $container->getSession()->getAccount();
        $config    = $container->getConfig();
        $uploadDir = FileSystem::pathJoin(array($config['directory/upload'], $account->getId()));

        return $uploadDir;
    }

    public function getAction(RequestInterface $request, array $args)
    {
        $uploadDir = $this->getUploadDir();

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
        $container = $this->getContainer();

        $values = $request->getContent();
        $errors = array();

        $albumCount = 0;
        $mediaCount = 0;

        if (is_array($values) && !empty($values['directories'])) {
            $uploadDir = $this->getUploadDir();

            $importer = new FilesystemImporter(
                $container->get('dao.media'),
                $container->get('dao.album'),
                $uploadDir
            );

            foreach ($values['directories'] as $directory) {
                $importer->importFromFolder($directory);
            }

        } else {
            $errors[] = "Please select at least one album or click cancel";
        }
die();
        $messager = $this->getContainer()->getMessager();

        if (!empty($errors)) {
            foreach ($errors as $error) {
                $messager->addMessage($error, Message::TYPE_ERROR);
            }

            return $this->getAction($request, $args);

        } else {

            $messager->addMessage(
                "Added " . $mediaCount . " file(s) in " . $albumCount . " album(s)",
                Message::TYPE_SUCCESS
            );

            return new RedirectResponse('app/index');
        }
    }
}
