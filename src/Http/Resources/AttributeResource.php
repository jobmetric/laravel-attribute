<?php

namespace JobMetric\Attribute\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JobMetric\Attribute\Models\AttributeRelation;
use JobMetric\Attribute\Models\AttributeValue;
use JobMetric\Translation\Models\Translation;

/**
 * @property int $id
 * @property string $name
 * @property string $type
 * @property bool $is_special
 * @property bool $is_filter
 * @property int $ordering
 * @property mixed $created_at
 * @property mixed $updated_at
 *
 * @property Translation[] $translations
 * @property AttributeValue[] $attributeValues
 * @property AttributeRelation[] $attributeRelations
 */
class AttributeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        global $translationLocale;

        return [
            'id' => $this->id,
            'name' => $this->whenHas('name', $this->name),
            'type' => $this->type,
            'is_special' => $this->is_special,
            'is_filter' => $this->is_filter,
            'ordering' => $this->ordering,
            'created_at' => Carbon::make($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::make($this->updated_at)->format('Y-m-d H:i:s'),

            'translations' => translationResourceData($this->translations, $translationLocale),

            'attributeValues' => $this->whenLoaded('attributeValues', function () {
                return AttributeValueResource::collection($this->attributeValues);
            }),

            'attributeRelations' => $this->whenLoaded('attributeRelations', function () {
                return AttributeRelationResource::collection($this->attributeRelations);
            }),
        ];
    }
}
