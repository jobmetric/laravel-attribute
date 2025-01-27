<?php

namespace JobMetric\Attribute\Enums;

use JobMetric\PackageCore\Enums\EnumToArray;

/**
 * @method static RADIO()
 * @method static CHECKBOX()
 * @method static SELECT()
 * @method static COLOR()
 * @method static CARD()
 * @method static IMAGE()
 * @method static INPUT()
 */
enum AttributeTypeEnum: string
{
    use EnumToArray;

    case RADIO = "radio";
    case CHECKBOX = "checkbox";
    case SELECT = "select";
    case COLOR = "color";
    case CARD = "card";
    case IMAGE = "image";
    case INPUT = "input";
}
