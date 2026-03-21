<?php

namespace JobMetric\Attribute\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;
use JobMetric\Attribute\Factories\AttributeRelationValueFactory;

/**
 * Class AttributeRelationValue
 *
 * Pivot between {@see AttributeRelation} and {@see AttributeValue}. No surrogate key:
 * uniqueness is enforced on (attribute_relation_id, attribute_value_id) in the database.
 *
 * @package JobMetric\Attribute
 *
 * @property int $attribute_relation_id
 * @property int $attribute_value_id
 *
 * @property-read AttributeRelation $attributeRelation
 * @property-read AttributeValue $attributeValue
 *
 * @method static AttributeRelationValueFactory factory($count = null, $state = [])
 * @method static Builder|AttributeRelationValue query()
 * @method static Builder|AttributeRelationValue whereAttributeRelationId(int $attribute_relation_id)
 * @method static Builder|AttributeRelationValue whereAttributeValueId(int $attribute_value_id)
 */
class AttributeRelationValue extends Pivot
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attribute_relation_id',
        'attribute_value_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attribute_relation_id' => 'integer',
        'attribute_value_id'    => 'integer',
    ];

    /**
     * Get the table associated with the model, allowing for configuration override.
     *
     * @return string
     */
    public function getTable(): string
    {
        return config('attribute.tables.attribute_relation_value', parent::getTable());
    }

    /**
     * Get a new factory instance for the model, used in testing and seeding.
     *
     * @return AttributeRelationValueFactory
     */
    protected static function newFactory(): AttributeRelationValueFactory
    {
        return AttributeRelationValueFactory::new();
    }

    /**
     * Get the attribute relation that this relation value belongs to.
     *
     * @return BelongsTo
     */
    public function attributeRelation(): BelongsTo
    {
        return $this->belongsTo(AttributeRelation::class, 'attribute_relation_id');
    }

    /**
     * Get the attribute value that this relation value belongs to.
     *
     * @return BelongsTo
     */
    public function attributeValue(): BelongsTo
    {
        return $this->belongsTo(AttributeValue::class, 'attribute_value_id');
    }
}
