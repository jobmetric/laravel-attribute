<?php

namespace JobMetric\Attribute\Exceptions;

use Exception;
use Throwable;

class AttributeNotFoundException extends Exception
{
    public function __construct(int $number, int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct(trans('attribute::base.exceptions.attribute_not_found', [
            'number' => $number,
        ]), $code, $previous);
    }
}
