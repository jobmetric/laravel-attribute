<?php

namespace JobMetric\Attribute\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use JobMetric\Attribute\Factories\AttributeFactory;
use JobMetric\Translation\HasTranslation;

/**
 * Class Attribute
 *
 * Global attribute definition (type, flags, ordering). Translatable name is stored via
 * the translation package. Values live in {@see AttributeValue}; entity links in
 * {@see AttributeRelation}.
 *
 * @package JobMetric\Attribute
 *
 * @property int $id
 * @property string $type AttributeTypeRegistry key (e.g. radio, select).
 * @property bool $is_special
 * @property bool $is_filter
 * @property int $ordering
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @property-read Collection<int, AttributeValue> $attributeValues
 * @property-read int|null $attribute_values_count
 * @property-read Collection<int, AttributeRelation> $attributeRelations
 * @property-read int|null $attribute_relations_count
 * @property-read mixed $translations
 *
 * @method static AttributeFactory factory($count = null, $state = [])
 * @method static Builder|Attribute query()
 * @method static Builder|Attribute whereType(string $type)
 * @method static Builder|Attribute whereIsSpecial(bool $value = true)
 * @method static Builder|Attribute whereIsFilter(bool $value = true)
 * @method static Builder|Attribute whereOrdering(int|string $ordering)
 * @method static Attribute|null find(mixed $id, array $columns = ['*'])
 */
class Attribute extends Model
{
    use HasFactory;
    use HasTranslation;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'is_special',
        'is_filter',
        'ordering',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type'       => 'string',
        'is_special' => 'boolean',
        'is_filter'  => 'boolean',
        'ordering'   => 'integer',
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
     * Override the default table name to allow configuration via package config. Falls back to parent table name if not set.
     *
     * @return string
     */
    public function getTable(): string
    {
        return config('attribute.tables.attribute', parent::getTable());
    }

    /**
     * Create a new factory instance for the model, using the package's AttributeFactory.
     *
     * @return AttributeFactory
     */
    protected static function newFactory(): AttributeFactory
    {
        return AttributeFactory::new();
    }

    /**
     * Values defined for this attribute (e.g. options for select/radio).
     *
     * @return HasMany
     */
    public function attributeValues(): HasMany
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id');
    }

    /**
     * Links attaching this attribute to attributable entities (polymorphic).
     */
    public function attributeRelations(): HasMany
    {
        return $this->hasMany(AttributeRelation::class, 'attribute_id');
    }
}
