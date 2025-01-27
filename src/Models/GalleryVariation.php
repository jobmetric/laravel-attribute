<?php

namespace JobMetric\Attribute\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * JobMetric\Attribute\Models\GalleryVariation
 *
 * @property int $id
 * @property string $galleryable_type
 * @property int $galleryable_id
 * @property Carbon|null $created_at
 *
 * @property-read Model $galleryable
 * @property-read Collection|GalleryVariationAttributeValue[] $galleryVariationAttributeValues
 * @property-read int|null $gallery_variation_attribute_values_count
 *
 * @method static find(int $int)
 */
class GalleryVariation extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'galleryable_type',
        'galleryable_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'galleryable_type' => 'string',
        'galleryable_id' => 'integer',
        'created_at' => 'datetime',
    ];

    public function getTable()
    {
        return config('attribute.tables.attribute_value', parent::getTable());
    }

    /**
     * galleryable relation
     *
     * @return MorphTo
     */
    public function galleryable(): MorphTo
    {
        return $this->morphTo('galleryable');
    }

    /**
     * galleryVariationAttributeValues relation
     *
     * @return HasMany
     */
    public function galleryVariationAttributeValues(): HasMany
    {
        return $this->hasMany(GalleryVariationAttributeValue::class, 'gallery_variation_id');
    }
}
