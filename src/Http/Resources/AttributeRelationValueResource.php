<?php

namespace JobMetric\Attribute\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JobMetric\Attribute\Models\AttributeRelation;
use JobMetric\Attribute\Models\AttributeValue;

/**
 * @property int $attribute_relation_id
 * @property int $attribute_value_id
 *
 * @property AttributeRelation $attributeRelation
 * @property AttributeValue $attributeValue
 */
class AttributeRelationValueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'attribute_relation_id' => $this->attribute_relation_id,
            'attribute_value_id' => $this->attribute_value_id,

            'attributeRelation' => $this->whenLoaded('attributeRelation', function () {
                return new AttributeRelationResource($this->attributeRelation);
            }),

            'attributeValue' => $this->whenLoaded('attributeValue', function () {
                return new AttributeValueResource($this->attributeValue);
            }),
        ];
    }
}
