<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Base Attribute Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during Attribute for
    | various messages that we need to display to the user.
    |
    */

    "validation" => [
    ],

    "messages" => [
        "attribute"=> [
            "created" => "Attribute created successfully.",
            "updated" => "Attribute updated successfully.",
            "deleted" => "Attribute deleted successfully.",
            "deleted_items" => "{1} One attribute deleted successfully.|[2,*] :count attributes deleted successfully.",
            "used_in"=> "This attribute is used in :count items.",
            "set_translation" => "Attribute translation set successfully.",
        ],
    ],

    "exceptions" => [
        "attribute_not_found" => "Attribute with number :number not found.",
        "attribute_used" => "Attribute :name is used.",
    ],

    "list" => [
        "attribute" => [
            "title" => "Attributes",
            "description" => "In this section, you can manage various attributes.",
            "filters" => [
                "name" => [
                    "title" => "Name",
                    "placeholder" => "Search by name",
                ],
            ],
        ]
    ],

    "form" => [
        "attribute" => [
            "create" => [
                "title" => "Create Attribute",
                "description" => "In this section, you can create a new attribute.",
            ],
            "edit" => [
                "title" => "Edit Attribute number :number",
                "description" => "In this section, you can edit attribute number :number.",
            ],
            "fields" => [
                "name" => [
                    "title" => "Name",
                    "info" => "Attribute name should not be repeated.",
                    "placeholder" => "Enter the attribute name.",
                ],
                "type" => [
                    "title" => "Type",
                ],
                "ordering" => [
                    "title" => "Ordering",
                    "placeholder" => "Enter the attribute ordering.",
                ],
                "is_filter" => [
                    "title" => "Is Filter",
                ],
                "is_special" => [
                    "title" => "Is Special",
                ],
            ]
        ]
    ],

    "enums" => [
        "attribute_type" => [
            "radio" => "Radio",
            "checkbox" => "Checkbox",
            "select" => "Select",
            "color" => "Color",
            "card" => "Card",
            "image" => "Image",
            "input" => "Input",
        ]
    ]

];
