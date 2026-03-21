<?php

namespace JobMetric\Attribute\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \JobMetric\Attribute\Services\AttributeValue
 *
 * @method static \Spatie\QueryBuilder\QueryBuilder query(array $filters = [], array $with = [], string|null $mode = null)
 * @method static \JobMetric\PackageCore\Output\Response paginate(int $pageLimit = 15, array $filters = [], array $with = [], string|null $mode = null)
 * @method static \JobMetric\PackageCore\Output\Response all(array $filters = [], array $with = [], string|null $mode = null)
 * @method static \JobMetric\PackageCore\Output\Response show(int $id, array $with = [], string|null $mode = null)
 * @method static \JobMetric\PackageCore\Output\Response storeForAttribute(int $attributeId, array $data, array $with = [])
 * @method static \JobMetric\PackageCore\Output\Response store(array $data, array $with = [])
 * @method static \JobMetric\PackageCore\Output\Response updateForAttribute(int $attributeId, int $attributeValueId, array $data, array $with = [])
 * @method static \JobMetric\PackageCore\Output\Response update(int $id, array $data, array $with = [])
 * @method static \JobMetric\PackageCore\Output\Response destroy(int $id, array $with = [])
 * @method static \JobMetric\PackageCore\Output\Response delete(int $id, array $with = [])
 * @method static string getName(int $attribute_value_id, string|null $locale = null)
 * @method static \JobMetric\PackageCore\Output\Response usedIn(int $attribute_value_id)
 * @method static bool hasUsed(int $attribute_value_id)
 * @method static \JobMetric\PackageCore\Output\Response setTranslation(array $data)
 */
class AttributeValue extends Facade
{
    /**
     * Get the registered name of the component in the service container.
     *
     * This accessor must match the binding defined in the package service provider.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'attribute_value';
    }
}
