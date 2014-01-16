<?php

namespace Wps\Media\Type;

use Wps\Media\Media;

interface TypeInterface
{
    /**
     * Find media metadata
     *
     * @param Media $media
     *
     * @return array
     *   Key value pairs where keys are attribute names and values are arrays
     *   of values which means that properties can be multivalued; Each value
     *   can be any scalar type
     */
    public function findMetadata(Media $media);
}
