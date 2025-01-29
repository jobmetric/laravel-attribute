<?php

namespace JobMetric\Attribute;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use JobMetric\Attribute\Events\AttributeDeleteEvent;
use JobMetric\Attribute\Events\AttributeStoreEvent;
use JobMetric\Attribute\Events\AttributeUpdateEvent;
use JobMetric\Attribute\Exceptions\AttributeNotFoundException;
use JobMetric\Attribute\Exceptions\AttributeUsedException;
use JobMetric\Attribute\Http\Requests\StoreAttributeRequest;
use JobMetric\Attribute\Http\Requests\UpdateAttributeRequest;
use JobMetric\Attribute\Http\Resources\AttributeRelationResource;
use JobMetric\Attribute\Http\Resources\AttributeResource;
use JobMetric\Attribute\Models\Attribute as AttributeModel;
use JobMetric\Attribute\Models\AttributeRelation;
use JobMetric\Metadata\HasFilterMeta;
use JobMetric\Translation\Models\Translation;
use Spatie\QueryBuilder\QueryBuilder;
use Throwable;

class Attribute
{
    use HasFilterMeta;

    /**
     * Get the specified attribute.
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
            'type',
            'is_special',
            'is_filter',
            'ordering',
            'created_at',
            'updated_at'
        ];

        $attribute_table = config('attribute.tables.attribute');
        $translation_table = config('translation.tables.translation');

        $locale = app()->getLocale();

        $query = AttributeModel::query()->select([$attribute_table . '.*']);

        // Get the name of the attribute
        $attribute_name = Translation::query()
            ->select('value')
            ->whereColumn('translatable_id', $attribute_table . '.id')
            ->where([
                'translatable_type' => AttributeModel::class,
                'locale' => $locale,
                'key' => 'name'
            ])
            ->getQuery();
        $query->selectSub($attribute_name, 'name');

        // Join the translation table for select the name of the attribute
        $query->leftJoin($translation_table . ' as t', function ($join) use ($attribute_table, $locale) {
            $join->on('t.translatable_id', '=', $attribute_table . '.id')
                ->where('t.translatable_type', '=', AttributeModel::class)
                ->where('t.locale', '=', $locale)
                ->where('t.key', '=', 'name');
        });

        $queryBuilder = QueryBuilder::for(AttributeModel::class)
            ->fromSub($query, $attribute_table)
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
     * Paginate the specified attributes.
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
        return AttributeResource::collection(
            $this->query($filter, $with)->paginate($page_limit)
        );
    }

    /**
     * Get all attributes.
     *
     * @param array $filter
     * @param array $with
     *
     * @return AnonymousResourceCollection
     * @throws Throwable
     */
    public function all(array $filter = [], array $with = []): AnonymousResourceCollection
    {
        return AttributeResource::collection(
            $this->query($filter, $with)->get()
        );
    }

    /**
     * Store the specified attribute.
     *
     * @param array $data
     * @return array
     * @throws Throwable
     */
    public function store(array $data): array
    {
        $validator = Validator::make($data, (new StoreAttributeRequest)->setData($data)->rules());
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

        return DB::transaction(function () use ($data) {
            $attribute = new AttributeModel;
            $attribute->type = $data['type'];
            $attribute->is_special = $data['is_special'] ?? false;
            $attribute->is_filter = $data['is_filter'] ?? false;
            $attribute->ordering = $data['ordering'] ?? 0;

            $attribute->translation = $data['translation'] ?? [];

            $attribute->save();

            event(new AttributeStoreEvent($attribute, $data));

            return [
                'ok' => true,
                'message' => trans('attribute::base.messages.attribute.created'),
                'data' => AttributeResource::make($attribute),
                'status' => 201
            ];
        });
    }

