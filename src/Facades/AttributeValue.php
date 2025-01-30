<?php

namespace JobMetric\Attribute\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \JobMetric\Attribute\AttributeValue
 *
 * @method static \Spatie\QueryBuilder\QueryBuilder query(array $filter = [], array $with = [])
 * @method static \Illuminate\Http\Resources\Json\AnonymousResourceCollection paginate(array $filter = [], int $page_limit = 15, array $with = [])
 * @method static \Illuminate\Http\Resources\Json\AnonymousResourceCollection all(array $filter = [], array $with = [])
 * @method static array store(int $attribute_id, array $data)
 * @method static array update(int $attribute_id, int $attribute_value_id, array $data)
 * @method static array delete(int $attribute_value_id)
 * @method static string getName(int $attribute_value_id, string $locale = null)
 * @method static array usedIn(int $attribute_value_id)
 * @method static bool hasUsed(int $attribute_value_id)
 * @method static array setTranslation(array $data)
 */
class AttributeValue extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return \JobMetric\Attribute\AttributeValue::class;
    }
}
