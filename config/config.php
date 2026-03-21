<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache Time
    |--------------------------------------------------------------------------
    |
    | Cache time for get data attribute
    |
    | - set zero for remove cache
    | - set null for forever
    |
    | - unit: minutes
    */

    "cache_time" => env("ATTRIBUTE_CACHE_TIME", 0),

    /*
    |--------------------------------------------------------------------------
    | Table Name
    |--------------------------------------------------------------------------
    |
    | Table name in database
    */

    "tables" => [
        'attribute' => 'attributes',
        'attribute_value' => 'attribute_values',
        'attribute_relation' => 'attribute_relations',
        'attribute_relation_value' => 'attribute_relation_values',
    ],

    /*
    |--------------------------------------------------------------------------
    | Attribute Types (Registry defaults)
    |--------------------------------------------------------------------------
    |
    | Types loaded into AttributeTypeRegistry at boot. After boot, change views with
    | AttributeTypeRegistry::setView() / register(), or use a Closure for `view` that
    | reads config when getView() runs. Each key is attributes.type; value holds
    | name/description (translation keys) and view (Blade name or Closure).
    |
    */

    'types' => [
        'radio' => [
            'name' => 'attribute::base.types.radio.name',
            'description' => 'attribute::base.types.radio.description',
            'view' => 'attribute::types.default',
        ],
        'checkbox' => [
            'name' => 'attribute::base.types.checkbox.name',
            'description' => 'attribute::base.types.checkbox.description',
            'view' => 'attribute::types.default',
        ],
        'select' => [
            'name' => 'attribute::base.types.select.name',
            'description' => 'attribute::base.types.select.description',
            'view' => 'attribute::types.default',
        ],
        'color' => [
            'name' => 'attribute::base.types.color.name',
            'description' => 'attribute::base.types.color.description',
            'view' => 'attribute::types.default',
        ],
        'card' => [
            'name' => 'attribute::base.types.card.name',
            'description' => 'attribute::base.types.card.description',
            'view' => 'attribute::types.default',
        ],
        'image' => [
            'name' => 'attribute::base.types.image.name',
            'description' => 'attribute::base.types.image.description',
            'view' => 'attribute::types.default',
        ],
        'input' => [
            'name' => 'attribute::base.types.input.name',
            'description' => 'attribute::base.types.input.description',
            'view' => 'attribute::types.default',
        ],
    ],

];
