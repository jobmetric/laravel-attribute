<?php

namespace JobMetric\Attribute\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JobMetric\Attribute\Models\Attribute;

/**
 * @extends Factory<Attribute>
 */
class AttributeFactory extends Factory
{
    /**
     * Default type keys aligned with package config attribute.types (registry defaults).
     *
     * @var list<string>
     */
    private const DEFAULT_TYPES = [
        'radio',
        'checkbox',
        'select',
        'color',
        'card',
        'image',
        'input',
    ];

    protected $model = Attribute::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type'       => $this->faker->randomElement(self::DEFAULT_TYPES),
            'is_special' => $this->faker->boolean(25),
            'is_filter'  => $this->faker->boolean(35),
            'ordering'   => $this->faker->numberBetween(0, 100),
        ];
    }

    /**
     * Set the attribute type, which should correspond to a registered type in the package configuration.
     *
     * @param string $type AttributeTypeRegistry / config key
     *
     * @return static
     */
    public function setType(string $type): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => $type,
        ]);
    }

    /**
     * Set whether the attribute is considered special (e.g., highlighted in UI).
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

    /**
     * Set whether the attribute should be used as a filter in listings.
     *
     * @param bool $isFilter
     *
     * @return static
     */
    public function setIsFilter(bool $isFilter): static
    {
        return $this->state(fn (array $attributes) => [
            'is_filter' => $isFilter,
        ]);
    }

    /**
     * Set the ordering value for the attribute.
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
