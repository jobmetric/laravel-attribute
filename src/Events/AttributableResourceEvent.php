<?php

namespace JobMetric\Attribute\Events;

class AttributableResourceEvent
{
    /**
     * The attributable model instance.
     *
     * @var mixed
     */
    public mixed $attributable;

    /**
     * The resource to be filled by the listener.
     *
     * @var mixed|null
     */
    public mixed $resource;

    /**
     * Create a new event instance.
     *
     * @param mixed $attributable
     */
    public function __construct(mixed $attributable)
    {
        $this->attributable = $attributable;
        $this->resource = null;
    }
}
