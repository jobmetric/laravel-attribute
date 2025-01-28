<?php

namespace JobMetric\Attribute\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use JobMetric\Attribute\Events\AttributableResourceEvent;

/**
 * JobMetric\Attribute\Models\AttributeRelation
 *
 * @property int $id
 * @property string $attributable_type
 * @property int $attributable_id
 * @property int $attribute_id
 * @property bool $is_coding
 * @property bool $is_gallery
 * @property bool $is_special
 * @property bool $is_filter
 * @property Carbon $created_at
 *
 * @property-read Model $attributable
 * @property-read Attribute $attribute
 * @property-read BelongsToMany $attributeValues
 * @property-read mixed $attributable_resource
 *
 * @method AttributeRelation find(int $int)
 */
class AttributeRelation extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'attributable_type',
        'attributable_id',
        'attribute_id',
        'is_coding',
        'is_gallery',
        'is_special',
        'is_filter',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attributable_type' => 'string',
        'attributable_id' => 'integer',
        'attribute_id' => 'integer',
        'is_coding' => 'boolean',
        'is_gallery' => 'boolean',
        'is_special' => 'boolean',
        'is_filter' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function getTable()
    {
        return config('attribute.tables.attribute_relation', parent::getTable());
    }

    /**
     * attributable relation
     *
     * @return MorphTo
     */
    public function attributable(): MorphTo
    {
        return $this->morphTo('attributable');
    }

    /**
     * attribute relation
     *
     * @return BelongsTo
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    /**
     * attributeValues relation
     *
     * @return BelongsToMany
     */
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, config('attribute.tables.attribute_relation_value'), 'attribute_relation_id', 'attribute_value_id');
    }

    /**
     * Get the attributable resource attribute.
     */
    public function getAttributableResourceAttribute()
    {
        $event = new AttributableResourceEvent($this->attributable);
        event($event);

        return $event->resource;
    }
}
