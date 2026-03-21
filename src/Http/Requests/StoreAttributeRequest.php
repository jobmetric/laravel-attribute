<?php

namespace JobMetric\Attribute\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use JobMetric\Attribute\Facades\AttributeTypeRegistry;
use JobMetric\Attribute\Models\Attribute;
use JobMetric\Translation\Http\Requests\TranslationArrayRequest;
use Throwable;

class StoreAttributeRequest extends FormRequest
{
    use TranslationArrayRequest;

    public array $data = [];

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
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
        if (! empty(request()->all())) {
            $this->data = request()->all();
        }

        $rules = [
            'type'       => ['required', 'string', Rule::in(AttributeTypeRegistry::values())],
            'is_special' => 'boolean|sometimes',
            'is_filter'  => 'boolean|sometimes',
            'ordering'   => 'numeric|sometimes',
        ];

        $this->renderTranslationFiled($rules, $this->data, Attribute::class);

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
            'type'       => trans('attribute::base.form.attribute.fields.type.title'),
            'is_special' => trans('attribute::base.form.attribute.fields.is_special.title'),
            'is_filter'  => trans('attribute::base.form.attribute.fields.is_filter.title'),
            'ordering'   => trans('attribute::base.form.attribute.fields.ordering.title'),
        ];

        $this->renderTranslationAttribute($params, $this->data, Attribute::class, 'attribute::base.form.attribute.fields.{field}.title');

        return $params;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        if (! empty(request()->all())) {
            $this->data = request()->all();
        }

        $this->merge([
            'is_special' => $this->is_special ?? false,
            'is_filter'  => $this->is_filter ?? false,
            'ordering'   => $this->ordering ?? 0,
        ]);
    }

    /**
     * Set data for validation
     *
     * @param array $data
     *
     * @return static
     */
    public function setData(array $data): static
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Normalize the given data before validation.
     *
     * @param array<string, mixed> $input
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     */
    public static function normalize(array $input, array $context = []): array
    {
        return array_merge([
            'is_special' => false,
            'is_filter'  => false,
            'ordering'   => 0,
        ], $input);
    }

    /**
     * Validate the given data against the rules of this request.
     *
     * @param array<string, mixed> $input
     * @param array<string, mixed> $context
     *
     * @return array<string, mixed>
     * @throws Throwable
     */
    public static function rulesFor(array $input, array $context = []): array
    {
        $request = new self;
        $request->data = $input;

        return $request->rules();
    }
}
