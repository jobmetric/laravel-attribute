<?php

namespace JobMetric\Attribute\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JobMetric\Attribute\Models\Attribute;

/**
 * @extends Factory<Attribute>
 */
class AttributeFactory extends Factory
{
    protected $model = Attribute::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => null,
            'is_gallery' => false,
            'is_special' => false,
            'is_filter' => false,
            'ordering' => 0,
        ];
    }

    /**
     * set type
     *
     * @param string $type
     *
     * @return static
     */
    public function setType(string $type): static
    {
        return $this->state(fn(array $attributes) => [
            'type' => $type
        ]);
    }

    /**
     * set is_gallery
     *
     * @param bool $isGallery
     *
     * @return static
     */
    public function setIsGallery(bool $isGallery): static
    {
        return $this->state(fn(array $attributes) => [
            'is_gallery' => $isGallery
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

    /**
     * set is_filter
     *
     * @param bool $isFilter
     *
     * @return static
     */
    public function setIsFilter(bool $isFilter): static
    {
        return $this->state(fn(array $attributes) => [
            'is_filter' => $isFilter
        ]);
    }

    /**
     * set ordering
     *
     * @param int $ordering
     *
     * @return static
     */
    public function setOrdering(int $ordering): static
    {
        return $this->state(fn(array $attributes) => [
            'ordering' => $ordering
        ]);
    }
}
