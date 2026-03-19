<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\RoutingSlip;


class PrintController extends Controller
{
    public function printLogbook()
    {
            $role   = auth()->user()->role;

        return view('print.printLogbook', [
    'users'  => User::orderBy('fname')->get(),
    'groups' => Group::orderBy('group_name')->get(),
    'role'   => $role
]);
    }


    
public function logbookPdf(Request $request)
{
    $query = RoutingSlip::query();
    $users = User::select('id', 'fname', 'lname')->get()->keyBy('id');

    // ✅ Control number range
    if ($request->filled('ctrl_from') && $request->filled('ctrl_to')) {
        
$query->whereBetween('rslip_id', [
    $request->ctrl_from,
    $request->ctrl_to
]);

    }

    // ✅ Routing status
    if ($request->filled('routing_status')) {
        $query->where('routing_status', $request->routing_status);
    }

    $routingSlips = $query
        ->orderBy('op_ctrl')
        ->get();

    
$pdf = Pdf::loadView('print.logbookPdf', compact(
    'routingSlips',
    'users'
))->setPaper('legal', 'landscape');


    return $pdf->stream('logbook.pdf');
}


}
