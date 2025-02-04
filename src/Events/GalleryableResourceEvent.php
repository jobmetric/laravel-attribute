<?php

namespace JobMetric\Attribute\Events;

class GalleryableResourceEvent
{
    /**
     * The galleryable model instance.
     *
     * @var mixed
     */
    public mixed $galleryable;

    /**
     * The resource to be filled by the listener.
     *
     * @var mixed|null
     */
    public mixed $resource;

    /**
     * Create a new event instance.
     *
     * @param mixed $galleryable
     */
    public function __construct(mixed $galleryable)
    {
        $this->galleryable = $galleryable;
        $this->resource = null;
    }
}
