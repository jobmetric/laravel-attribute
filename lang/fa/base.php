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
        "attribute" => [
            "created" => "ویژگی با موفقیت ایجاد شد.",
            "updated" => "ویژگی با موفقیت به روز شد.",
            "deleted" => "ویژگی با موفقیت حذف شد.",
            "deleted_items" => "{1} یک مورد ویژگی با موفقیت حذف شد.|[2,*] :count مورد ویژگی با موفقیت حذف شدند.",
            "used_in" => "این ویژگی در :count مورد استفاده شده است.",
            "set_translation" => "ترجمه ویژگی با موفقیت انجام شد.",
        ],
    ],

    "exceptions" => [
        "attribute_not_found" => "ویژگی با شماره :number یافت نشد.",
        "attribute_used" => "ویژگی :name استفاده شده است.",
    ],

    "list" => [
        "attribute" => [
            "title" => "ویژگی ها",
            "description" => "در این بخش می‌توانید انواع ویژگی‌ها را مدیریت کنید.",
            "filters" => [
                "name" => [
                    "title" => "نام",
                    "placeholder" => "جستجو بر اساس نام",
                ],
            ],
        ]
    ],

    "form" => [
        "attribute" => [
            "create" => [
                "title" => "ایجاد ویژگی",
                "description" => "در این بخش می‌توانید ویژگی جدید ایجاد کنید.",
            ],
            "edit" => [
                "title" => "ویرایش ویژگی شماره :number",
                "description" => "در این بخش می‌توانید ویژگی شماره :number را ویرایش کنید.",
            ],
            "fields" => [
                "name" => [
                    "title" => "نام",
                    "info" => "نام ویژگی نباید تکراری باشد.",
                    "placeholder" => "نام ویژگی را وارد کنید.",
                ],
                "type" => [
                    "title" => "نوع فیلد",
                ],
                "ordering" => [
                    "title" => "ترتیب",
                    "placeholder" => "ترتیب نمایش ویژگی را وارد کنید.",
                ],
                "is_filter" => [
                    "title" => "فیلتر است",
                ],
                "is_special" => [
                    "title" => "خاص است",
                ],
            ]
        ]
    ],

    "enums" => [
        "attribute_type" => [
            "radio" => "رادیو",
            "checkbox" => "چک‌باکس",
            "select" => "انتخابی",
            "color" => "رنگ",
            "card" => "کارت",
            "image" => "تصویر",
            "input" => "ورودی",
        ]
    ]

];
