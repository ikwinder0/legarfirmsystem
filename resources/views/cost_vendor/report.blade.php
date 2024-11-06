@extends(backpack_view('blank'))

@section('before_styles')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
@endsection

@section('content')
<h2 class="d-flex" style="max-width: 800px">
    <span> Quotation (Cost of Assist Vendor) </span>
</h2>
<div class="card" style="max-width: 800px">
    <table class="table sm:table-sm table-responsive-lg" id="showReportTable">
        <tbody>
            <tr class="static">
                <th scope="col" class="text-center" width="52%"><u>PARTICULARS</u></th>
                <th width="20%"></th>
            </tr>
            <tr class="static h-pro">
                <td><strong><u>Professional Charges :-</u></strong></td>
                <td>
                    <a href="{{ route('calculator-item.create', ["section" => "professional_charges", "cid" => 4]) }}" class="btn btn-primary">Add</a>
                </td>
            </tr>

            @php
            $start = 0;
            $items = \App\Models\CostOfAssistVendor::pro_charge_items();
            @endphp

            @if (sizeof($items))
            @foreach ($items as $i=>$item)
            <tr data-id="{{ $item->id }}" data-section="professional_charges">
                <td>{{$i+1}}) {{$item->label}}</td>
                <td>
                    <a href="{{ route('calculator-item.edit', ["id"=> $item->id])}}" class="btn btn-info">Edit</a>
                    <a href="javascript:void(0)" class="btn btn-danger" onclick="deleteEntry({{ $item->id }})">Delete</a>
                </td>
            </tr>
            @endforeach
            @else
            <tr class="empty" data-section="professional_charges">
                <td></td>
                <td></td>
            </tr>
            @endif

            <tr class="static">
                <th scope="col" class="text-right">Sub Total Professional Charges :</th>
                <th></th>
            </tr>
            {{-- --------------------------------------------------- --}}
            <tr class="static h-reim">
                <td><strong><u>Reimbursements :-</u></strong></td>
                <td>
                    <a href="{{ route('calculator-item.create', ["section" => "reimbursements", "cid" => 4]) }}" class="btn btn-primary">Add</a>
                </td>
            </tr>

            @php
            $start = $items->last()->pos ?? 0;
            $items = \App\Models\CostOfAssistVendor::reimbursement_items();
            @endphp

            @if (sizeof($items))
            @foreach ($items as $i=>$item)
            <tr data-id="{{ $item->id }}" data-section="reimbursements">
                <td>{{$i+1}}) {{$item->label}}</td>
                <td>
                    <a href="{{ route('calculator-item.edit', ["id"=> $item->id])}}" class="btn btn-info">Edit</a>
                    <a href="javascript:void(0)" class="btn btn-danger" onclick="deleteEntry({{ $item->id }})">Delete</a>
                </td>
            </tr>
            @endforeach
            @else
            <tr class="empty" data-section="reimbursements">
                <td></td>
                <td></td>
            </tr>
            @endif

            <tr class="static">
                <th scope="col" class="text-right">Total Disbursements :</th>
                <th></th>
            </tr>
            {{-- --------------------------------------------------- --}}
            <tr class="static h-dis">
                <td><strong><u>Disbursements :-</u></strong></td>
                <td>
                    <a href="{{ route('calculator-item.create', ["section" => "disbursements", "cid" => 4]) }}" class="btn btn-primary">Add</a>
                </td>
            </tr>

            @php
            $start = $items->last()->pos ?? 0;
            $items = \App\Models\CostOfAssistVendor::disbursement_items();
            @endphp

            @if (sizeof($items))
            @foreach ($items as $i=>$item)
            <tr data-id="{{ $item->id }}" data-section="disbursements">
                <td>{{$i+1}}) {{$item->label}}</td>
                <td>
                    <a href="{{ route('calculator-item.edit', ["id"=> $item->id])}}" class="btn btn-info">Edit</a>
                    <a href="javascript:void(0)" class="btn btn-danger" onclick="deleteEntry({{ $item->id }})">Delete</a>
                </td>
            </tr>
            @endforeach
            @else
            <tr class="empty" data-section="disbursements">
                <td></td>
                <td></td>
            </tr>
            @endif

            <tr class="static">
                <th scope="col" class="text-right">Total Disbursements :</th>
                <th></th>
            </tr>
            <tr class="static">
            </tr>
            <tr class="static">
                <th scope="col" class="text-right">Total Payable Amount (MYR) :</th>
                <th></th>
            </tr>
        </tbody>
        {{-- --------------------------------------------------- --}}
    </table>

    <button class="btn mt-2 btn-success" id="saveBtn">Save layout</button>
