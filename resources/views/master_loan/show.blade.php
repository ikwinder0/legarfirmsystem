@extends(backpack_view('blank'))

@section('content')
<h3 class="d-flex" style="max-width: 800px">
    <span> Quotation (Master Title Loan) </span>
    <button class="btn btn-secondary ml-auto" onclick="print()">Print</button>
</h3>
<div class="card" style="max-width: 800px">
    <table class="table tbl-master-title-loan">
        <tr>
            <td style="max-width: 150px"><strong>Purchaser : </strong></td>
            <td>{{ $entry->purchaser }}</td>
            <td class="text-right"></td>
            <td class="text-right"></td>
        </tr>
        <tr>
            <td><strong>Property : </strong></td>
            <td>{{ $entry->property }}</td>
            <td class="text-right"></td>
            <td class="text-right"></td>
        </tr>
        <tr>
            <td><strong>Loan Amount : </strong></td>
            <td>RM {{ number_format($entry->loan_amount,2) }}</td>
            <td class="text-right"></td>
            <td class="text-right"></td>
        </tr>
    </table>
    <table class="table sm:table-sm table-responsive-lg tbl-master-title-loan">

        {{-- --------------------------------------------------- --}}
        <tr>
            <td colspan="4">
                BEING OUR PROFESSIONAL CHARGES for attending to your instruction in respect of the above-captioned
                matter, including perusing, advising, drafting, preparing and attending to the preparation and execution
                of document/s and cause papers, attending to correspondences, telephone calls and meeting in relation to
                the above-captioned matter and attending to others matters incidental thereto but not specifically set
                out herein pursuant to the Solicitor’s Remuneration Order 2005.
            </td>
        </tr>
        <tr>
            <th scope="col" class="text-center" width="52%"><u>PARTICULARS</u></th>
            <th scope="col" class="text-right" width="18%"><u>Amount (RM)</u></th>
            <th scope="col" class="text-right" width="10%"><u>6% SST</u></th>
            <th scope="col" class="text-right" width="18%"><u>TOTAL</u></th>
        </tr>
        <tr>
            <td><strong><u>Professional Charges :-</u></strong></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
        </tr>
        @foreach ($entry->pro_charge_items() as $i=>$item)
        <tr>
            <td>{{$i+1}}) {{$item->label}} {{ $item->type_of_price == "pax" ? "( $entry->pax pax )" : "" }}</td>
            <td class="text-right">
                @if ($item->type_of_price == 'fix_price')
                {{ number_format($item->price,2) }}
                @else
                @if (isset($entry[$item->name]))
                {{ number_format($entry[$item->name],2) }}
                @else
                {{ number_format(\App\Models\AddedSectionItem::where([
                ['item_id', $entry->id],
                ['name', $item->name]
                ])->first()->amount ?? 0,2) ?? 0.00 }}
                @endif
                @endif
            </td>
            <td class="text-right"></td>
            <td class="text-right">
                @if ($item->type_of_price == 'fix_price')
                {{ number_format($item->price,2) }}
                @else
                @if (isset($entry[$item->name]))
                {{ number_format($entry[$item->name],2) }}
                @else
                {{ number_format(\App\Models\AddedSectionItem::where([
                ['item_id', $entry->id],
                ['name', $item->name]
                ])->first()->amount ?? 0,2) ?? 0.00 }}
                @endif
                @endif
            </td>
        </tr>
        @endforeach
        <tr>
            <th scope="col" class="text-right">Sub Total Professional Charges :</th>
            <th scope="col" class="text-right">{{ number_format($entry->totalProfessionalCharge(),2) }}</th>
            <th scope="col" class="text-right">0.00</th>
            <th scope="col" class="text-right">{{ number_format($entry->totalProfessionalCharge(),2) }}</th>
        </tr>
        {{-- --------------------------------------------------- --}}
        <tr>
            <td><strong><u>Reimbursements :-</u></strong></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
        </tr>
        @foreach ($entry->reimbursement_items() as $i=>$item)
        <tr>
            <td>{{$i+1}}) {{$item->label}} {{ $item->type_of_price == "pax" ? "( $entry->pax pax )" : "" }}</td>
            <td class="text-right">
                @if ($item->type_of_price == 'fix_price')
                {{ number_format($item->price,2) }}
                @else
                @if (isset($entry[$item->name]))
                {{ number_format($entry[$item->name],2) }}
                @else
                {{ number_format(\App\Models\AddedSectionItem::where([
                ['item_id', $entry->id],
                ['name', $item->name]
                ])->first()->amount ?? 0,2) ?? 0.00 }}
                @endif
                @endif
            </td>
            <td class="text-right"></td>
            <td class="text-right">
                @if ($item->type_of_price == 'fix_price')
                {{ number_format($item->price,2) }}
                @else
                @if (isset($entry[$item->name]))
                {{ number_format($entry[$item->name],2) }}
                @else
                {{ number_format(\App\Models\AddedSectionItem::where([
                ['item_id', $entry->id],
                ['name', $item->name]
                ])->first()->amount ?? 0,2) ?? 0.00 }}
                @endif
                @endif
            </td>
        </tr>
        @endforeach
        <tr>
            <th scope="col" class="text-right">Total Disbursements :</th>
            <th scope="col" class="text-right">{{ number_format($entry->totalReimbursements(),2) }}</th>
            <th scope="col" class="text-right">0.00</th>
            <th scope="col" class="text-right">{{ number_format($entry->totalReimbursements(),2) }}</th>
        </tr>
        {{-- --------------------------------------------------- --}}
        <tr>
            <td><strong><u>Disbursements :-</u></strong></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
            <td class="text-right"></td>
        </tr>
        @foreach ($entry->disbursement_items() as $i=>$item)
        <tr>
            <td>{{$i+1}}) {{$item->label}} {{ $item->type_of_price == "pax" ? "( $entry->pax pax )" : "" }}</td>
            <td class="text-right">
                @if ($item->type_of_price == 'fix_price')
                {{ number_format($item->price,2) }}
                @else
                @if (isset($entry[$item->name]))
                {{ number_format($entry[$item->name],2) }}
                @else
                {{ number_format(\App\Models\AddedSectionItem::where([
                ['item_id', $entry->id],
                ['name', $item->name]
                ])->first()->amount ?? 0,2) ?? 0.00 }}
                @endif
                @endif
            </td>
            <td class="text-right"></td>
            <td class="text-right">
                @if ($item->type_of_price == 'fix_price')
                {{ number_format($item->price,2) }}
                @else
                @if (isset($entry[$item->name]))
                {{ number_format($entry[$item->name],2) }}
                @else
                {{ number_format(\App\Models\AddedSectionItem::where([
                ['item_id', $entry->id],
                ['name', $item->name]
                ])->first()->amount ?? 0,2) ?? 0.00 }}
                @endif
                @endif
            </td>
        </tr>
        @endforeach
        <tr>
            <th scope="col" class="text-right">Total Disbursements :</th>
            <th scope="col" class="text-right">{{ number_format($entry->totalDisbursements(),2) }}</th>
            <th scope="col" class="text-right">0.00</th>
            <th scope="col" class="text-right">{{ number_format($entry->totalDisbursements(),2) }}</th>
        </tr>
        <tr>
            <th scope="col" class="text-right">Subtotal :</th>
            <th scope="col" class="text-right">{{ number_format($entry->subtotal(),2) }}</th>
            <th scope="col" class="text-right">0.00</th>
            <th scope="col"></th>
        </tr>
        <tr>
            <th scope="col" colspan="3" class="text-right">Total Payable Amount (MYR) :</th>
            <th scope="col" class="text-right">{{ number_format($entry->subtotal(),2) }}</th>
        </tr>
        {{-- --------------------------------------------------- --}}
    </table>
</div>
@endsection