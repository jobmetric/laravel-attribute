<?php

namespace JobMetric\Attribute\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use JobMetric\Attribute\Events\AttributeValueDeleteEvent;
use JobMetric\Attribute\Events\AttributeValueStoreEvent;
use JobMetric\Attribute\Events\AttributeValueUpdateEvent;
use JobMetric\Attribute\Exceptions\AttributeNotFoundException;
use JobMetric\Attribute\Exceptions\AttributeValueNotFoundException;
use JobMetric\Attribute\Exceptions\AttributeValueUsedException;
use JobMetric\Attribute\Http\Requests\SetTranslationAttributeValueRequest;
use JobMetric\Attribute\Http\Requests\StoreAttributeValueRequest;
use JobMetric\Attribute\Http\Requests\UpdateAttributeValueRequest;
use JobMetric\Attribute\Http\Resources\AttributeRelationValueResource;
use JobMetric\Attribute\Http\Resources\AttributeValueResource;
use JobMetric\Attribute\Models\Attribute as AttributeModel;
use JobMetric\Attribute\Models\AttributeRelationValue;
use JobMetric\Attribute\Models\AttributeValue as AttributeValueModel;
use JobMetric\PackageCore\Output\Response;
use JobMetric\PackageCore\Services\AbstractCrudService;
use JobMetric\Translation\Models\Translation;
use Spatie\QueryBuilder\QueryBuilder;
use Throwable;

/**
 * CRUD and listing service for AttributeValue entities (scoped to a parent Attribute).
 *
 * Responsibilities:
 * - Spatie QueryBuilder over a subquery exposing localized `name`
 * - dto validation; store/update scoped to parent attribute id when required
 * - Block delete when pivot rows exist; uniform Response output
 */
class AttributeValue extends AbstractCrudService
{
    /**
     * Human-readable entity name key used in response messages.
     *
     * @var string
     */
    protected string $entityName = 'attribute::base.entity_names.attribute_value';

    /**
     * Bound model/resource classes for the base CRUD.
     *
     * @var class-string
     */
    protected static string $modelClass = AttributeValueModel::class;
    protected static string $resourceClass = AttributeValueResource::class;

    /**
     * Allowed fields for selection/filter/sort in QueryBuilder (includes synthetic `name`).
     *
     * @var string[]
     */
    protected static array $fields = [
        'id',
        'name',
        'attribute_id',
        'ordering',
        'created_at',
        'updated_at',
    ];

    /**
     * Default ordering for value lists.
     *
     * @var string[]
     */
    protected static array $defaultSort = ['name'];

    /**
     * Domain events mapping for CRUD lifecycle.
     *
     * @var class-string|null
     */
    protected static ?string $storeEventClass = AttributeValueStoreEvent::class;
    protected static ?string $updateEventClass = AttributeValueUpdateEvent::class;
    protected static ?string $deleteEventClass = AttributeValueDeleteEvent::class;

    /**
     * Ensure `translations` relation is always loaded for mutations.
     *
     * @param array<int, string> $with
     *
     * @return array<int, string>
     */
    protected function mutationWith(array $with): array
    {
        return array_values(array_unique(array_merge(['translations'], $with)));
    }

    /**
     * Build QueryBuilder over a subquery with localized `name` column.
     *
     * @param array<string, mixed> $filters
     * @param array<int, string> $with
     * @param string|null $mode
     *
     * @return QueryBuilder
     * @throws Throwable
     */
    public function query(array $filters = [], array $with = [], ?string $mode = null): QueryBuilder
    {
        $attribute_value_table = config('attribute.tables.attribute_value');
        $translation_table = config('translation.tables.translation');
        $locale = app()->getLocale();

        $base = AttributeValueModel::query()->select([$attribute_value_table . '.*']);

        $nameSub = Translation::query()
            ->select('value')
            ->whereColumn('translatable_id', $attribute_value_table . '.id')
            ->where([
                'translatable_type' => AttributeValueModel::class,
                'locale'            => $locale,
                'key'               => 'name',
            ])
            ->getQuery();
        $base->selectSub($nameSub, 'name');

        $base->leftJoin($translation_table . ' as t', function ($join) use ($attribute_value_table, $locale) {
            $join->on('t.translatable_id', '=', $attribute_value_table . '.id')
                ->where('t.translatable_type', '=', AttributeValueModel::class)
                ->where('t.locale', '=', $locale)
                ->where('t.key', '=', 'name');
        });

        $qb = QueryBuilder::for(AttributeValueModel::class)
            ->fromSub($base, $attribute_value_table)
            ->allowedFields(static::$fields)
            ->allowedSorts(static::$fields)
            ->allowedFilters(static::$fields)
            ->defaultSort(static::$defaultSort)
            ->where($filters);

        $qb->with('translations');

        if (! empty($with)) {
            $qb->with($with);
        }

        return $qb;
    }

