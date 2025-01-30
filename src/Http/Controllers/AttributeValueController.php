<?php

namespace JobMetric\Attribute\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use JobMetric\Attribute\Facades\Attribute as AttributeFacade;
use JobMetric\Attribute\Facades\AttributeValue as AttributeValueFacade;
use JobMetric\Attribute\Http\Requests\SetTranslationAttributeValueRequest;
use JobMetric\Attribute\Http\Requests\StoreAttributeValueRequest;
use JobMetric\Attribute\Http\Requests\UpdateAttributeValueRequest;
use JobMetric\Attribute\Http\Resources\AttributeValueResource;
use JobMetric\Attribute\Models\Attribute as AttributeModel;
use JobMetric\Attribute\Models\AttributeValue as AttributeValueModel;
use JobMetric\Language\Facades\Language;
use JobMetric\Panelio\Facades\Breadcrumb;
use JobMetric\Panelio\Facades\Button;
use JobMetric\Panelio\Facades\Datatable;
use JobMetric\Panelio\Http\Controllers\Controller;
use Throwable;

class AttributeValueController extends Controller
{
    private array $route;

    public function __construct()
    {
        if (request()->route()) {
            $parameters = request()->route()->parameters();

            $this->route = [
                'index' => route('attributes_values.index', $parameters),
                'create' => route('attributes_values.create', $parameters),
                'store' => route('attributes_values.store', $parameters),
                'options' => route('attributes_values.options', $parameters),
                'set_translation' => route('attributes_values.set-translation', $parameters),
            ];

            array_pop($parameters);

            $this->route['attribute'] = [
                'index' => route('attributes.index', $parameters),
            ];
        }
    }

