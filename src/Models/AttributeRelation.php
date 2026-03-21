<?php

namespace JobMetric\Attribute\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use JobMetric\Attribute\Factories\AttributeRelationFactory;
use JobMetric\PackageCore\Traits\HasMorphResourceAttributes;

/**
 * Class AttributeRelation
 *
 * Binds an {@see Attribute} to a polymorphic parent (attributable). Selected
 * values are attached via {@see attributeValues()} pivot. Only `created_at` is
 * stored on this model.
 *
 * @package JobMetric\Attribute
 *
 * @property int $id
 * @property string $attributable_type
 * @property int $attributable_id
 * @property int $attribute_id
 * @property bool $is_variant
 * @property bool $is_special
 * @property Carbon|null $created_at
 *
 * @property-read Model|null $attributable
 * @property-read Attribute $attribute
 * @property-read BelongsToMany $attributeValues
 * @property-read mixed $attributable_resource
 *
 * @method static AttributeRelationFactory factory($count = null, $state = [])
 * @method static Builder|AttributeRelation query()
 * @method static Builder|AttributeRelation whereAttributeId(int $attribute_id)
 * @method static Builder|AttributeRelation whereAttributableType(string $attributable_type)
 * @method static Builder|AttributeRelation whereAttributableId(int|string $attributable_id)
 * @method static Builder|AttributeRelation whereIsVariant(bool $value = true)
 * @method static Builder|AttributeRelation whereIsSpecial(bool $value = true)
 * @method static Builder|AttributeRelation forAttributable(Model $model)
 * @method static AttributeRelation|null find(mixed $id, array $columns = ['*'])
 */
class AttributeRelation extends Model
{
    use HasFactory, HasMorphResourceAttributes;

    const UPDATED_AT = null;

    /**
     * MorphTo relations that expose a virtual "{name}_resource" attribute (see package-core trait).
     *
     * @var list<string>
     */
    protected array $resourceMorphRelations = [
        'attributable',
    ];

    /**
     * The relationships that should be touched when this model is updated, ensuring that the parent attributable's
     * timestamps are updated when related attributes change.
     *
     * @var array<int, string>
     */
    protected $touches = [
        'attribute',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'attributable_type',
        'attributable_id',
        'attribute_id',
        'is_variant',
        'is_special',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attributable_type' => 'string',
        'attributable_id'   => 'integer',
        'attribute_id'      => 'integer',
        'is_variant'        => 'boolean',
        'is_special'        => 'boolean',
    ];

    /**
     * Override the default table name to use the one specified in configuration, falling back to the parent method if
     * not set.
     *
     * @return string
     */
    public function getTable(): string
    {
        return config('attribute.tables.attribute_relation', parent::getTable());
    }

    /**
     * Get a new factory instance for the model, used in testing and seeding.
     *
     * @return AttributeRelationFactory
     */
    protected static function newFactory(): AttributeRelationFactory
    {
        return AttributeRelationFactory::new();
    }

    /**
     * The parent model that this attribute relation is associated with (polymorphic).
     *
     * @return MorphTo
     */
    public function attributable(): MorphTo
    {
        return $this->morphTo('attributable');
    }

    /**
     * The attribute associated with this relation.
     *
     * @return BelongsTo
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    /**
     * Attribute values selected for this relation (pivot: attribute_relation_values).
     *
     * @return BelongsToMany
     */
    public function attributeValues(): BelongsToMany
    {
        return $this->belongsToMany(AttributeValue::class, config('attribute.tables.attribute_relation_value'), 'attribute_relation_id', 'attribute_value_id');
    }

    /**
     * Scope a query to filter attribute relations for a given attributable model instance.
     *
     * @param Builder $query
     * @param Model $model
     *
     * @return Builder
     */
    public function scopeForAttributable(Builder $query, Model $model): Builder
    {
        return $query->where([
            'attributable_type' => $model->getMorphClass(),
            'attributable_id'   => $model->getKey(),
        ]);
    }
}
