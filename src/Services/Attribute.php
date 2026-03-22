<?php

namespace JobMetric\Attribute\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use JobMetric\Attribute\Events\Attribute\AttributeDeleteEvent;
use JobMetric\Attribute\Events\Attribute\AttributeStoreEvent;
use JobMetric\Attribute\Events\Attribute\AttributeUpdateEvent;
use JobMetric\Attribute\Exceptions\AttributeNotFoundException;
use JobMetric\Attribute\Exceptions\AttributeUsedException;
use JobMetric\Attribute\Http\Requests\SetTranslationAttributeRequest;
use JobMetric\Attribute\Http\Requests\StoreAttributeRequest;
use JobMetric\Attribute\Http\Requests\UpdateAttributeRequest;
use JobMetric\Attribute\Http\Resources\AttributeRelationResource;
use JobMetric\Attribute\Http\Resources\AttributeResource;
use JobMetric\Attribute\Models\Attribute as AttributeModel;
use JobMetric\Attribute\Models\AttributeRelation;
use JobMetric\PackageCore\Output\Response;
use JobMetric\PackageCore\Services\AbstractCrudService;
use JobMetric\Translation\Models\Translation;
use Spatie\QueryBuilder\QueryBuilder;
use Throwable;

/**
 * CRUD and listing service for Attribute entities (localized name via subquery, usage guards, translations).
 *
 * Responsibilities:
 * - Spatie QueryBuilder over a subquery exposing localized `name` for sort/filter
 * - Validate payloads with dto() and form requests; uniform Response output
 * - Domain events via AbstractCrudService; block delete when attribute is in use
 * - Helpers: getName, usedIn, hasUsed, setTranslation
 */
class Attribute extends AbstractCrudService
{
    /**
     * Human-readable entity name key used in response messages.
     *
     * @var string
     */
    protected string $entityName = 'attribute::base.entity_names.attribute';

    /**
     * Bound model/resource classes for the base CRUD.
     *
     * @var class-string
     */
    protected static string $modelClass = AttributeModel::class;
    protected static string $resourceClass = AttributeResource::class;

    /**
     * Allowed fields for selection/filter/sort in QueryBuilder (includes synthetic `name`).
     *
     * @var string[]
     */
    protected static array $fields = [
        'id',
        'name',
        'type',
        'is_special',
        'is_filter',
        'ordering',
        'created_at',
        'updated_at',
    ];

    /**
     * Default ordering for attribute lists.
     *
     * @var string[]
     */
    protected static array $defaultSort = ['name'];

    /**
     * Domain events mapping for CRUD lifecycle.
     *
     * @var class-string|null
     */
    protected static ?string $storeEventClass = AttributeStoreEvent::class;
    protected static ?string $updateEventClass = AttributeUpdateEvent::class;
    protected static ?string $deleteEventClass = AttributeDeleteEvent::class;

    /**
     * Eager loads merged into mutation responses when caller passes none.
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
        $attribute_table = config('attribute.tables.attribute');
        $translation_table = config('translation.tables.translation');
        $locale = app()->getLocale();

        $base = AttributeModel::query()->select([$attribute_table . '.*']);

        $nameSub = Translation::query()
            ->select('value')
            ->whereColumn('translatable_id', $attribute_table . '.id')
            ->where([
                'translatable_type' => AttributeModel::class,
                'locale'            => $locale,
                'key'               => 'name',
            ])
            ->getQuery();
        $base->selectSub($nameSub, 'name');

        $base->leftJoin($translation_table . ' as t', function ($join) use ($attribute_table, $locale) {
            $join->on('t.translatable_id', '=', $attribute_table . '.id')
                ->where('t.translatable_type', '=', AttributeModel::class)
                ->where('t.locale', '=', $locale)
                ->where('t.key', '=', 'name');
        });

        $qb = QueryBuilder::for(AttributeModel::class)
            ->fromSub($base, $attribute_table)
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
     * Role: validate/normalize payload before create.
     *
     * @param array<string, mixed> $data
     *
     * @return void
     * @throws Throwable
     */
    protected function changeFieldStore(array &$data): void
    {
        $data = dto($data, StoreAttributeRequest::class);
    }