</div>



@endsection

@section('after_scripts')
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<script>
    function deleteEntry(id) {
        swal({
            title: "{!! trans('backpack::base.warning') !!}"
            , text: "{!! trans('backpack::crud.delete_confirm') !!}"
            , icon: "warning"
            , buttons: ["{!! trans('backpack::crud.cancel') !!}", "{!! trans('backpack::crud.delete') !!}"]
            , dangerMode: true
        , }).then((value) => {
            if (value) {
                jQuery.ajax({
                    url: `/admin/calculator-item/${id}`
                    , type: 'DELETE'
                    , success: function(result) {
                        if (result == 1) {
                            // Show a success notification bubble
                            new Noty({
                                type: "success"
                                , text: "{!! '<strong>' . trans('backpack::crud.delete_confirmation_title') . '</strong><br>' . trans('backpack::crud.delete_confirmation_message') !!}"
                            }).show();

                            // Hide the modal, if any
                            jQuery('.modal').modal('hide');
                            location.reload();
                        } else {
                            // if the result is an array, it means 
                            // we have notification bubbles to show
                            if (result instanceof Object) {
                                // trigger one or more bubble notifications 
                                Object.entries(result).forEach(function(entry, index) {
                                    var type = entry[0];
                                    entry[1].forEach(function(message, i) {
                                        new Noty({
                                            type: type
                                            , text: message
                                        }).show();
                                    });
                                });
                            } else { // Show an error alert
                                swal({
                                    title: "{!! trans('backpack::crud.delete_confirmation_not_title') !!}"
                                    , text: "{!! trans('backpack::crud.delete_confirmation_not_message') !!}"
                                    , icon: "error"
                                    , timer: 4000
                                    , buttons: false
                                , });
                            }
                        }
                    }
                    , error: function(result) {
                        // Show an alert with the result
                        swal({
                            title: "{!! trans('backpack::crud.delete_confirmation_not_title') !!}"
                            , text: "{!! trans('backpack::crud.delete_confirmation_not_message') !!}"
                            , icon: "error"
                            , timer: 4000
                            , buttons: false
                        , });
                    }
                });
            }
        });

    }

    jQuery("#showReportTable tbody").sortable({
        items: '>tr:not(.static)'
        , cursor: 'move'
        , update: function(event, ui) {
            if (ui.item.next().data('section')) {
                ui.item[0].dataset.section = ui.item.next().data('section')
            }
            if (ui.item.prev().data('section')) {
                ui.item[0].dataset.section = ui.item.prev().data('section')
            }

            if (!jQuery('[data-section="reimbursements"]').length) {
                if (!jQuery('[data-section="reimbursements"].empty').length) {
                    jQuery(`
                        <tr class="empty" data-section="reimbursements">
                            <td></td>
                            <td></td>
                        </tr>
                    `).insertAfter('.h-reim')
                }
            } else {
                jQuery('[data-section="reimbursements"].empty').remove()
            }
            if (!jQuery('[data-section="disbursements"]').length) {
                if (!jQuery('[data-section="disbursements"].empty').length) {
                    jQuery(`
                        <tr class="empty" data-section="disbursements">
                            <td></td>
                            <td></td>
                        </tr>
                    `).insertAfter('.h-dis')
                }
            } else {
                jQuery('[data-section="disbursements"].empty').remove()
            }
            if (!jQuery('[data-section="professional_charges"]').length) {
                if (!jQuery('[data-section="professional_charges"].empty').length) {
                    jQuery(`
                        <tr class="empty" data-section="professional_charges">
                            <td></td>
                            <td></td>
                        </tr>
                    `).insertAfter('.h-pro')
                }
            } else {
                jQuery('[data-section="professional_charges"].empty').remove()
            }
        }
    });

    jQuery("#saveBtn").click(function() {
        const arr = []

        $('[data-id]').each(function(index, value) {
            arr.push({
                id: value.dataset.id
                , pos: index + 1
                , section: value.dataset.section
            })
        })

        jQuery.ajax({
            url: '/api/v1/calculator/2'
            , type: 'PUT'
            , data: {
                items: arr
            }
            , success: function() {
                location.reload();
            }
        })
    })

</script>
@endsection
