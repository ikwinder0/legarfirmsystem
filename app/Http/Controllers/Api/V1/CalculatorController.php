<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\CalculatorItem;
use Illuminate\Http\Request;

class CalculatorController extends Controller
{
    public function update(Request $request, $cid)
    {
        $items = $request->items;

        foreach ($items as $item) {
            CalculatorItem::find($item["id"])->update([
                "pos" => $item["pos"],
                "section" => $item["section"]
            ]);
        }

        return response()->json([
            "status" => "Success",
            "message" => "Item successfully updated."
        ]);
    }
}
