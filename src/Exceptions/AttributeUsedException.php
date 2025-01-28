<?php

namespace JobMetric\Attribute\Exceptions;

use Exception;
use Throwable;

class AttributeUsedException extends Exception
{
    public function __construct(string $name, int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct(trans('attribute::base.exceptions.attribute_used', [
            'name' => $name,
        ]), $code, $previous);
    }
}
