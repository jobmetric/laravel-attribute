<?php

namespace JobMetric\Attribute;

use Illuminate\Support\Facades\Route;
use JobMetric\Attribute\Facades\AttributeTypeRegistry as AttributeTypeRegistryFacade;
use JobMetric\Attribute\Models\Attribute as AttributeModel;
use JobMetric\Attribute\Models\AttributeValue as AttributeValueModel;
use JobMetric\Attribute\Services\Attribute as AttributeService;
use JobMetric\Attribute\Services\AttributeValue as AttributeValueService;
use JobMetric\Attribute\Support\AttributeTypeRegistry;
use JobMetric\PackageCore\Enums\RegisterClassTypeEnum;
use JobMetric\PackageCore\Exceptions\MigrationFolderNotFoundException;
use JobMetric\PackageCore\Exceptions\RegisterClassTypeNotFoundException;
use JobMetric\PackageCore\Exceptions\ViewFolderNotFoundException;
use JobMetric\PackageCore\PackageCore;
use JobMetric\PackageCore\PackageCoreServiceProvider;

class AttributeServiceProvider extends PackageCoreServiceProvider
{
    /**
     * @param PackageCore $package
     *
     * @return void
     * @throws MigrationFolderNotFoundException
     * @throws ViewFolderNotFoundException
     * @throws RegisterClassTypeNotFoundException
     */
    public function configuration(PackageCore $package): void
    {
        $package->name('laravel-attribute')
            ->hasConfig()
            ->hasMigration()
            ->hasView()
            ->hasTranslation()
            ->registerClass('AttributeTypeRegistry', AttributeTypeRegistry::class, RegisterClassTypeEnum::SINGLETON())
            ->registerClass('attribute', AttributeService::class, RegisterClassTypeEnum::SINGLETON())
            ->registerClass('attribute_value', AttributeValueService::class, RegisterClassTypeEnum::SINGLETON());
    }

    /**
     * After register package
     *
     * @return void
     */
    public function afterRegisterPackage(): void
    {
        foreach (config('attribute.types', []) as $type => $options) {
            AttributeTypeRegistryFacade::register($type, is_array($options) ? $options : []);
        }

        // Register model binding
        Route::model('jm_attribute', AttributeModel::class);
        Route::model('jm_attribute_value', AttributeValueModel::class);
    }
}