    /**
     * Display a listing of the attribute.
     *
     * @param string $panel
     * @param string $section
     * @param AttributeModel $attribute
     *
     * @return View|JsonResponse
     * @throws Throwable
     */
    public function index(string $panel, string $section, AttributeModel $attribute): View|JsonResponse
    {
        if (request()->ajax()) {
            $query = AttributeValueFacade::query([
                'attribute_id' => $attribute->id
            ], ['translations', 'attributeRelations']);

            return Datatable::of($query, resource_class: AttributeValueResource::class);
        }

        $attribute_name = AttributeFacade::getName($attribute->id);
        $attribute_list_name = trans('attribute::base.list.attribute.title');
        $data['label'] = trans('attribute::base.list.attribute_value.title', [
            'name' => $attribute_name
        ]);
        $data['description'] = trans('attribute::base.list.attribute_value.description', [
            'name' => $attribute_name
        ]);

        DomiTitle($data['label']);

        // Add breadcrumb
        add_breadcrumb_base($panel, $section);
        Breadcrumb::add($attribute_list_name, $this->route['attribute']['index']);
        Breadcrumb::add($data['label']);

        // add button
        Button::add($this->route['create']);
        Button::delete();

        DomiLocalize('attribute_value', [
            'route' => $this->route['index'],
        ]);

        DomiPlugins('jquery.form');

        $translation = (new AttributeValueModel)->translationAllowFields();

        DomiAddModal('translation', '', view('translation::modals.translation-array-list', [
            'action' => $this->route['set_translation'],
            'items' => $translation,
            'trans_scope' => 'attribute::base.form.attribute_value.fields.{field}',
        ]), options: [
            'size' => 'lg'
        ]);

        DomiScript('assets/vendor/attribute/js/attribute_value/list.js');

        $data['route'] = $this->route['options'];

        return view('attribute::attribute_value.list', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param string $panel
     * @param string $section
     * @param AttributeModel $attribute
     *
     * @return View
     */
    public function create(string $panel, string $section, AttributeModel $attribute): View
    {
        $data['mode'] = 'create';

        $attribute_name = AttributeFacade::getName($attribute->id);
        $attribute_list_name = trans('attribute::base.list.attribute.title');
        $list_label = trans('attribute::base.list.attribute_value.title', [
            'name' => $attribute_name
        ]);
        $data['label'] = trans('attribute::base.form.attribute_value.create.title', [
            'name' => $attribute_name
        ]);
        $data['description'] = trans('attribute::base.form.attribute_value.create.description', [
            'name' => $attribute_name
        ]);

        DomiTitle($data['label']);

        // Add breadcrumb
        add_breadcrumb_base($panel, $section);
        Breadcrumb::add($attribute_list_name, $this->route['attribute']['index']);
        Breadcrumb::add($list_label, $this->route['index']);
        Breadcrumb::add($data['label']);

        // add button
        Button::save();
        Button::saveNew();
        Button::saveClose();
        Button::cancel($this->route['index']);

        $data['action'] = $this->route['store'];
        $data['translations'] = (new AttributeValueModel)->translationAllowFields();

        return view('attribute::attribute_value.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreAttributeValueRequest $request
     * @param string $panel
     * @param string $section
     * @param AttributeModel $attribute
     *
     * @return RedirectResponse
     * @throws Throwable
     */
    public function store(StoreAttributeValueRequest $request, string $panel, string $section, AttributeModel $attribute): RedirectResponse
    {
        $form_data = $request->all();

        $attribute_value = AttributeValueFacade::store($attribute->id, $request->validated());

        if ($attribute_value['ok']) {
            $this->alert($attribute_value['message']);

            if ($form_data['save'] == 'save.new') {
                return back();
            }

            if ($form_data['save'] == 'save.close') {
                return redirect()->to($this->route['index']);
            }

            // btn save
            return redirect()->route('attributes_values.edit', [
                'panel' => $panel,
                'section' => $section,
                'attribute' => $attribute->id,
                'value' => $attribute_value['data']->id
            ]);
        }

        $this->alert($attribute_value['message'], 'danger');

        return back();
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param string $panel
     * @param string $section
     * @param AttributeModel $attribute
     * @param AttributeValueModel $value
     *
     * @return View
     */
    public function edit(string $panel, string $section, AttributeModel $attribute, AttributeValueModel $value): View
    {
        $value->load(['translations']);

        $data['mode'] = 'edit';

        $attribute_name = AttributeFacade::getName($attribute->id);
        $attribute_list_name = trans('attribute::base.list.attribute.title');
        $list_label = trans('attribute::base.list.attribute_value.title', [
            'name' => $attribute_name
        ]);
        $data['label'] = trans('attribute::base.form.attribute_value.edit.title', [
            'name' => $attribute_name,
            'number' => $attribute->id
        ]);
        $data['description'] = trans('attribute::base.form.attribute_value.edit.description', [
            'name' => $attribute_name,
            'number' => $attribute->id
        ]);

        DomiTitle($data['label']);

        // Add breadcrumb
        add_breadcrumb_base($panel, $section);
        Breadcrumb::add($attribute_list_name, $this->route['attribute']['index']);
        Breadcrumb::add($list_label, $this->route['index']);
        Breadcrumb::add($data['label']);

        // add button
        Button::save();
        Button::saveNew();
        Button::saveClose();
        Button::cancel($this->route['index']);

        $data['action'] = route('attributes_values.update', [
            'panel' => $panel,
            'section' => $section,
            'attribute' => $attribute->id,
            'value' => $value->id
        ]);

        $data['attribute_value'] = $value;
        $data['languages'] = Language::all();
        $data['translations'] = (new AttributeValueModel)->translationAllowFields();
        $data['translation_edit_values'] = translationResourceData($value->translations);

        return view('attribute::attribute_value.form', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param UpdateAttributeValueRequest $request
     * @param string $panel
     * @param string $section
     * @param AttributeModel $attribute
     * @param AttributeValueModel $value
     *
     * @return RedirectResponse
     * @throws Throwable
     */
    public function update(UpdateAttributeValueRequest $request, string $panel, string $section, AttributeModel $attribute, AttributeValueModel $value): RedirectResponse
    {
        $form_data = $request->all();

        $attribute_value = AttributeValueFacade::update($attribute->id, $value->id, $request->validated());

        if ($attribute_value['ok']) {
            $this->alert($attribute_value['message']);

            if ($form_data['save'] == 'save.new') {
                return redirect()->to($this->route['create']);
            }

            if ($form_data['save'] == 'save.close') {
                return redirect()->to($this->route['index']);
            }

            // btn save
            return redirect()->route('attributes_values.edit', [
                'panel' => $panel,
                'section' => $section,
                'attribute' => $attribute->id,
                'value' => $attribute_value['data']->id
            ]);
        }

        $this->alert($attribute_value['message'], 'danger');

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
                AttributeValueFacade::delete($id);
            }

            $alert = trans_choice('attribute::base.messages.attribute_value.deleted_items', count($ids));

            return true;
        } catch (Throwable $e) {
            $danger = $e->getMessage();

            return false;
        }
    }

    /**
     * Set Translation in list
     *
     * @param SetTranslationAttributeValueRequest $request
     *
     * @return JsonResponse
     * @throws Throwable
     */
    public function setTranslation(SetTranslationAttributeValueRequest $request): JsonResponse
    {
        try {
            return $this->response(
                AttributeValueFacade::setTranslation($request->validated())
            );
        } catch (Throwable $exception) {
            return $this->response(message: $exception->getMessage(), status: $exception->getCode());
        }
    }
}
