<?php

namespace JobMetric\Attribute\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;
use JobMetric\Attribute\Models\AttributeRelation;
use JobMetric\Attribute\Models\AttributeValue;
use JobMetric\Translation\Models\Translation;

/**
 * Class AttributeResource
 *
 * Transforms the Attribute model into a structured JSON resource.
 *
 * @property int $id
 * @property string|null $name
 * @property string $type
 * @property bool $is_special
 * @property bool $is_filter
 * @property int $ordering
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Translation[] $translations
 * @property-read AttributeValue[] $attributeValues
 * @property-read AttributeRelation[] $attributeRelations
 */
class AttributeResource extends JsonResource
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
            'id'         => $this->id,
            'name'       => $this->whenHas('name', $this->name),
            'type'       => $this->type,
            'is_special' => (bool) $this->is_special,
            'is_filter'  => (bool) $this->is_filter,
            'ordering'   => $this->ordering,

            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),

            'translations' => $this->whenLoaded('translations', function () use ($translationLocale) {
                return translationResourceData($this->translations, $translationLocale);
            }),

            'attribute_values' => $this->whenLoaded('attributeValues', function () {
                return AttributeValueResource::collection($this->attributeValues);
            }),

            'attribute_relations' => $this->whenLoaded('attributeRelations', function () {
                return AttributeRelationResource::collection($this->attributeRelations);
            }),
        ];
    }
}
