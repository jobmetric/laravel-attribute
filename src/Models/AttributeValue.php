<?php

namespace JobMetric\Attribute\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use JobMetric\Translation\Contracts\TranslationContract;
use JobMetric\Translation\HasTranslation;

/**
 * JobMetric\Attribute\Models\Attribute
 *
 * @property int $id
 * @property int $attribute_id
 *
 * @method static find(int $int)
 */
class AttributeValue extends Model implements TranslationContract
{
    use HasFactory,
        HasTranslation;

    protected $fillable = [
        'attribute_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'attribute_id' => 'integer',
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
     * Attribute relation
     *
     * @return BelongsTo
     */
    public function attribute(): BelongsTo
    {
        return $this->belongsTo(Attribute::class, 'attribute_id');
    }
}
