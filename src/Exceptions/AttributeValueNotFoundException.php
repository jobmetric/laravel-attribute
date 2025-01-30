<?php

namespace JobMetric\Attribute\Exceptions;

use Exception;
use Throwable;

class AttributeValueNotFoundException extends Exception
{
    public function __construct(int $number, int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct(trans('attribute::base.exceptions.attribute_value_not_found', [
            'number' => $number,
        ]), $code, $previous);
    }
}
