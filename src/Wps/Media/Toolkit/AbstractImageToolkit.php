<?php

namespace Wps\Media\Toolkit;

use Wps\Util\FileSystem;

use Smvc\Error\LogicError;
use Smvc\Error\NotFoundError;

abstract class AbstractImageToolkit implements ImageToolkitInterface
{
    /**
     * Ensure input and output files can be manipulated before running
     * the procedure
     *
     * @param string $inFile
     * @param string $outFile
     */
    protected function ensureFiles($inFile, $outFile)
    {
        if (!file_exists($inFile)) {
            throw new NotFoundError();
        }
        if (!is_readable($inFile)) {
            throw new LogicError(sprintf("File is not readable '%s'", $inFile));
        }
        if (file_exists($outFile)) {
            if (is_writable($outFile)) {
                trigger_error(sprintf("Will overwrite file '%s'", $outFile), E_USER_NOTICE);
            } else {
                throw new LogicError(sprintf("File is not writable '%s'", $outFile));
            }
        } else {
            FileSystem::ensureDirectory(dirname($outFile), true, true);
        }
    }
}
