<?php

namespace JobMetric\Attribute\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use JobMetric\Translation\Contracts\TranslationContract;
use JobMetric\Translation\HasTranslation;

/**
 * JobMetric\Attribute\Models\AttributeValue
 *
 * @property int $id
 * @property int $attribute_id
 * @property int $ordering
 *
 * @property-read Attribute $attribute
 * @property-read BelongsToMany $attributeRelations
 * @property mixed $translations
 *
 * @method AttributeValue find(int $int)
 */
class AttributeValue extends Model implements TranslationContract
{
    use HasFactory,
        HasTranslation;

    protected $fillable = [
        'attribute_id',
        'ordering'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attribute_id' => 'integer',
        'ordering' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    public function getTable()
    {
        return config('attribute.tables.attribute_value', parent::getTable());
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
     * attribute relation
     *
     * @return BelongsTo
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }

    /**
     * attributeRelations relation
     *
     * @return BelongsToMany
     */
    public function attributeRelations(): BelongsToMany
    {
        return $this->belongsToMany(AttributeRelation::class, config('attribute.tables.attribute_relation_value'), 'attribute_value_id', 'attribute_relation_id');
    }
}
