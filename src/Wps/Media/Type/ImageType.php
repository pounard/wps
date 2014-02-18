<?php

namespace Wps\Media\Type;

use Wps\Media\Media;
use Wps\Util\Date;
use Wps\Util\FileSystem;

use Smvc\Core\AbstractApplicationAware;
use Wps\Media\Exif;

class ImageType extends AbstractApplicationAware implements TypeInterface
{
    /**
     * Extracted EXIF sections
     */
    const EXIF_SECTIONS = "COMPUTED,IFD0,COMMENT,EXIF";

    public function findMetadata(Media $media)
    {
        $ret = array();

        $config = $this->getApplication()->getConfig();
        $filename = FileSystem::pathJoin($config['directory/public'], 'full', $media->getRealPath());
        $updates = array();

        if ($sizes = getimagesize($filename)) {
            $updates['width'] = $sizes[0];
            $updates['height'] = $sizes[1];
        }

        if (function_exists('exif_read_data')) {
            foreach (exif_read_data($filename, self::EXIF_SECTIONS, true) as $section => $values) {
                // Sad but true story the function batlantly ignores our
                // sections parameters and returns everything...
                if (false !== strpos(self::EXIF_SECTIONS, $section)) {
                    foreach ($values as $key => $value) {
                        // Exclude some garbage we got on some photos
                        if ("MakerNote" !== $key && 0 !== strpos($key, "Undefined") && 0 !== strpos($key, "Thumbnail")) {
                            // It seems that we often inherit from stupid
                            // empty values from the EXIF data, it also
                            // excludes most of the weird binary non readable
                            // values
                            if ("0" === $value || (!empty($value) && preg_match('/[a-zA-Z0-9]+/', $value))) {
                                $ret[$key][] = $value;
                            }
                        }
                    }
                }

                if (isset($ret['DateTimeOriginal'])) {
                    $updates['userDate'] = \DateTime::createFromFormat(
                        Date::EXIF_DATETIME,
                        $ret['DateTimeOriginal'][0]
                    );
                }
                /*
                 * FIXME: Do not try this this may end up on bad behaviors;
                 * Don't know weither some camera did it wrong or php
                 * exif_read_data() is failing but it happens that some
                 * dimensions are inverted
                 *
                if (isset($ret['Width'])) {
                    $updates['width'] = $ret['Width'][0];
                }
                if (isset($ret['Height'])) {
                    $updates['height'] = $ret['Height'][0];
                }
                 */
                if (isset($ret['Orientation'])) {
                    $updates['orientation'] = (int)$ret['Orientation'][0];
                }
            }

            if (isset($updates['orientation'])) {
                if (isset($updates['width'])) {
                    switch ($updates['orientation']) {

                        case Exif::ORIENTATION_LEFTTOP:
                        case Exif::ORIENTATION_RIGHTTOP:
                        case Exif::ORIENTATION_RIGHTBOTTOM:
                        case Exif::ORIENTATION_LEFTBOTTOM:
                            $height = $updates['width'];
                            $updates['width'] = $updates['height'];
                            $updates['height'] = $height;
                            break;
                    }
                }
            } else {
                $updates['orientation'] = Exif::ORIENTATION_TOPLEFT;
            }

            if (!empty($updates)) {
                $media->fromArray($updates);
            }

            return $ret;
        }
    }
}
