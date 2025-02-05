<?php

namespace JobMetric\Attribute\Http\Requests;

use Exception;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use InvalidArgumentException;
use JobMetric\Attribute\Models\AttributeValue;
use JobMetric\Translation\Http\Requests\TranslationArrayRequest;

class SetTranslationAttributeValueRequest extends FormRequest
{
    use TranslationArrayRequest;

    public string|null $type = null;

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
     * @throws Exception
     */
    public function rules(): array
    {
        $form_data = request()->all();

        $locale = $form_data['locale'] ?? null;
        $id = $form_data['translatable_id'] ?? null;

        if (is_null($locale)) {
            throw new InvalidArgumentException('Locale is required', 400);
        }

        if (is_null($id)) {
            throw new InvalidArgumentException('Translatable ID is required', 400);
        }

        AttributeValue::query()->findOrFail($id);

        $rules = [
            'locale' => 'required|string',
            'translatable_id' => 'required|integer|exists:' . config('attribute.tables.attribute_value') . ',id',
        ];

        $this->renderTranslationFiled($rules, $form_data, AttributeValue::class, object_id: $id);

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        $form_data = request()->all();

        $params = [];
        $this->renderTranslationAttribute($params, $form_data, AttributeValue::class, 'attribute::base.form.attribute_value.fields.{field}.title');

        return $params;
    }
}
