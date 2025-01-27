<?php

namespace JobMetric\Attribute\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * JobMetric\Attribute\Models\GalleryVariationAttributeValue
 *
 * @property int $gallery_variation_id
 * @property int $attribute_relation_id
 * @property int $attribute_value_id
 *
 * @property-read GalleryVariation $galleryVariation
 * @property-read AttributeRelation $attributeRelation
 * @property-read AttributeValue $attributeValue
 *
 * @method static find(int $int)
 */
class GalleryVariationAttributeValue extends Pivot
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'gallery_variation_id',
        'attribute_relation_id',
        'attribute_value_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'gallery_variation_id' => 'integer',
        'attribute_relation_id' => 'integer',
        'attribute_value_id' => 'integer',
    ];

    public function getTable()
    {
        return config('attribute.tables.attribute_value', parent::getTable());
    }

    /**
     * galleryVariation relation
     *
     * @return BelongsTo
     */
    public function galleryVariation(): BelongsTo
    {
        return $this->belongsTo(GalleryVariation::class, 'gallery_variation_id');
    }

    /**
     * attributeRelation relation
     *
     * @return BelongsTo
     */
    public function attributeRelation(): BelongsTo
    {
        return $this->belongsTo(AttributeRelation::class, 'attribute_relation_id');
    }

    /**
     * attributeValue relation
     *
     * @return BelongsTo
     */
    public function attributeValue(): BelongsTo
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_id');
    }
}
