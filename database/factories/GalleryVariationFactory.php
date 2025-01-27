<?php

namespace JobMetric\Attribute\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use JobMetric\Attribute\Models\GalleryVariation;

/**
 * @extends Factory<GalleryVariation>
 */
class GalleryVariationFactory extends Factory
{
    protected $model = GalleryVariation::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'galleryable_type' => null,
            'galleryable_id' => null,
        ];
    }

    /**
     * set galleryable
     *
     * @param string $galleryableType
     * @param int $galleryableId
     *
     * @return static
     */
    public function setGalleryable(string $galleryableType, int $galleryableId): self
    {
        return $this->state([
            'galleryable_type' => $galleryableType,
            'galleryable_id' => $galleryableId,
        ]);
    }
}
