@extends('panelio::layout.layout')

@section('body')
    <x-list-view name="{{ $label }}" action="{{ $route }}">
        <x-slot name="filter">
            <div class="col-md-3">
                <div class="mb-5">
                    <label class="form-label">{{ trans('attribute::base.list.attribute.filters.name.title') }}</label>
                    <input type="text" name="translation[name]" class="form-control filter-list" id="filter-name" placeholder="{{ trans('attribute::base.list.attribute.filters.name.placeholder') }}" autocomplete="off">
                </div>
            </div>
        </x-slot>

        <thead>
            <tr>
                <th width="1%"></th>
                <th width="1%">
                    <div class="form-check form-check-custom form-check-solid">
                        <input class="form-check-input" type="checkbox" value="1" id="check-all"/>
                        <label class="form-check-label ms-0" for="check-all"></label>
                    </div>
                </th>
                <th width="48%" class="text-gray-800 auto-width-content">{{ trans('package-core::base.list.columns.name') }}</th>
                <th width="10%" class="text-center text-gray-800">{{ trans('package-core::base.list.columns.ordering') }}</th>
                <th width="15%" class="text-center text-gray-800">{{ trans('package-core::base.list.columns.translations') }}</th>
                <th width="25%" class="text-center text-gray-800">{{ trans('package-core::base.list.columns.action') }}</th>
            </tr>
        </thead>
    </x-list-view>

    <div class="mt-10">
        <h6>{{ $description ?? '' }}</h6>
    </div>
@endsection
