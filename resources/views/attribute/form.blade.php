@extends('panelio::layout.layout')

@section('body')
    <form method="post" action="{{ $action }}" class="form d-flex flex-column flex-lg-row" id="form">
        @csrf
        @if($mode === 'edit')
            @method('put')
        @endif
        <div class="d-flex flex-column flex-row-fluid gap-7 gap-lg-12">
            <div class="tab-content">
                <div class="tab-pane fade show active">
                    <div class="d-flex flex-column gap-7 gap-lg-10">
                        @php
                            $trans_scope = 'attribute::base.form.attribute.fields.{field}';
                        @endphp
                        @if($mode === 'create')
                            @php
                                $translation_values = [];
                                foreach($translations as $item) {
                                    $translation_locale = app()->getLocale();
                                    $translation_values[$item] = old("translation.$translation_locale.$item");
                                }
                            @endphp
                            <x-translation-card :items="$translations" :values="$translation_values" :trans-scope="$trans_scope" />
                        @endif

                        @if($mode === 'edit')
                            @php
                                $translation_values = [];
                                foreach ($languages as $language) {
                                    foreach($translations as $item) {
                                        $translation_values[$language->locale][$item] = old("translation.$language->locale.$item", $translation_edit_values[$language->locale][$item] ?? null);
                                    }
                                }
                            @endphp
                            <x-translation-card :items="$translations" :values="$translation_values" :trans-scope="$trans_scope" multiple />
                        @endif

                        <!--begin::Information-->
                        <div class="card card-flush py-4">
                            <div class="card-header">
                                <div class="card-title">
                                    <span class="fs-5 fw-bold">{{ trans('package-core::base.cards.proprietary_info') }}</span>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="mb-10">
                                    <label class="form-label">{{ trans('attribute::base.form.attribute.fields.type.title') }}</label>
                                    <select name="type" class="form-select" data-control="select2">
                                        <option value="">{{ trans('package-core::base.select.none') }}</option>
                                        @foreach($types as $type)
                                            <option value="{{ $type }}" @if(old('type', $attribute->type ?? null) == $type) selected @endif>{{ trans('attribute::base.enums.attribute_type.' . $type) }}</option>
                                        @endforeach
                                    </select>
                                    @error('type')
                                        <div class="form-errors text-danger fs-7 mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-10">
                                    <label class="form-label">{{ trans('attribute::base.form.attribute.fields.ordering.title') }}</label>
                                    <input type="number" name="ordering" class="form-control mb-2" placeholder="{{ trans('attribute::base.form.attribute.fields.ordering.placeholder') }}" value="{{ old('type', $attribute->ordering ?? null) }}">
                                    @error('ordering')
                                        <div class="form-errors text-danger fs-7 mt-2">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="d-flex align-items-center gap-10">
                                    <div class="form-check form-check-custom form-check-solid form-check-sm">
                                        <input type="checkbox" name="is_filter" class="form-check-input" value="1" id="input-is-filter" @if($attribute->is_filter == '1') checked @endif/>
                                        <label class="form-check-label" for="input-is-filter">{{ trans('attribute::base.form.attribute.fields.is_filter.title') }}</label>
                                    </div>
                                    <div class="form-check form-check-custom form-check-solid form-check-sm">
                                        <input type="checkbox" name="is_special" class="form-check-input" value="1" id="input-is-special" @if($attribute->is_special == '1') checked @endif/>
                                        <label class="form-check-label" for="input-is-special">{{ trans('attribute::base.form.attribute.fields.is_special.title') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!--end::Information-->
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection
