<?php

namespace JobMetric\Attribute\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use JobMetric\Attribute\Factories\AttributeValueFactory;
use JobMetric\Translation\HasTranslation;

/**
 * Class AttributeValue
 *
 * A single selectable/displayable value belonging to an {@see Attribute}.
 * Name is translatable. Linked to {@see AttributeRelation} rows through the
 * {@see AttributeRelationValue} pivot.
 *
 * @package JobMetric\Attribute
 *
 * @property int $id
 * @property int $attribute_id
 * @property int $ordering
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Attribute $attribute
 * @property-read BelongsToMany $attributeRelations
 * @property-read mixed $translations
 *
 * @method static AttributeValueFactory factory($count = null, $state = [])
 * @method static Builder|AttributeValue query()
 * @method static Builder|AttributeValue whereAttributeId(int $attribute_id)
 * @method static Builder|AttributeValue whereOrdering(int|string $ordering)
 * @method static AttributeValue|null find(mixed $id, array $columns = ['*'])
 */
class AttributeValue extends Model
{
    use HasFactory;
    use HasTranslation;

    /**
     * The relationships that should be touched when this model is updated, ensuring that the parent attribute's
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
        'attribute_id',
        'ordering',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attribute_id' => 'integer',
        'ordering'     => 'integer',
    ];

    /**
     * The attributes that are translatable via the translation package.
     *
     * @var array<int, string>
     */
    protected array $translatables = [
        'name',
    ];

    /**
     * Get the table associated with the model, allowing for configuration override.
     *
     * @return string
     */
    public function getTable(): string
    {
        return config('attribute.tables.attribute_value', parent::getTable());
    }

    /**
     * Get a new factory instance for the model, used in testing and seeding.
     *
     * @return AttributeValueFactory
     */
    protected static function newFactory(): AttributeValueFactory
    {
        return AttributeValueFactory::new();
    }

    /**
     * Get the attribute that this value belongs to.
     *
     * @return BelongsTo
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    /**
     * Relations that include this value in their pivot set.
     *
     * @return BelongsToMany
     */
    public function attributeRelations(): BelongsToMany
    {
        return $this->belongsToMany(AttributeRelation::class, config('attribute.tables.attribute_relation_value'), 'attribute_value_id', 'attribute_relation_id');
    }
}
