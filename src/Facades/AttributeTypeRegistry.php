<?php

namespace JobMetric\Attribute\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @mixin \JobMetric\Attribute\Support\AttributeTypeRegistry
 *
 * @method static \JobMetric\Attribute\Support\AttributeTypeRegistry register(string $type, array $options = [])
 * @method static \JobMetric\Attribute\Support\AttributeTypeRegistry setView(string $type, string|\Closure|null $view)
 * @method static \JobMetric\Attribute\Support\AttributeTypeRegistry unregister(string $type)
 * @method static bool has(string $type)
 * @method static array<string, mixed>|null get(string $type)
 * @method static array<string, mixed> all()
 * @method static array<int, string> values()
 * @method static mixed getOption(string $type, string $key, mixed $default = null)
 * @method static string getName(string $type)
 * @method static string getDescription(string $type)
 * @method static string|null getView(string $type)
 * @method static \JobMetric\Attribute\Support\AttributeTypeRegistry clear()
 */
class AttributeTypeRegistry extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'AttributeTypeRegistry';
    }
}
