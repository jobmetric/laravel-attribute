<?php

namespace JobMetric\Attribute\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JobMetric\Attribute\Models\GalleryVariationAttributeValue;

/**
 * @extends Factory<GalleryVariationAttributeValue>
 */
class GalleryVariationAttributeValueFactory extends Factory
{
    protected $model = GalleryVariationAttributeValue::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'gallery_variation_id' => null,
            'attribute_relation_id' => null,
            'attribute_value_id' => null,
        ];
    }

    /**
     * set gallery_variation_id
     *
     * @param int $galleryVariationId
     *
     * @return static
     */
    public function galleryVariationId(int $galleryVariationId): self
    {
        return $this->state(fn() => [
            'gallery_variation_id' => $galleryVariationId,
        ]);
    }

    /**
     * set attribute_relation_id
     *
     * @param int $attributeRelationId
     *
     * @return static
     */
    public function attributeRelationId(int $attributeRelationId): self
    {
        return $this->state(fn() => [
            'attribute_relation_id' => $attributeRelationId,
        ]);
    }

    /**
     * set attribute_value_id
     *
     * @param int $attributeValueId
     *
     * @return static
     */
    public function attributeValueId(int $attributeValueId): self
    {
        return $this->state(fn() => [
            'attribute_value_id' => $attributeValueId,
        ]);
    }
}
