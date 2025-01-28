<?php

namespace JobMetric\Attribute\Events;

use JobMetric\Attribute\Models\Attribute;

class AttributeDeleteEvent
{
    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly Attribute $attribute,
    )
    {
    }
}
