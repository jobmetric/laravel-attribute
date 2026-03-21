<?php

namespace JobMetric\Attribute\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JobMetric\Attribute\Models\Attribute;

/**
 * @property int $id
 * @property string $attributable_type
 * @property int $attributable_id
 * @property int $attribute_id
 * @property bool $is_variant
 * @property bool $is_special
 * @property Carbon $created_at
 *
 * @property Attribute $attribute
 * @property mixed $attributable
 * @property mixed $attributable_resource
 */
class AttributeRelationResource extends JsonResource
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
            'attributable_type' => $this->attributable_type,
            'attributable_id' => $this->attributable_id,
            'attribute_id' => $this->attribute_id,
            'is_variant' => $this->is_variant,
            'is_special' => $this->is_special,
            'created_at' => Carbon::make($this->created_at)->format('Y-m-d H:i:s'),

            'attribute' => $this->whenLoaded('attribute', function () {
                return new AttributeResource($this->attribute);
            }),

            'attributable' => $this?->attributable_resource
        ];
    }
}
