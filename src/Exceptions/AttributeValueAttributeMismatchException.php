<?php

namespace JobMetric\Attribute\Exceptions;

use Exception;
use Throwable;

/**
 * Thrown when an attribute value exists but its parent attribute id does not match the requested scope.
 */
class AttributeValueAttributeMismatchException extends Exception
{
    /**
     * @param int $attributeValueId    Primary key of the attribute value.
     * @param int $attributeId         Parent attribute id from the request or route scope.
     * @param int $code                HTTP or application error code.
     * @param Throwable|null $previous Previous exception for chaining.
     */
    public function __construct(int $attributeValueId, int $attributeId, int $code = 404, ?Throwable $previous = null)
    {
        parent::__construct(trans('attribute::base.exceptions.attribute_value_attribute_mismatch', [
            'value_number'     => $attributeValueId,
            'attribute_number' => $attributeId,
        ]), $code, $previous);
    }
}
