@extends(backpack_view('blank'))

@section('before_styles')
<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
<script src="//code.jquery.com/jquery-1.12.4.js"></script>
<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<style>

</style>
@endsection

@section('content')
<h2 class="d-flex" style="max-width: 800px">
    <span> Quotation (Master Title Loan) </span>
</h2>
<div class="card" style="max-width: 800px">
    <table class="table sm:table-sm table-responsive-lg" id="showReportTable">
        <tbody>
            <tr class="static">
                <th scope="col" class="text-center" width="52%"><u>PARTICULARS</u></th>
                <th width="20%"></th>
            </tr>
            <tr class="static">
                <td><strong><u>Professional Charges :-</u></strong></td>
                <td>
                    <a href="{{ route('calculator-item.create', ["section" => "professional_charges", "cid" => 3]) }}" class="btn btn-primary">Add</a>
                </td>
            </tr>

            @php
            $start = 0;
            $items = \App\Models\MasterTitleLoan::pro_charge_items();
            @endphp
            @foreach ($items as $i=>$item)
            <tr data-id="{{ $item->id }}" data-pos="{{ $item->pos }}" data-sub="2" data-section="professional_charges">
                <td>{{$i+1}}) {{$item->label}}</td>
                <td>
                    <a href="{{ route('calculator-item.edit', ["id"=> $item->id])}}" class="btn btn-info">Edit</a>
                    <a href="javascript:void(0)" class="btn btn-danger" onclick="deleteEntry({{ $item->id }})">Delete</a>
                </td>
            </tr>
            @endforeach

            <tr class="static">
                <th scope="col" class="text-right">Sub Total Professional Charges :</th>
                <th></th>
            </tr>
            {{-- --------------------------------------------------- --}}
            <tr class="static">
                <td><strong><u>Reimbursements :-</u></strong></td>
                <td>
                    <a href="{{ route('calculator-item.create', ["section" => "reimbursements", "cid" => 3]) }}" class="btn btn-primary">Add</a>
                </td>
            </tr>

            @php
            $start = $items->last()->pos;
            $items = \App\Models\MasterTitleLoan::reimbursement_items();
            @endphp
            @foreach ($items as $i=>$item)
            <tr data-id="{{ $item->id }}" data-pos="{{ $item->pos }}" data-sub="4" data-section="reimbursements">
                <td>{{$i+1}}) {{$item->label}}</td>
                <td>
                    <a href="{{ route('calculator-item.edit', ["id"=> $item->id])}}" class="btn btn-info">Edit</a>
                    <a href="javascript:void(0)" class="btn btn-danger" onclick="deleteEntry({{ $item->id }})">Delete</a>
                </td>
            </tr>
            @endforeach

            <tr class="static">
                <th scope="col" class="text-right">Total Disbursements :</th>
                <th></th>
            </tr>
            {{-- --------------------------------------------------- --}}
            <tr class="static">
                <td><strong><u>Disbursements :-</u></strong></td>
                <td>
                    <a href="{{ route('calculator-item.create', ["section" => "disbursements", "cid" => 3]) }}" class="btn btn-primary">Add</a>
                </td>
            </tr>

            @php
            $start = $items->last()->pos;
            $items = \App\Models\MasterTitleLoan::disbursement_items();
            @endphp
            @foreach ($items as $i=>$item)
            <tr data-id="{{ $item->id }}" data-pos="{{ $item->pos }}" data-sub="6" data-section="disbursements">
                <td>{{$i+1}}) {{$item->label}}</td>
                <td>
                    <a href="{{ route('calculator-item.edit', ["id"=> $item->id])}}" class="btn btn-info">Edit</a>
                    <a href="javascript:void(0)" class="btn btn-danger" onclick="deleteEntry({{ $item->id }})">Delete</a>
                </td>
            </tr>
            @endforeach

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
                $.ajax({
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
                            $('.modal').modal('hide');
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

    $("#showReportTable tbody").sortable({
        items: '>tr:not(.static)'
        , cursor: 'move'
        , update: function(event, ui) {
            const old_pos = Number(ui.item[0].dataset.pos)
            let sub_pos = Number(ui.item[0].dataset.sub)

            if (ui.item.next().data('sub')) {
                ui.item[0].dataset.sub = sub_pos = ui.item.next().data('sub')
            }
            if (ui.item.prev().data('sub')) {
                ui.item[0].dataset.sub = sub_pos = ui.item.prev().data('sub')
            }

            if (ui.item.next().data('section')) {
                ui.item[0].dataset.section = ui.item.next().data('section')
            }
            if (ui.item.prev().data('section')) {
                ui.item[0].dataset.section = ui.item.prev().data('section')
            }


            const new_pos = ui.item.index() + 1 - sub_pos;

            if (old_pos < new_pos) {
                $('[data-id]').each(function(index, value) {
                    const pos = value.dataset.pos

                    if (pos > old_pos && pos <= new_pos) {
                        value.dataset.pos = Number(value.dataset.pos) - 1;
                    }
                })

            } else {
                $('[data-id]').each(function(index, value) {
                    const pos = value.dataset.pos

                    if (pos >= new_pos && pos < old_pos) {
                        value.dataset.pos = Number(value.dataset.pos) + 1;
                    }
                })
            }

            ui.item[0].dataset.pos = new_pos
        }
    });

    $("#saveBtn").click(function() {
        const arr = []
        $('[data-id]').each(function(index, value) {
            arr.push({
                id: value.dataset.id
                , pos: value.dataset.pos
                , section: value.dataset.section
            })
        })

        $.ajax({
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
