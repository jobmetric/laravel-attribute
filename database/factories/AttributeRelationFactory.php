<?php

namespace JobMetric\Attribute\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
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
            'attributable_id'   => null,
            'attribute_id'      => null,
            'is_variant'        => $this->faker->boolean(30),
            'is_special'        => $this->faker->boolean(20),
        ];
    }

    /**
     * Set the attributable model type and ID for this attribute relation.
     *
     * @param class-string<Model>|string $attributableType Morph class or alias
     * @param int|string $attributableId
     *
     * @return static
     */
    public function setAttributable(string $attributableType, int|string $attributableId): static
    {
        return $this->state(fn (array $attributes) => [
            'attributable_type' => $attributableType,
            'attributable_id'   => $attributableId,
        ]);
    }

    /**
     * Set the associated attribute ID for this relation.
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
     * Set whether this attribute relation is considered a variant (e.g., used for product variants).
     *
     * @param bool $isVariant
     *
     * @return static
     */
    public function setIsVariant(bool $isVariant): static
    {
        return $this->state(fn (array $attributes) => [
            'is_variant' => $isVariant,
        ]);
    }

    /**
     * Set whether this attribute relation is considered special (e.g., highlighted in UI).
     *
     * @param bool $isSpecial
     *
     * @return static
     */
    public function setIsSpecial(bool $isSpecial): static
    {
        return $this->state(fn (array $attributes) => [
            'is_special' => $isSpecial,
        ]);
    }
}
