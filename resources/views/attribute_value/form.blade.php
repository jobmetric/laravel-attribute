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
                            $trans_scope = 'attribute::base.form.attribute_value.fields.{field}';
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
                                    <label class="form-label">{{ trans('attribute::base.form.attribute_value.fields.ordering.title') }}</label>
                                    <input type="number" name="ordering" class="form-control mb-2" placeholder="{{ trans('attribute::base.form.attribute_value.fields.ordering.placeholder') }}" value="{{ old('type', $attribute_value->ordering ?? null) }}">
                                    @error('ordering')
                                        <div class="form-errors text-danger fs-7 mt-2">{{ $message }}</div>
                                    @enderror
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
