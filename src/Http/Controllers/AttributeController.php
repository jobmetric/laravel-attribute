<?php

namespace JobMetric\Attribute\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use JobMetric\Attribute\Enums\AttributeTypeEnum;
use JobMetric\Attribute\Facades\Attribute as AttributeFacade;
use JobMetric\Attribute\Http\Requests\SetTranslationAttributeRequest;
use JobMetric\Attribute\Http\Requests\StoreAttributeRequest;
use JobMetric\Attribute\Http\Requests\UpdateAttributeRequest;
use JobMetric\Attribute\Http\Resources\AttributeResource;
use JobMetric\Attribute\Models\Attribute;
use JobMetric\Attribute\Models\Attribute as AttributeModel;
use JobMetric\Language\Facades\Language;
use JobMetric\Panelio\Facades\Breadcrumb;
use JobMetric\Panelio\Facades\Button;
use JobMetric\Panelio\Facades\Datatable;
use JobMetric\Panelio\Http\Controllers\Controller;
use Throwable;

class AttributeController extends Controller
{
    private array $route;

    public function __construct()
    {
        if (request()->route()) {
            $parameters = request()->route()->parameters();

            $this->route = [
                'index' => route('attributes.index', $parameters),
                'create' => route('attributes.create', $parameters),
                'store' => route('attributes.store', $parameters),
                'options' => route('attributes.options', $parameters),
                'set_translation' => route('attributes.set-translation', $parameters),
            ];
        }
    }

