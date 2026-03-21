<?php

namespace JobMetric\Attribute\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JobMetric\Attribute\Models\AttributeValue;

/**
 * @extends Factory<AttributeValue>
 */
class AttributeValueFactory extends Factory
{
    protected $model = AttributeValue::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'attribute_id' => null,
            'ordering'     => $this->faker->numberBetween(0, 50),
        ];
    }

    /**
     * Set the associated attribute ID for this attribute value, linking it to a specific attribute.
     *
     * @param int $attributeId
     *
     * @return static
     */
    public function setAttributeId(int $attributeId): static
    {
        return $this->state(fn (array $attributes) => [
            'attribute_id' => $attributeId,
        ]);
    }

    /**
     * Set the ordering value for this attribute value, which can be used to determine display order among sibling
     * values.
     *
     * @param int $ordering
     *
     * @return static
     */
    public function setOrdering(int $ordering): static
    {
        return $this->state(fn (array $attributes) => [
            'ordering' => $ordering,
        ]);
    }
}
