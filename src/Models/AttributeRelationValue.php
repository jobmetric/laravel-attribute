<?php

namespace JobMetric\Attribute\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * JobMetric\Attribute\Models\AttributeRelationValue
 *
 * @property int $attribute_relation_id
 * @property int $attribute_value_id
 *
 * @property-read AttributeRelation $attributeRelation
 * @property-read AttributeValue $attributeValue
 *
 * @method AttributeRelationValue find(int $int)
 */
class AttributeRelationValue extends Pivot
{
    use HasFactory;

    protected $fillable = [
        'attribute_relation_id',
        'attribute_value_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attribute_relation_id' => 'int',
        'attribute_value_id' => 'int',
    ];

    public function getTable()
    {
        return config('attribute.tables.attribute_relation_value', parent::getTable());
    }

    /**
     * attributeRelation relation
     *
     * @return BelongsTo
     */
    public function attributeRelation(): BelongsTo
    {
        return $this->belongsTo(AttributeRelation::class);
    }

    /**
     * attributeValue relation
     *
     * @return BelongsTo
     */
    public function attributeValue(): BelongsTo
    {
        return $this->belongsTo(AttributeValue::class);
    }
}
