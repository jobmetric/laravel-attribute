<?php

namespace JobMetric\Attribute;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use JobMetric\Attribute\Events\AttributeValueDeleteEvent;
use JobMetric\Attribute\Events\AttributeValueStoreEvent;
use JobMetric\Attribute\Events\AttributeValueUpdateEvent;
use JobMetric\Attribute\Exceptions\AttributeNotFoundException;
use JobMetric\Attribute\Exceptions\AttributeValueNotFoundException;
use JobMetric\Attribute\Exceptions\AttributeValueUsedException;
use JobMetric\Attribute\Http\Requests\StoreAttributeValueRequest;
use JobMetric\Attribute\Http\Requests\UpdateAttributeValueRequest;
use JobMetric\Attribute\Http\Resources\AttributeRelationValueResource;
use JobMetric\Attribute\Http\Resources\AttributeValueResource;
use JobMetric\Attribute\Models\Attribute as AttributeModel;
use JobMetric\Attribute\Models\AttributeRelationValue;
use JobMetric\Attribute\Models\AttributeValue as AttributeValueModel;
use JobMetric\Metadata\HasFilterMeta;
use JobMetric\Translation\Models\Translation;
use Spatie\QueryBuilder\QueryBuilder;
use Throwable;

class AttributeValue
{
    use HasFilterMeta;

    /**
     * Get the specified attribute value.
     *
     * @param array $filter
     * @param array $with
     *
     * @return QueryBuilder
     * @throws Throwable
     */
    public function query(array $filter = [], array $with = []): QueryBuilder
    {
        $fields = [
            'id',
            'name',
            'attribute_id',
            'ordering',
            'created_at',
            'updated_at'
        ];

        $attribute_value_table = config('attribute.tables.attribute_value');
        $translation_table = config('translation.tables.translation');

        $locale = app()->getLocale();

        $query = AttributeValueModel::query()->select([$attribute_value_table . '.*']);

        // Get the name of the attribute
        $attribute_value_name = Translation::query()
            ->select('value')
            ->whereColumn('translatable_id', $attribute_value_table . '.id')
            ->where([
                'translatable_type' => AttributeValueModel::class,
                'locale' => $locale,
                'key' => 'name'
            ])
            ->getQuery();
        $query->selectSub($attribute_value_name, 'name');

        // Join the translation table for select the name of the attribute
        $query->leftJoin($translation_table . ' as t', function ($join) use ($attribute_value_table, $locale) {
            $join->on('t.translatable_id', '=', $attribute_value_table . '.id')
                ->where('t.translatable_type', '=', AttributeValueModel::class)
                ->where('t.locale', '=', $locale)
                ->where('t.key', '=', 'name');
        });

        $queryBuilder = QueryBuilder::for(AttributeValueModel::class)
            ->fromSub($query, $attribute_value_table)
            ->allowedFields($fields)
            ->allowedSorts($fields)
            ->allowedFilters($fields)
            ->defaultSort([
                'name'
            ])
            ->where($filter);

        $queryBuilder->with('translations');

        if (!empty($with)) {
            $queryBuilder->with($with);
        }

        return $queryBuilder;
    }

    /**
     * Paginate the specified attribute values.
     *
     * @param array $filter
     * @param int $page_limit
     * @param array $with
     *
     * @return AnonymousResourceCollection
     * @throws Throwable
     */
    public function paginate(array $filter = [], int $page_limit = 15, array $with = []): AnonymousResourceCollection
    {
        return AttributeValueResource::collection(
            $this->query($filter, $with)->paginate($page_limit)
        );
    }

    /**
     * Get all attribute values.
     *
     * @param array $filter
     * @param array $with
     *
     * @return AnonymousResourceCollection
     * @throws Throwable
     */
    public function all(array $filter = [], array $with = []): AnonymousResourceCollection
    {
        return AttributeValueResource::collection(
            $this->query($filter, $with)->get()
        );
    }

    /**
     * Store the specified attribute value.
     *
     * @param int $attribute_id
     * @param array $data
     * @return array
     * @throws Throwable
     */
    public function store(int $attribute_id, array $data): array
    {
        $attribute = AttributeModel::find($attribute_id);
        if (!$attribute) {
            throw new AttributeNotFoundException($attribute_id);
        }

        $validator = Validator::make($data, (new StoreAttributeValueRequest)->setAttributeId($attribute_id)->setData($data)->rules());
        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return [
                'ok' => false,
                'message' => trans('package-core::base.validation.errors'),
                'errors' => $errors,
                'status' => 422
            ];
        } else {
            $data = $validator->validated();
        }

