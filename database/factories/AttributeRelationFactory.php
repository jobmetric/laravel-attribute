<?php

namespace JobMetric\Attribute\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JobMetric\Attribute\Models\AttributeRelation;

/**
 * @extends Factory<AttributeRelation>
 */
class AttributeRelationFactory extends Factory
{
    protected $model = AttributeRelation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'attributable_type' => null,
            'attributable_id' => null,
            'attribute_id' => null,
            'is_variant' => $this->faker->boolean,
            'is_special' => $this->faker->boolean,
        ];
    }

    /**
     * set attributable
     *
     * @param string $attributableType
     * @param string $attributableId
     *
     * @return static
     */
    public function setAttributable(string $attributableType, string $attributableId): static
    {
        return $this->state(fn(array $attributes) => [
            'attributable_type' => $attributableType,
            'attributable_id' => $attributableId
        ]);
    }

    /**
     * set attribute_id
     *
     * @param int $attributeId
     *
     * @return static
     */
    public function setAttributeId(int $attributeId): static
    {
        return $this->state(fn(array $attributes) => [
            'attribute_id' => $attributeId
        ]);
    }

    /**
     * set is_variant
     *
     * @param bool $isVariant
     *
     * @return static
     */
    public function setIsVariant(bool $isVariant): static
    {
        return $this->state(fn(array $attributes) => [
            'is_variant' => $isVariant
        ]);
    }

    /**
     * set is_special
     *
     * @param bool $isSpecial
     *
     * @return static
     */
    public function setIsSpecial(bool $isSpecial): static
    {
        return $this->state(fn(array $attributes) => [
            'is_special' => $isSpecial
        ]);
    }
}
