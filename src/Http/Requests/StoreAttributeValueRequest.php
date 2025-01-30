<?php

namespace JobMetric\Attribute\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use JobMetric\Attribute\Models\AttributeValue;
use JobMetric\Translation\Http\Requests\TranslationArrayRequest;
use Throwable;

class StoreAttributeValueRequest extends FormRequest
{
    use TranslationArrayRequest;

    public int|null $attribute_id = null;
    public array $data = [];

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
     * @throws Throwable
     */
    public function rules(): array
    {
        if (is_null($this->attribute_id)) {
            $attribute_id = $this->route()->parameter('attribute')->id;
        } else {
            $attribute_id = $this->attribute_id;
        }

        if (!empty(request()->all())) {
            $this->data = request()->all();
        }

        $rules = [
            'ordering' => 'numeric|sometimes',
        ];

        $this->renderTranslationFiled($rules, $this->data, AttributeValue::class);

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

        $this->renderTranslationAttribute($params, $this->data, AttributeValue::class, 'attribute::base.form.attribute_value.fields.{field}.title');

        return $params;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if (!empty(request()->all())) {
            $this->data = request()->all();
        }

        $this->merge([
            'ordering' => $this->ordering ?? 0,
        ]);
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
     * Set data for validation
     *
     * @param array $data
     * @return static
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }
}
