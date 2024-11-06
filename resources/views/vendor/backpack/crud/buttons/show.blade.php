@php
    $routeName = request()->route()->getName();
@endphp
@if ($crud->hasAccess('show'))
    @if ($routeName === 'purchase.search' || $routeName === 'loan.search' || $routeName === 'master-title-loan.search' || $routeName === 'cost-of-assist-vendor.search')
        <a
        href="{{ url($crud->route . '/' . $entry->getKey()) . '/show' }}" class="btn btn-sm btn-link"
        data-button-type="delete"><i class="la la-eye"></i>
        Quotation</a>
    @else
    <a
        href="{{ url($crud->route . '/' . $entry->getKey()) . '/show'}}" class="btn btn-sm btn-link"
        data-button-type="show"><i class="la la-eye"></i>
        {{ trans('backpack::crud.preview') }}</a>
    @endif  
@endif