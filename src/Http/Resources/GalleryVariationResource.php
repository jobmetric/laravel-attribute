<?php

namespace JobMetric\Attribute\Http\Resources;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JobMetric\Attribute\Models\GalleryVariationAttributeValue;

/**
 * @property int $id
 * @property string $galleryable_type
 * @property int $galleryable_id
 * @property Carbon|null $created_at
 * @property mixed $galleryable_resource
 *
 * @property-read Model $galleryable
 * @property-read Collection|GalleryVariationAttributeValue[] $galleryVariationAttributeValues
 * @property-read int|null $gallery_variation_attribute_values_count
 */
class GalleryVariationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'galleryable_type' => $this->galleryable_type,
            'galleryable_id' => $this->galleryable_id,
            'created_at' => Carbon::make($this->created_at)->format('Y-m-d H:i:s'),

            'galleryable' => $this?->galleryable_resource,

            'galleryVariationAttributeValues' => $this->whenLoaded('galleryVariationAttributeValues', function () {
                return GalleryVariationAttributeValueResource::collection($this->galleryVariationAttributeValues);
            }),
        ];
    }
}
