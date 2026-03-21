<?php

namespace JobMetric\Attribute\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JobMetric\Attribute\Facades\AttributeTypeRegistry;
use JobMetric\Attribute\Models\Attribute;
use JobMetric\Translation\Exceptions\ModelHasTranslationNotFoundException;
use JobMetric\Translation\Http\Requests\MultiTranslationArrayRequest;

class UpdateAttributeRequest extends FormRequest
{
    use MultiTranslationArrayRequest;

    public string|null $type = null;
    public int|null $attribute_id = null;

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
     * @throws ModelHasTranslationNotFoundException
     */
    public function rules(): array
    {
        if (is_null($this->attribute_id)) {
            $attribute_id = $this->route()->parameter('attribute')->id;
        }
        else {
            $attribute_id = $this->attribute_id;
        }

        $rules = [
            'type'       => ['required', 'string', Rule::in(AttributeTypeRegistry::values())],
            'is_special' => 'boolean|sometimes',
            'is_filter'  => 'boolean|sometimes',
            'ordering'   => 'numeric|sometimes',
        ];

        $this->renderMultiTranslationFiled($rules, Attribute::class, object_id: $attribute_id);

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     * @throws ModelHasTranslationNotFoundException
     */
    public function attributes(): array
    {
        $params = [
            'type'       => trans('attribute::base.form.attribute.fields.type.title'),
            'is_special' => trans('attribute::base.form.attribute.fields.is_special.title'),
            'is_filter'  => trans('attribute::base.form.attribute.fields.is_filter.title'),
            'ordering'   => trans('attribute::base.form.attribute.fields.ordering.title'),
        ];

        $this->renderMultiTranslationAttribute($params, Attribute::class, 'attribute::base.form.attribute.fields.{field}.title');

        return $params;
    }

    /**
     * Set attribute id for validation
     *
     * @param int $attribute_id
     *
     * @return static
     */
    public function setAttributeId(int $attribute_id): static
    {
        $this->attribute_id = $attribute_id;

        return $this;
    }

    /**
     * @param array<string, mixed> $input
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     * @throws ModelHasTranslationNotFoundException
     */
    public static function rulesFor(array $input, array $context = []): array
    {
        $request = new self;
        if (isset($context['id'])) {
            $request->setAttributeId((int) $context['id']);
        }

        return $request->rules();
    }
}
