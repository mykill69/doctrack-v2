<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Group;
use App\Models\RoutingSlip;
use App\Models\LogsTrans;
use App\Models\Office;
use App\Models\SystemLog;
use App\Models\LogsRoute;
use App\Models\ManagementLog;

class ArchiveController extends Controller
{
    public function archiveLogbook(Request $request)
{
    // Base query: exclude purged
   $query = RoutingSlip::where(function ($q) {
    $q->whereNull('validity_status')    // include rows where validity_status is NULL
      ->orWhere('validity_status', 0); // or 0
});
    // Search by validity (text or year)
    if ($request->filled('search')) {
    $query->where('validity', 'LIKE', '%' . $request->search . '%');
}

if ($request->filled('validity')) {
    $query->where('validity', $request->validity);
}

$routingSlips = $query->orderBy('date_received', 'desc')->get();

    $offices = Office::orderBy('office_name')->get();
    $groups  = Group::orderBy('group_name')->get();
    $users   = User::orderBy('fname')->get();

    // AJAX request: return table rows only
    if ($request->ajax()) {
        return view('archived.partials.archive_logbook_rows', compact('routingSlips'))->render();
    }

    return view('archived.archive_logbook', compact(
        'routingSlips',
        'offices',
        'groups',
        'users'
    ));
}

public function archivedHistory(Request $request)
{
    // Base query: only rows where validity_status is NOT NULL and NOT 1
    $query = RoutingSlip::whereNotNull('validity_status')
                        ->where('validity_status', '=', 1);

    // Search by validity (text or year)
    if ($request->filled('search')) {
        $query->where('validity', 'LIKE', '%' . $request->search . '%');
    }

    // Filter by year dropdown (if used)
    if ($request->filled('validity')) {
        $query->where('validity', $request->validity);
    }

    // Fetch results
    $routingSlips = $query->orderBy('date_received', 'desc')->get();

    $offices = Office::orderBy('office_name')->get();
    $groups  = Group::orderBy('group_name')->get();
    $users   = User::orderBy('fname')->get();

    // AJAX request: return table rows only
    if ($request->ajax()) {
        return view('archived.partials.archive_logbook_rows', compact('routingSlips'))->render();
    }

    return view('archived.archived_history', compact(
        'routingSlips',
        'offices',
        'groups',
        'users'
    ));
}

// public function archiveByYear(Request $request)
// {
//     $request->validate([
//         'validity' => 'required|digits:4',
//     ]);

//     RoutingSlip::where('validity', $request->validity)
//         ->update([
//             'validity_status' => 1,
//         ]);

//     return redirect()
//         ->route('archiveLogbook', ['validity' => $request->validity])
//         ->with('success', 'Logbooks archived successfully.');
// }

public function archiveByYear(Request $request)
{
    $request->validate([
        'validity' => 'required|digits:4',
    ]);

    // Fetch all slips for this year that are not yet archived
    $slipsToArchive = RoutingSlip::where('validity', $request->validity)
                        ->where(function ($q) {
                            $q->whereNull('validity_status')
                              ->orWhere('validity_status', 0);
                        })
                        ->get();

    foreach ($slipsToArchive as $slip) {

        // Archive the slip
        $slip->update([
            'validity_status' => 1,
        ]);

        // Prepare routed users IDs for log
        $mergedUserIds = $slip->routed_users ? explode(',', $slip->routed_users) : [];

        // Extract file name for log
        $fileName = $slip->file ?? null;

        // Create log entry
        \App\Models\LogsRoute::create([
            'slip_id'      => $slip->id,
            'rslip_id'     => $slip->rslip_id,
            'log_creator'  => auth()->id(),
            'log_action'   => 'Archived',
            'file'         => $fileName,
            'routed_users' => implode(',', $mergedUserIds),
        ]);
    }

    return redirect()
        ->route('archiveLogbook', ['validity' => $request->validity])
        ->with('success', 'Logbooks archived and logged successfully.');
}

}
