<?php

namespace JobMetric\Attribute\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use JobMetric\Attribute\Models\Attribute;
use JobMetric\Attribute\Models\AttributeRelation;
use JobMetric\Translation\Models\Translation;

/**
 * Class AttributeValueResource
 *
 * Transforms the AttributeValue model into a structured JSON resource.
 *
 * @property int $id
 * @property string|null $name
 * @property int $attribute_id
 * @property int $ordering
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Translation[] $translations
 * @property-read Attribute $attribute
 * @property-read AttributeRelation[] $attributeRelations
 */
class AttributeValueResource extends JsonResource
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
        global $translationLocale;

        return [
            'id'           => $this->id,
            'name'         => $this->whenHas('name', $this->name),
            'attribute_id' => $this->attribute_id,
            'ordering'     => $this->ordering,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            'translations' => $this->whenLoaded('translations', function () use ($translationLocale) {
                return translationResourceData($this->translations, $translationLocale);
            }),

            'attribute' => $this->whenLoaded('attribute', function () {
                return AttributeResource::make($this->attribute);
            }),

            'attribute_relations' => $this->whenLoaded('attributeRelations', function () {
                return AttributeRelationResource::collection($this->attributeRelations);
            }),
        ];
    }
}
