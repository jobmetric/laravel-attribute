<?php

namespace JobMetric\Attribute\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JobMetric\Attribute\Models\AttributeRelation;
use JobMetric\Translation\Models\Translation;

/**
 * @property int $id
 * @property string $type
 * @property bool $is_gallery
 * @property bool $is_special
 * @property bool $is_filter
 * @property int $ordering
 * @property mixed $created_at
 * @property mixed $updated_at
 *
 * @property Translation[] $translations
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
            'type' => $this->type,
            'is_gallery' => $this->is_gallery,
            'is_special' => $this->is_special,
            'is_filter' => $this->is_filter,
            'ordering' => $this->ordering,
            'created_at' => Carbon::make($this->created_at)->format('Y-m-d H:i:s'),
            'updated_at' => Carbon::make($this->updated_at)->format('Y-m-d H:i:s'),

            'translations' => translationResourceData($this->translations, $translationLocale),

            'attributeRelations' => $this->whenLoaded('attributeRelations', function () {
                return AttributeRelationResource::collection($this->attributeRelations);
            }),
        ];
    }
}