    /**
     * Role: validate/normalize payload before update.
     *
     * @param Model $model
     * @param array<string, mixed> $data
     *
     * @return void
     * @throws Throwable
     */
    protected function changeFieldUpdate(Model $model, array &$data): void
    {
        $data = dto($data, UpdateAttributeRequest::class, ['id' => $model->id]);
    }

    /**
     * Role: assign translatable fields before save.
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
     * Role: assign translatable fields before save on update.
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
     * Role: refuse delete when attribute is linked; clear translations before delete.
     *
     * @param Model $model
     *
     * @return void
     * @throws Throwable
     */
    protected function beforeDestroy(Model $model): void
    {
        if ($this->hasUsed((int) $model->getKey())) {
            throw new AttributeUsedException($this->getName((int) $model->getKey()));
        }

        $model->forgetTranslations();
    }

    /**
     * Override base CRUD methods to inject default eager loads for mutation responses.
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
     * Override base CRUD methods to inject default eager loads for mutation responses.
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
     * Override base CRUD methods to inject default eager loads for mutation responses.
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
     * Resolved display name from translations.
     *
     * @param int $attribute_id
     * @param string|null $locale
     *
     * @return string
     * @throws AttributeNotFoundException
     */
    public function getName(int $attribute_id, ?string $locale = null): string
    {
        $attribute = AttributeModel::query()->find($attribute_id);

        if (! $attribute) {
            throw new AttributeNotFoundException($attribute_id);
        }

        $locale = $locale ?? app()->getLocale();

        return (string) Translation::query()->where([
            'translatable_id'   => $attribute_id,
            'translatable_type' => AttributeModel::class,
            'locale'            => $locale,
            'key'               => 'name',
        ])->value('value');
    }

    /**
     * List relation rows where this attribute is attached.
     *
     * @param int $attribute_id
     *
     * @return Response
     * @throws AttributeNotFoundException
     */
    public function usedIn(int $attribute_id): Response
    {
        $attribute = AttributeModel::query()->find($attribute_id);

        if (! $attribute) {
            throw new AttributeNotFoundException($attribute_id);
        }

        $relations = AttributeRelation::query()->where(['attribute_id' => $attribute_id])->with([
            'attributable',
            'attribute',
        ])->get();

        return Response::make(true, trans('attribute::base.messages.attribute.used_in', [
            'count' => $relations->count(),
        ]), AttributeRelationResource::collection($relations));
    }

    /**
     * Whether the attribute is referenced by any relation.
     *
     * @param int $attribute_id
     *
     * @return bool
     * @throws AttributeNotFoundException
     */
    public function hasUsed(int $attribute_id): bool
    {
        $attribute = AttributeModel::query()->find($attribute_id);

        if (! $attribute) {
            throw new AttributeNotFoundException($attribute_id);
        }

        return AttributeRelation::query()->where([
            'attribute_id' => $attribute_id,
        ])->exists();
    }

    /**
     * Bulk-set translations from list UI payload.
     *
     * @param array<string, mixed> $data
     *
     * @return Response
     * @throws Throwable
     */
    public function setTranslation(array $data): Response
    {
        $validated = dto($data, SetTranslationAttributeRequest::class);

        return DB::transaction(function () use ($validated) {
            $attribute = AttributeModel::query()->findOrFail($validated['translatable_id']);

            foreach ($validated['translation'] as $locale => $translation_data) {
                foreach ($translation_data as $translation_key => $translation_value) {
                    $attribute->translate($locale, [
                        $translation_key => $translation_value,
                    ]);
                }
            }

            return Response::make(true, trans('attribute::base.messages.attribute.set_translation'));
        });
    }
}