    /**
     * Update the specified attribute.
     *
     * @param int $attribute_id
     * @param array $data
     *
     * @return array
     * @throws Throwable
     */
    public function update(int $attribute_id, array $data): array
    {
        $attribute = AttributeModel::find($attribute_id);

        if (!$attribute) {
            throw new AttributeNotFoundException($attribute_id);
        }

        $validator = Validator::make($data, (new UpdateAttributeRequest)->setAttributeId($attribute_id)->rules());
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

        return DB::transaction(function () use ($attribute_id, $data, $attribute) {
            if (array_key_exists('type', $data)) {
                $attribute->type = $data['type'];
            }

            if (array_key_exists('is_special', $data)) {
                $attribute->is_special = $data['is_special'];
            }

            if (array_key_exists('is_filter', $data)) {
                $attribute->is_filter = $data['is_filter'];
            }

            if (array_key_exists('ordering', $data)) {
                $attribute->ordering = $data['ordering'];
            }

            $attribute->translation = $data['translation'] ?? [];

            $attribute->save();

            event(new AttributeUpdateEvent($attribute, $data));

            return [
                'ok' => true,
                'message' => trans('attribute::base.messages.attribute.updated'),
                'data' => AttributeResource::make($attribute),
                'status' => 200
            ];
        });
    }

    /**
     * Delete the specified attribute.
     *
     * @param int $attribute_id
     *
     * @return array
     * @throws Throwable
     */
    public function delete(int $attribute_id): array
    {
        $attribute = AttributeModel::find($attribute_id);

        if (!$attribute) {
            throw new AttributeNotFoundException($attribute_id);
        }

        $data = AttributeResource::make($attribute);

        return DB::transaction(function () use ($attribute_id, $attribute, $data) {
            if ($this->hasUsed($attribute_id)) {
                throw new AttributeUsedException($this->getName($attribute_id));
            }

            $attribute->forgetTranslations();
            $attribute->delete();

            event(new AttributeDeleteEvent($attribute));

            return [
                'ok' => true,
                'message' => trans('attribute::base.messages.deleted'),
                'data' => $data,
                'status' => 200
            ];
        });
    }

    /**
     * Get Name the specified attribute.
     *
     * @param int $attribute_id
     * @param string|null $locale
     *
     * @return string
     * @throws Throwable
     */
    public function getName(int $attribute_id, string $locale = null): string
    {
        $attribute = AttributeModel::find($attribute_id);

        if (!$attribute) {
            throw new AttributeNotFoundException($attribute_id);
        }

        $locale = $locale ?? app()->getLocale();

        return Translation::query()->where([
            'translatable_id' => $attribute_id,
            'translatable_type' => AttributeModel::class,
            'locale' => $locale,
            'key' => 'name'
        ])->value('value');
    }

    /**
     * Used In attribute
     *
     * @param int $attribute_id
     *
     * @return array
     * @throws Throwable
     */
    public function usedIn(int $attribute_id): array
    {
        $attribute = AttributeModel::find($attribute_id);

        if (!$attribute) {
            throw new AttributeNotFoundException($attribute_id);
        }

        $attribute_relations = AttributeRelation::query()->where([
            'attribute_id' => $attribute_id
        ])->get();

        return [
            'ok' => true,
            'message' => trans('attribute::base.messages.attribute.used_in', [
                'count' => $attribute_relations->count()
            ]),
            'data' => AttributeRelationResource::collection($attribute_relations),
            'status' => 200
        ];
    }

    /**
     * Has Used attribute
     *
     * @param int $attribute_id
     *
     * @return bool
     * @throws Throwable
     */
    public function hasUsed(int $attribute_id): bool
    {
        $attribute = AttributeModel::find($attribute_id);

        if (!$attribute) {
            throw new AttributeNotFoundException($attribute_id);
        }

        return AttributeRelation::query()->where([
            'attribute_id' => $attribute_id
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
            $attribute = AttributeModel::find($data['translatable_id'] ?? null);

            foreach ($data['translation'] as $locale => $translation_data) {
                foreach ($translation_data as $translation_key => $translation_value) {
                    $attribute->translate($locale, [
                        $translation_key => $translation_value
                    ]);
                }
            }

            return [
                'ok' => true,
                'message' => trans('attribute::base.messages.attribute.set_translation'),
                'status' => 200
            ];
        });
    }
}
