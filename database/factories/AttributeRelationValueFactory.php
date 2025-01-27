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
            'attribute_value_id' => null,
        ];
    }

    /**
     * set attribute_relation_id
     *
     * @param int $attribute_relation_id
     *
     * @return static
     */
    public function attributeRelationId(int $attribute_relation_id): static
    {
        return $this->state(fn() => [
            'attribute_relation_id' => $attribute_relation_id,
        ]);
    }

    /**
     * set attribute_value_id
     *
     * @param int $attribute_value_id
     *
     * @return static
     */
    public function attributeValueId(int $attribute_value_id): static
    {
        return $this->state(fn() => [
            'attribute_value_id' => $attribute_value_id,
        ]);
    }
}