    /**
     * Role: ensure parent exists and validate payload before create.
     *
     * @param array<string, mixed> $data
     *
     * @return void
     * @throws Throwable
     */
    protected function changeFieldStore(array &$data): void
    {
        if (empty($data['attribute_id'])) {
            throw ValidationException::withMessages([
                'attribute_id' => [trans('validation.required', ['attribute' => 'attribute_id'])],
            ]);
        }

        AttributeModel::query()->findOrFail($data['attribute_id']);

        $data = dto($data, StoreAttributeValueRequest::class, [
            'attribute_id' => (int) $data['attribute_id'],
        ]);
    }

    /**
     * Role: validate payload before update.
     *
     * @param Model $model
     * @param array<string, mixed> $data
     *
     * @return void
     * @throws Throwable
     */
    protected function changeFieldUpdate(Model $model, array &$data): void
    {
        $data = dto($data, UpdateAttributeValueRequest::class, [
            'attribute_id'       => (int) $model->getAttribute('attribute_id'),
            'attribute_value_id' => (int) $model->getKey(),
        ]);
    }

    /**
     * Role: assign translatable fields before create.
     *
     * @param Model $model
     * @param array<string, mixed> $data
     *
     * @return void
     */
    protected function beforeStore(Model $model, array &$data): void
    {
        if (array_key_exists('translation', $data)) {
            $model->translation = $data['translation'];
        }
    }

    /**
     * Role: assign translatable fields before update.
     *
     * @param Model $model
     * @param array<string, mixed> $data
     *
     * @return void
     */
    protected function beforeUpdate(Model $model, array &$data): void
    {
        if (array_key_exists('translation', $data)) {
            $model->translation = $data['translation'];
        }
    }

    /**
     * Role: refuse delete when value is linked; clear translations.
     *
     * @param Model $model
     *
     * @return void
     * @throws Throwable
     */
    protected function beforeDestroy(Model $model): void
    {
        $id = (int) $model->getKey();

        if ($this->hasUsed($id)) {
            throw new AttributeValueUsedException($this->getName($id));
        }

        $model->forgetTranslations();
    }

    /**
     * Create a value under a given attribute (panel entry point).
     *
     * @param int $attributeId
     * @param array<string, mixed> $data
     * @param array<int, string> $with
     *
     * @return Response
     * @throws Throwable
     */
    public function storeForAttribute(int $attributeId, array $data, array $with = []): Response
    {
        $data['attribute_id'] = $attributeId;

        return $this->store($data, $with);
    }

    /**
     * Update ensuring the value belongs to the given attribute.
     *
     * @param int $attributeId
     * @param int $attributeValueId
     * @param array<string, mixed> $data
     * @param array<int, string> $with
     *
     * @return Response
     * @throws AttributeNotFoundException
     * @throws AttributeValueNotFoundException
     * @throws Throwable
     */
    public function updateForAttribute(int $attributeId, int $attributeValueId, array $data, array $with = []): Response
    {
        $value = AttributeValueModel::query()->whereKey($attributeValueId)->first();

        if (! $value) {
            throw new AttributeValueNotFoundException($attributeValueId);
        }

        if ((int) $value->getAttribute('attribute_id') !== $attributeId) {
            throw new AttributeNotFoundException($attributeId);
        }

        return $this->update($attributeValueId, $data, $with);
    }