        return DB::transaction(function () use ($attribute_id, $data) {
            $attribute_value = new AttributeValueModel;
            $attribute_value->attribute_id = $attribute_id;
            $attribute_value->ordering = $data['ordering'] ?? 0;

            $attribute_value->translation = $data['translation'] ?? [];

            $attribute_value->save();

            event(new AttributeValueStoreEvent($attribute_value, $data));

            return [
                'ok' => true,
                'message' => trans('attribute::base.messages.attribute_value.created'),
                'data' => AttributeValueResource::make($attribute_value),
                'status' => 201
            ];
        });
    }

    /**
     * Update the specified attribute value.
     *
     * @param int $attribute_id
     * @param int $attribute_value_id
     * @param array $data
     *
     * @return array
     * @throws Throwable
     */
    public function update(int $attribute_id, int $attribute_value_id, array $data): array
    {
        $attribute = AttributeModel::find($attribute_id);
        if (!$attribute) {
            throw new AttributeNotFoundException($attribute_id);
        }

        $attribute_value = AttributeValueModel::find($attribute_value_id);
        if (!$attribute_value) {
            throw new AttributeValueNotFoundException($attribute_value_id);
        }

        $validator = Validator::make($data, (new UpdateAttributeValueRequest)->setAttributeId($attribute_id)->setAttributeValueId($attribute_value_id)->rules());
        if ($validator->fails()) {
            $errors = $validator->errors()->all();

            return [
                'ok' => false,
                'message' => trans('package-core::base.validation.errors'),
                'errors' => $errors,
                'status' => 422
            ];
        } else {
            $data = $validator->validated();
        }

        return DB::transaction(function () use ($attribute_value_id, $data, $attribute_value) {
            if (array_key_exists('ordering', $data)) {
                $attribute_value->ordering = $data['ordering'];
            }

            $attribute_value->translation = $data['translation'] ?? [];

            $attribute_value->save();

            event(new AttributeValueUpdateEvent($attribute_value, $data));

            return [
                'ok' => true,
                'message' => trans('attribute::base.messages.attribute_value.updated'),
                'data' => AttributeValueResource::make($attribute_value),
                'status' => 200
            ];
        });
    }

    /**
     * Delete the specified attribute value.
     *
     * @param int $attribute_value_id
     *
     * @return array
     * @throws Throwable
     */
    public function delete(int $attribute_value_id): array
    {
        $attribute_value = AttributeValueModel::find($attribute_value_id);
        if (!$attribute_value) {
            throw new AttributeNotFoundException($attribute_value_id);
        }

        $data = AttributeValueResource::make($attribute_value);

        return DB::transaction(function () use ($attribute_value_id, $attribute_value, $data) {
            if ($this->hasUsed($attribute_value_id)) {
                throw new AttributeValueUsedException($this->getName($attribute_value_id));
            }

            $attribute_value->forgetTranslations();
            $attribute_value->delete();

            event(new AttributeValueDeleteEvent($attribute_value));

            return [
                'ok' => true,
                'message' => trans('attribute::base.messages.attribute_value.deleted'),
                'data' => $data,
                'status' => 200
            ];
        });
    }

    /**
     * Get Name the specified attribute value.
     *
     * @param int $attribute_value_id
     * @param string|null $locale
     *
     * @return string
     * @throws Throwable
     */
    public function getName(int $attribute_value_id, string $locale = null): string
    {
        $attribute_value = AttributeValueModel::find($attribute_value_id);
        if (!$attribute_value) {
            throw new AttributeValueNotFoundException($attribute_value_id);
        }

        $locale = $locale ?? app()->getLocale();

        return Translation::query()->where([
            'translatable_id' => $attribute_value_id,
            'translatable_type' => AttributeValueModel::class,
            'locale' => $locale,
            'key' => 'name'
        ])->value('value');
    }

    /**
     * Used In attribute value
     *
     * @param int $attribute_value_id
     *
     * @return array
     * @throws Throwable
     */
    public function usedIn(int $attribute_value_id): array
    {
        $attribute_value = AttributeValueModel::find($attribute_value_id);
        if (!$attribute_value) {
            throw new AttributeNotFoundException($attribute_value_id);
        }

        $attribute_relation_values = AttributeRelationValue::query()->where([
            'attribute_value_id' => $attribute_value_id
        ])->get();

        return [
            'ok' => true,
            'message' => trans('attribute::base.messages.attribute_value.used_in', [
                'count' => $attribute_relation_values->count()
            ]),
            'data' => AttributeRelationValueResource::collection($attribute_relation_values),
            'status' => 200
        ];
    }

    /**
     * Has Used attribute value
     *
     * @param int $attribute_value_id
     *
     * @return bool
     * @throws Throwable
     */
    public function hasUsed(int $attribute_value_id): bool
    {
        $attribute_value = AttributeValueModel::find($attribute_value_id);

        if (!$attribute_value) {
            throw new AttributeNotFoundException($attribute_value_id);
        }

        return AttributeRelationValue::query()->where([
            'attribute_value_id' => $attribute_value_id
        ])->exists();
    }

    /**
     * Set Translation in list
     *
     * @param array $data
     *
     * @return array
     * @throws Throwable
     */
    public function setTranslation(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $attribute_value = AttributeValueModel::find($data['translatable_id'] ?? null);

            foreach ($data['translation'] as $locale => $translation_data) {
                foreach ($translation_data as $translation_key => $translation_value) {
                    $attribute_value->translate($locale, [
                        $translation_key => $translation_value
                    ]);
                }
            }

            return [
                'ok' => true,
                'message' => trans('attribute::base.messages.attribute_value.set_translation'),
                'status' => 200
            ];
        });
    }
}
