<?php

namespace JobMetric\Attribute\Exceptions;

use Exception;
use Throwable;

/**
 * Thrown when an attribute value cannot be deleted because it is still referenced.
 */
class AttributeValueUsedException extends Exception
{
    /**
     * @param string $name             Resolved display name of the attribute value (for the message).
     * @param int $code                HTTP or application error code.
     * @param Throwable|null $previous Previous exception for chaining.
     */
    public function __construct(string $name, int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct(trans('attribute::base.exceptions.attribute_value_used', [
            'name' => $name,
        ]), $code, $previous);
    }
}
