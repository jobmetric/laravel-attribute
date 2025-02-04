<?php

namespace JobMetric\Attribute\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JobMetric\Attribute\Models\AttributeRelation;
use JobMetric\Attribute\Models\AttributeValue;
use JobMetric\Attribute\Models\GalleryVariation;

/**
 * @property int $gallery_variation_id
 * @property int $attribute_relation_id
 * @property int $attribute_value_id
 *
 * @property-read GalleryVariation $galleryVariation
 * @property-read AttributeRelation $attributeRelation
 * @property-read AttributeValue $attributeValue
 */
class GalleryVariationAttributeValueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'gallery_variation_id' => $this->gallery_variation_id,
            'attribute_relation_id' => $this->attribute_relation_id,
            'attribute_value_id' => $this->attribute_value_id,

            'galleryVariation' => $this->whenLoaded('galleryVariation', function () {
                return new GalleryVariationResource($this->galleryVariation);
            }),

            'attributeRelation' => $this->whenLoaded('attributeRelation', function () {
                return new AttributeRelationResource($this->attributeRelation);
            }),

            'attributeValue' => $this->whenLoaded('attributeValue', function () {
                return new AttributeValueResource($this->attributeValue);
            }),
        ];
    }
}
