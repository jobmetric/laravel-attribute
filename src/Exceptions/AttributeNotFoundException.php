<?php

namespace JobMetric\Attribute\Exceptions;

use Exception;
use Throwable;

/**
 * Thrown when an attribute record cannot be resolved by its primary key.
 */
class AttributeNotFoundException extends Exception
{
    /**
     * @param int $number              Primary key of the missing attribute.
     * @param int $code                HTTP or application error code.
     * @param Throwable|null $previous Previous exception for chaining.
     */
    public function __construct(int $number, int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct(trans('attribute::base.exceptions.attribute_not_found', [
            'number' => $number,
        ]), $code, $previous);
    }
}
