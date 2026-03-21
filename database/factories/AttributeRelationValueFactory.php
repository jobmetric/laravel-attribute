<?php

namespace JobMetric\Attribute\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JobMetric\Attribute\Models\AttributeRelationValue;

/**
 * @extends Factory<AttributeRelationValue>
 */
class AttributeRelationValueFactory extends Factory
{
    protected $model = AttributeRelationValue::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'attribute_relation_id' => null,
            'attribute_value_id'    => null,
        ];
    }

    /**
     * Set the associated attribute relation ID for this relation value.
     *
     * @param int $attributeRelationId
     *
     * @return static
     */
    public function setAttributeRelationId(int $attributeRelationId): static
    {
        return $this->state(fn (array $attributes) => [
            'attribute_relation_id' => $attributeRelationId,
        ]);
    }

    /**
     * Set the associated attribute value ID for this relation value.
     *
     * @param int $attributeValueId
     *
     * @return static
     */
    public function setAttributeValueId(int $attributeValueId): static
    {
        return $this->state(fn (array $attributes) => [
            'attribute_value_id' => $attributeValueId,
        ]);
    }
}