    /**
     * Display a listing of the attribute.
     *
     * @param string $panel
     * @param string $section
     *
     * @return View|JsonResponse
     * @throws Throwable
     */
    public function index(string $panel, string $section): View|JsonResponse
    {
        if (request()->ajax()) {
            $query = AttributeFacade::query(with: ['translations', 'attributeRelations']);

            return Datatable::of($query, resource_class: AttributeResource::class);
        }

        $data['label'] = trans('attribute::base.list.attribute.title');
        $data['description'] = trans('attribute::base.list.attribute.description');

        DomiTitle($data['label']);

        // Add breadcrumb
        add_breadcrumb_base($panel, $section);
        Breadcrumb::add($data['label']);

        // add button
        Button::add($this->route['create']);
        Button::delete();

        DomiLocalize('attribute', [
            'route' => $this->route['index'],
            'language' => [
                'buttons' => [
                    'attribute_value_list' => trans('attribute::base.list.attribute.buttons.attribute_value_list'),
                ]
            ]
        ]);

        DomiPlugins('jquery.form');

        $translation = (new Attribute)->translationAllowFields();

        DomiAddModal('translation', '', view('translation::modals.translation-array-list', [
            'action' => $this->route['set_translation'],
            'items' => $translation,
            'trans_scope' => 'attribute::base.form.attribute.fields.{field}',
        ]), options: [
            'size' => 'lg'
        ]);

        DomiScript('assets/vendor/attribute/js/attribute/list.js');

        $data['route'] = $this->route['options'];

        return view('attribute::attribute.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param string $panel
     * @param string $section
     *
     * @return View
     */
    public function create(string $panel, string $section): View
    {
        $data['mode'] = 'create';

        $list_label = trans('attribute::base.list.attribute.title');
        $data['label'] = trans('attribute::base.form.attribute.create.title');
        $data['description'] = trans('attribute::base.form.attribute.create.description');

        DomiTitle($data['label']);

        // Add breadcrumb
        add_breadcrumb_base($panel, $section);
        Breadcrumb::add($list_label, $this->route['index']);
        Breadcrumb::add($data['label']);

        // add button
        Button::save();
        Button::saveNew();
        Button::saveClose();
        Button::cancel($this->route['index']);

        $data['action'] = $this->route['store'];
        $data['translations'] = (new Attribute)->translationAllowFields();
        $data['types'] = AttributeTypeEnum::values();

        return view('attribute::attribute.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAttributeRequest $request
     * @param string $panel
     * @param string $section
     *
     * @return RedirectResponse
     * @throws Throwable
     */
    public function store(StoreAttributeRequest $request, string $panel, string $section): RedirectResponse
    {
        $form_data = $request->all();

        $attribute = AttributeFacade::store($request->validated());

        if ($attribute['ok']) {
            $this->alert($attribute['message']);

            if ($form_data['save'] == 'save.new') {
                return back();
            }

            if ($form_data['save'] == 'save.close') {
                return redirect()->to($this->route['index']);
            }

            // btn save
            return redirect()->route('attributes.edit', [
                'panel' => $panel,
                'section' => $section,
                'attribute' => $attribute['data']->id
            ]);
        }

        $this->alert($attribute['message'], 'danger');

        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $panel
     * @param string $section
     * @param AttributeModel $attribute
     *
     * @return View
     */
    public function edit(string $panel, string $section, AttributeModel $attribute): View
    {
        $attribute->load(['translations']);

        $data['mode'] = 'edit';

        $list_label = trans('attribute::base.list.attribute.title');
        $data['label'] = trans('attribute::base.form.attribute.edit.title', [
            'number' => $attribute->id
        ]);
        $data['description'] = trans('attribute::base.form.attribute.edit.description', [
            'number' => $attribute->id
        ]);

        DomiTitle($data['label']);

        // Add breadcrumb
        add_breadcrumb_base($panel, $section);
        Breadcrumb::add($list_label, $this->route['index']);
        Breadcrumb::add($data['label']);

        // add button
        Button::save();
        Button::saveNew();
        Button::saveClose();
        Button::cancel($this->route['index']);

        $data['action'] = route('attributes.update', [
            'panel' => $panel,
            'section' => $section,
            'attribute' => $attribute->id
        ]);

        $data['attribute'] = $attribute;
        $data['languages'] = Language::all();
        $data['translations'] = (new Attribute)->translationAllowFields();
        $data['types'] = AttributeTypeEnum::values();
        $data['translation_edit_values'] = translationResourceData($attribute->translations);

        return view('attribute::attribute.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAttributeRequest $request
     * @param string $panel
     * @param string $section
     * @param AttributeModel $attribute
     *
     * @return RedirectResponse
     * @throws Throwable
     */
    public function update(UpdateAttributeRequest $request, string $panel, string $section, AttributeModel $attribute): RedirectResponse
    {
        $form_data = $request->all();

        $attribute = AttributeFacade::update($attribute->id, $request->validated());

        if ($attribute['ok']) {
            $this->alert($attribute['message']);

            if ($form_data['save'] == 'save.new') {
                return redirect()->to($this->route['create']);
            }

            if ($form_data['save'] == 'save.close') {
                return redirect()->to($this->route['index']);
            }

            // btn save
            return redirect()->route('attributes.edit', [
                'panel' => $panel,
                'section' => $section,
                'attribute' => $attribute['data']->id
            ]);
        }

        $this->alert($attribute['message'], 'danger');

        return back();
    }

    /**
     * Delete the specified resource from storage.
     *
     * @param array $ids
     * @param mixed $params
     * @param string|null $alert
     * @param string|null $danger
     *
     * @return bool
     * @throws Throwable
     */
    public function deletes(array $ids, mixed $params, string &$alert = null, string &$danger = null): bool
    {
        try {
            foreach ($ids as $id) {
                AttributeFacade::delete($id);
            }

            $alert = trans_choice('attribute::base.messages.attribute.deleted_items', count($ids));

            return true;
        } catch (Throwable $e) {
            $danger = $e->getMessage();

            return false;
        }
    }

    /**
     * Set Translation in list
     *
     * @param SetTranslationAttributeRequest $request
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function setTranslation(SetTranslationAttributeRequest $request): JsonResponse
    {
        try {
            return $this->response(
                AttributeFacade::setTranslation($request->validated())
            );
        } catch (Throwable $exception) {
            return $this->response(message: $exception->getMessage(), status: $exception->getCode());
        }
    }
}
