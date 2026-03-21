<?php

namespace JobMetric\Attribute\Http\Resources;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use JobMetric\Attribute\Models\Attribute;

/**
 * Class AttributeRelationResource
 *
 * Transforms the AttributeRelation model into a structured JSON resource.
 *
 * @property int $id
 * @property string $attributable_type
 * @property int $attributable_id
 * @property int $attribute_id
 * @property bool $is_variant
 * @property bool $is_special
 * @property Carbon|null $created_at
 *
 * @property-read Attribute $attribute
 * @property-read Model|null $attributable
 * @property-read mixed $attributable_resource
 */
class AttributeRelationResource extends JsonResource
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
            'id'                => $this->id,
            'attributable_type' => $this->attributable_type,
            'attributable_id'   => $this->attributable_id,
            'attribute_id'      => $this->attribute_id,
            'is_variant'        => (bool) $this->is_variant,
            'is_special'        => (bool) $this->is_special,

            'created_at' => $this->created_at?->toISOString(),

            'attribute' => $this->whenLoaded('attribute', function () {
                return AttributeResource::make($this->attribute);
            }),

            'attributable' => $this->whenLoaded('attributable', function () {
                return $this->attributable_resource;
            }),
        ];
    }
}
