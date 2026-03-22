<?php

namespace JobMetric\Attribute\Events\AttributeValue;

use JobMetric\Attribute\Models\AttributeValue;
use JobMetric\EventSystem\Contracts\DomainEvent;
use JobMetric\EventSystem\Support\DomainEventDefinition;

readonly class AttributeValueStoreEvent implements DomainEvent
{
    /**
     * Create a new event instance.
     */
    public function __construct(
        public AttributeValue $attributeValue,
        public array $data,
    ) {
    }

    /**
     * Returns the stable technical key for the domain event.
     *
     * @return string
     */
    public static function key(): string
    {
        return 'attribute_value.stored';
    }

    /**
     * Returns the full metadata definition for this domain event.
     *
     * @return DomainEventDefinition
     */
    public static function definition(): DomainEventDefinition
    {
        return new DomainEventDefinition(self::key(), 'attribute::base.events.groups.attribute_value', 'attribute::base.events.attribute_value_stored.title', 'attribute::base.events.attribute_value_stored.description', 'fas fa-save', [
            'attribute',
            'attribute_value',
            'storage',
            'management',
        ]);
    }
}
