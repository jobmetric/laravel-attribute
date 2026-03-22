<?php

namespace JobMetric\Attribute\Events\Attribute;

use JobMetric\Attribute\Models\Attribute;
use JobMetric\EventSystem\Contracts\DomainEvent;
use JobMetric\EventSystem\Support\DomainEventDefinition;

readonly class AttributeUpdateEvent implements DomainEvent
{
    /**
     * Create a new event instance.
     */
    public function __construct(
        public Attribute $attribute,
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
        return 'attribute.updated';
    }

    /**
     * Returns the full metadata definition for this domain event.
     *
     * @return DomainEventDefinition
     */
    public static function definition(): DomainEventDefinition
    {
        return new DomainEventDefinition(self::key(), 'attribute::base.events.groups.attribute', 'attribute::base.events.attribute_updated.title', 'attribute::base.events.attribute_updated.description', 'fas fa-edit', [
            'attribute',
            'storage',
            'management',
        ]);
    }
}
