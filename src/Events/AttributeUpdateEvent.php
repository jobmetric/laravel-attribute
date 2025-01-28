<?php

namespace JobMetric\Attribute\Events;

use JobMetric\Attribute\Models\Attribute;

class AttributeUpdateEvent
{
    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Attribute $taxonomy,
        public readonly array     $data,
    )
    {
    }
}
