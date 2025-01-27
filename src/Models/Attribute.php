<?php

namespace JobMetric\Attribute\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use JobMetric\Translation\Contracts\TranslationContract;
use JobMetric\Translation\HasTranslation;

/**
 * JobMetric\Attribute\Models\Attribute
 *
 * @property int $id
 * @property string $type
 * @property bool $is_gallery
 * @property bool $is_special
 * @property bool $is_filter
 * @property int $ordering
 *
 * @method static find(int $int)
 */
class Attribute extends Model implements TranslationContract
{
    use HasFactory,
        HasTranslation;

    protected $fillable = [
        'type',
        'is_gallery',
        'is_special',
        'is_filter',
        'ordering',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'type' => 'string',
        'is_gallery' => 'boolean',
        'is_special' => 'boolean',
        'is_filter' => 'boolean',
        'ordering' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function getTable()
    {
        return config('attribute.tables.attribute', parent::getTable());
    }

    /**
     * translation allow fields.
     *
     * @return array
     */
    public function translationAllowFields(): array
    {
        return [
            'name'
        ];
    }

    /**
     * Attribute values relation
     *
     * @return HasMany
     */
    public function attributeValues(): HasMany
    {
        return $this->hasMany(AttributeValue::class, 'attribute_id');
    }
}
