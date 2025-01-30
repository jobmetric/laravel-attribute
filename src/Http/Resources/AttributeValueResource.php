<?php

namespace JobMetric\Attribute\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JobMetric\Attribute\Models\Attribute;
use JobMetric\Attribute\Models\AttributeRelation;
use JobMetric\Translation\Models\Translation;

/**
 * @property int $id
 * @property string $name
 * @property int $attribute_id
 * @property int $ordering
 * @property mixed $created_at
 * @property mixed $updated_at
 *
 * @property Translation[] $translations
 * @property Attribute $attribute
 * @property AttributeRelation[] $attributeRelations
 */
class AttributeValueResource extends JsonResource
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
            'ordering' => $this->ordering,
            'created_at' => Carbon::make($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::make($this->updated_at)->format('Y-m-d H:i:s'),

            'translations' => translationResourceData($this->translations, $translationLocale),

            'attribute' => $this->whenLoaded('attribute', function () {
                return AttributeResource::collection($this->attribute);
            }),

            /*'attributeRelations' => $this->whenLoaded('attributeRelations', function () {
                return AttributeRelationResource::collection($this->attributeRelations);
            }),*/
        ];
    }
}
