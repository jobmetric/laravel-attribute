<?php

namespace JobMetric\Attribute\Events;

use JobMetric\Attribute\Models\AttributeValue;

class AttributeValueStoreEvent
{
    /**
     * Create a new event instance.
     */
    public function __construct(
        public readonly AttributeValue $attributeValue,
        public readonly array          $data,
    )
    {
    }
}
