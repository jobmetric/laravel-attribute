<?php

namespace JobMetric\Attribute\Exceptions;

use Exception;
use Throwable;

class AttributeValueUsedException extends Exception
{
    public function __construct(string $name, int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct(trans('attribute::base.exceptions.attribute_value_used', [
            'name' => $name,
        ]), $code, $previous);
    }
}
