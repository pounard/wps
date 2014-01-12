<?php

namespace Wps\Media\Toolkit;

use Smvc\Error\NotImplementedError;
use Smvc\Error\LogicError;

/**
 * Uses the convert system command
 */
class ExternalImagickImageToolkit extends AbstractImageToolkit
{
    /**
     * Scale and crop image
     *
     * @param string $inFile
     * @param string $outFile
     * @param int $width
     * @param int $height
     */
    public function scaleAndCrop($inFile, $outFile, $width, $height)
    {
        $this->ensureFiles($inFile, $outFile);

        $size = ((int)$width) . "x" . ((int)$height);

        $command = array(
            escapeshellcmd("convert"),
            escapeshellarg($inFile),
            "-resize",
            "'" . $size . "^'",
            "-gravity",
            "center",
            "-crop",
            "'" . $size . "+0+0'",
            escapeshellarg($outFile),
        );

        $ret = 0;
        system(implode(" ", $command), $ret);

        if (0 !== ((int)$ret)) {
            throw new LogicError("Could not exec command", $ret);
        }
    }

    /**
     * Scale image
     *
     * @param string $inFile
     * @param string $outFile
     * @param int $maxWidth
     * @param int $maxHeight
     */
    public function scaleTo($inFile, $outFile, $maxWidth = null, $maxHeight = null)
    {
        $this->ensureFiles($inFile, $outFile);

        throw new NotImplementedError();
    }
}
