<?php

namespace JobMetric\Attribute\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JobMetric\Attribute\Models\AttributeRelation;
use JobMetric\Attribute\Models\AttributeValue;

/**
 * Class AttributeRelationValueResource
 *
 * Transforms the AttributeRelationValue pivot into a structured JSON resource.
 *
 * @property int $attribute_relation_id
 * @property int $attribute_value_id
 *
 * @property-read AttributeRelation $attributeRelation
 * @property-read AttributeValue $attributeValue
 */
class AttributeRelationValueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'attribute_relation_id' => $this->attribute_relation_id,
            'attribute_value_id'    => $this->attribute_value_id,

            'attribute_relation' => $this->whenLoaded('attributeRelation', function () {
                return AttributeRelationResource::make($this->attributeRelation);
            }),

            'attribute_value' => $this->whenLoaded('attributeValue', function () {
                return AttributeValueResource::make($this->attributeValue);
            }),
        ];
    }
}
