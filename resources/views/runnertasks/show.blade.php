@extends(backpack_view('blank'))

@php
  $defaultBreadcrumbs = [
    trans('backpack::crud.admin') => url(config('backpack.base.route_prefix'), 'dashboard'),
    $crud->entity_name_plural => url($crud->route),
    trans('backpack::crud.preview') => false,
  ];

  // if breadcrumbs aren't defined in the CrudController, use the default breadcrumbs
  $breadcrumbs = $breadcrumbs ?? $defaultBreadcrumbs;
@endphp



@section('content')
<div class="row">
	<div class="col-md-12">

	<!-- Default box -->
	  <div class="">
	    <div class="card no-padding no-border">
			<table class="table table-striped mb-0" style="">
		        <tbody>
		        @foreach ($crud->columns() as $column)
		            <tr>
		                <td>
		                    <strong>{!! $column['label'] !!}:</strong>
		                </td>
                        <td>
                        	@php
                        		// create a list of paths to column blade views
                        		// including the configured view_namespaces
                        		$columnPaths = array_map(function($item) use ($column) {
                        			return $item.'.'.$column['type'];
                        		}, config('backpack.crud.view_namespaces.columns'));

                        		// but always fall back to the stock 'text' column
                        		// if a view doesn't exist
                        		if (!in_array('crud::columns.text', $columnPaths)) {
                        			$columnPaths[] = 'crud::columns.text';
                        		}
								
                        	@endphp
							@includeFirst($columnPaths)
                        </td>
		            </tr>
		        @endforeach
				
				
				@if ($crud->buttons()->where('stack', 'line')->count())
					<tr>
						<td><strong>{{ trans('backpack::crud.actions') }}</strong></td>
						<td>
							@include('crud::inc.button_stack', ['stack' => 'line'])
						</td>
					</tr>
				@endif
		        </tbody>
			</table>
	    </div><!-- /.box-body -->
	  </div><!-- /.box -->

	</div>
</div>
@endsection


