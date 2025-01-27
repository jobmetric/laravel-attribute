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
        'gallery_variation' => 'gallery_variations',
        'gallery_variation_attribute_value' => 'gallery_variation_attribute_values',
    ],

];