    /**
     * Delete ensuring the value belongs to the given attribute.
     *
     * @param array<string, mixed> $data
     * @param array<int, string> $with
     *
     * @return Response
     * @throws Throwable
     */
    public function store(array $data, array $with = []): Response
    {
        return parent::store($data, $this->mutationWith($with));
    }

    /**
     * Update ensuring the value belongs to the given attribute.
     *
     * @param int $id
     * @param array<string, mixed> $data
     * @param array<int, string> $with
     *
     * @return Response
     * @throws Throwable
     */
    public function update(int $id, array $data, array $with = []): Response
    {
        return parent::update($id, $data, $this->mutationWith($with));
    }

    /**
     * Delete ensuring the value belongs to the given attribute.
     *
     * @param int $id
     * @param array<int, string> $with
     *
     * @return Response
     * @throws Throwable
     */
    public function destroy(int $id, array $with = []): Response
    {
        return parent::destroy($id, $this->mutationWith($with));
    }

    /**
     * Alias for destroy (panel / legacy callers).
     *
     * @param int $id
     * @param array<int, string> $with
     *
     * @return Response
     * @throws Throwable
     */
    public function delete(int $id, array $with = []): Response
    {
        return $this->destroy($id, $with);
    }

    /**
     * Get the localized name of an attribute value by its ID.
     *
     * @param int $attribute_value_id
     * @param string|null $locale
     *
     * @return string
     * @throws AttributeValueNotFoundException
     */
    public function getName(int $attribute_value_id, ?string $locale = null): string
    {
        $attribute_value = AttributeValueModel::query()->find($attribute_value_id);

        if (! $attribute_value) {
            throw new AttributeValueNotFoundException($attribute_value_id);
        }

        $locale = $locale ?? app()->getLocale();

        return (string) Translation::query()->where([
            'translatable_id'   => $attribute_value_id,
            'translatable_type' => AttributeValueModel::class,
            'locale'            => $locale,
            'key'               => 'name',
        ])->value('value');
    }

    /**
     * List relations where this value is used.
     *
     * @param int $attribute_value_id
     *
     * @return Response
     * @throws AttributeNotFoundException
     */
    public function usedIn(int $attribute_value_id): Response
    {
        $attribute_value = AttributeValueModel::query()->find($attribute_value_id);

        if (! $attribute_value) {
            throw new AttributeNotFoundException($attribute_value_id);
        }

        $rows = AttributeRelationValue::query()->where([
            'attribute_value_id' => $attribute_value_id,
        ])->get();

        return Response::make(true, trans('attribute::base.messages.attribute_value.used_in', [
            'count' => $rows->count(),
        ]), AttributeRelationValueResource::collection($rows));
    }

    /**
     * Check if the attribute value is used in any relations.
     *
     * @param int $attribute_value_id
     *
     * @return bool
     * @throws AttributeNotFoundException
     */
    public function hasUsed(int $attribute_value_id): bool
    {
        $attribute_value = AttributeValueModel::query()->find($attribute_value_id);

        if (! $attribute_value) {
            throw new AttributeNotFoundException($attribute_value_id);
        }

        return AttributeRelationValue::query()->where([
            'attribute_value_id' => $attribute_value_id,
        ])->exists();
    }

    /**
     * Set translations for an attribute value (panel entry point).
     *
     * @param array<string, mixed> $data
     *
     * @return Response
     * @throws Throwable
     */
    public function setTranslation(array $data): Response
    {
        $validated = dto($data, SetTranslationAttributeValueRequest::class);

        return DB::transaction(function () use ($validated) {
            $attribute_value = AttributeValueModel::query()->findOrFail($validated['translatable_id']);

            foreach ($validated['translation'] as $locale => $translation_data) {
                foreach ($translation_data as $translation_key => $translation_value) {
                    $attribute_value->translate($locale, [
                        $translation_key => $translation_value,
                    ]);
                }
            }

            return Response::make(true, trans('attribute::base.messages.attribute_value.set_translation'), null, 200);
        });
    }
}
