<?php

namespace JobMetric\Attribute;

use JobMetric\PackageCore\Exceptions\MigrationFolderNotFoundException;
use JobMetric\PackageCore\PackageCore;
use JobMetric\PackageCore\PackageCoreServiceProvider;

class AttributeServiceProvider extends PackageCoreServiceProvider
{
    /**
     * @param PackageCore $package
     *
     * @return void
     * @throws MigrationFolderNotFoundException
     */
    public function configuration(PackageCore $package): void
    {
        $package->name('laravel-attribute')
            ->hasConfig()
            ->hasMigration()
            ->hasRoute()
            ->hasTranslation();
    }
}
