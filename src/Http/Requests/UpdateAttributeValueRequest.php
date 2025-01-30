<?php

namespace JobMetric\Attribute\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use JobMetric\Attribute\Models\AttributeValue;
use JobMetric\Translation\Http\Requests\MultiTranslationArrayRequest;

class UpdateAttributeValueRequest extends FormRequest
{
    use MultiTranslationArrayRequest;

    public string|null $type = null;
    public int|null $attribute_id = null;
    public int|null $attribute_value_id = null;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        if (is_null($this->attribute_id)) {
            $attribute_id = $this->route()->parameter('attribute')->id;
        } else {
            $attribute_id = $this->attribute_id;
        }

        if (is_null($this->attribute_value_id)) {
            $attribute_value_id = $this->route()->parameter('value')->id;
        } else {
            $attribute_value_id = $this->attribute_value_id;
        }

        $rules = [
            'ordering' => 'numeric|sometimes',
        ];

        $this->renderMultiTranslationFiled($rules, AttributeValue::class, object_id: $attribute_value_id);

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        $params = [
            'ordering' => trans('attribute::base.form.attribute_value.fields.ordering.title'),
        ];

        $this->renderMultiTranslationAttribute($params, AttributeValue::class, 'attribute::base.form.attribute_value.fields.{field}.title');

        return $params;
    }

    /**
     * Set attribute id for validation
     *
     * @param int $attribute_id
     * @return static
     */
    public function setAttributeId(int $attribute_id): static
    {
        $this->attribute_id = $attribute_id;

        return $this;
    }

    /**
     * Set attribute value id for validation
     *
     * @param int $attribute_value_id
     * @return static
     */
    public function setAttributeValueId(int $attribute_value_id): static
    {
        $this->attribute_value_id = $attribute_value_id;

        return $this;
    }
}
