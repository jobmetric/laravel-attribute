<?php

namespace JobMetric\Attribute\Events;

use JobMetric\Attribute\Models\AttributeValue;

class AttributeValueDeleteEvent
{
    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly AttributeValue $attributeValue,
    )
    {
    }
}
