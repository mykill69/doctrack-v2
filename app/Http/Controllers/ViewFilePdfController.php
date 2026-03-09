<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\User;
use App\Models\Group;
use App\Models\RoutingSlip;
use App\Models\RoutingPdf;
use App\Models\ReassignedUser;
use App\Models\Esig;

class ViewFilePdfController extends Controller
{
    public function viewRouteSlip($slipId)
{
    $slip = RoutingSlip::where('id', $slipId)->firstOrFail();

    return view('pdf.viewRouteSlip', [
        'slip'        => $slip,
        'users'       => User::orderBy('fname')->get(),
        'groups'      => Group::orderBy('group_name')->get(),
    ]);
}

// march 6, 2026 added the id 56 to view all pdf slip

// public function pdfSlip($slipId)
// {
//     $user = auth()->user();
//     $userId = $user->id;

//     // Get the routing slip
//     $slip = RoutingSlip::findOrFail($slipId);

//     // Get only the RoutingPdf row relevant to the current user
//     $routingPdf = RoutingPdf::where('routing_slip_id', $slip->id)
//         ->where(function($query) use ($userId) {
//             $query->where('creator_id', $userId)
//                   ->orWhere('reassigned_to', $userId)
//                   ->orWhereRaw("FIND_IN_SET(?, routed_users)", [$userId]);
//         })
//         ->orderBy('id', 'asc')
//         ->first();

//     // Get reassigned users for this slip
//     $reassignedUsers = ReassignedUser::where('slip_id', $slip->id)->get();

//     $departments = collect();
//     $fromType   = null;
//     $toText     = '';
//     $dateText   = '';

//     if ($routingPdf) {
//         $routedUserIds = collect(explode(',', $routingPdf->routed_users))
//             ->map(fn($id) => trim($id))
//             ->filter()
//             ->toArray();

//         $isAdminOrRecords = in_array($user->role, ['Administrator', 'records_officer']);
//         $isRoutedUser     = in_array((string)$user->id, $routedUserIds);

//         $reassignedEntry = $reassignedUsers->first(fn($ru) => $ru->reassigned_id == $user->id);
//         $isReassignedUser = !is_null($reassignedEntry);

//         // Determine FROM type
//         if ($isAdminOrRecords || $isRoutedUser) {
//             $departments->push([
//                 'type'  => 'pres',
//                 'value' => $routingPdf->pres_dept
//             ]);
//             $fromType = 'pres';
//         } elseif ($isReassignedUser) {
//             $creator = User::find($reassignedEntry->creator_id);
//             if ($creator) {
//                 $departments->push([
//                     'type'  => 'reassigned',
//                     'value' => $creator->department
//                 ]);
//                 $fromType = 'reassigned';
//             }
//         }

//         // TO + DATE
//         if ($fromType === 'pres') {
//             $users = User::whereIn('id', $routedUserIds)->get();
//             $toText = $users->map(fn($u) => $u->fname . ' ' . $u->lname)->implode(', ');
//             $dateText = $routingPdf->created_at->format('m/d/Y');
//         } elseif ($fromType === 'reassigned' && $reassignedEntry) {
//             $assignedUser = User::find($reassignedEntry->reassigned_id);
//             $toText = $assignedUser ? $assignedUser->fname . ' ' . $assignedUser->lname : '';
//             $dateText = $reassignedEntry->created_at->format('m/d/Y');
//         }
//     }

//     // Remove duplicate departments
//     $departments = $departments->unique(fn($item) => $item['type'] . $item['value']);

//     $signatoryName = 'ALADINO C. MORACA, Ph.D.';
//     $signatoryTitle = 'SUC President';
//     $isReassignedFrom = false;

//     if ($routingPdf) {
//         if ($fromType === 'reassigned' && $reassignedEntry) {
//             $creator = User::find($reassignedEntry->creator_id);
//             if ($creator) {
//                 $signatoryName = $creator->fname . ' ' . $creator->lname;
//                 $signatoryTitle = $creator->department;
//                 $isReassignedFrom = true;
//             }
//         }
//     }

//         $signatoryEsig = null;
//         $esigUserId = null;

//     if ($routingPdf) {

//         // REASSIGNED → creator
//         if ($isReassignedFrom && isset($creator)) {
//             $esigUserId = $creator->id;
//         }

//         // PRESIDENT → fixed ID
//         if (!$isReassignedFrom) {
//             $esigUserId = 38;
//         }
//     }

//     if ($esigUserId) {
//         $signatoryEsig = Esig::where('user_id', $esigUserId)->first();

//         if ($signatoryEsig && $signatoryEsig->esig_file) {
//             $signatoryEsig->esig_path =
//                 public_path('storage/esignature/' . $signatoryEsig->esig_file);
//         }
//     }

//     // Pass to PDF view
//     $pdf = Pdf::loadView('pdf.pdfSlip', compact(
//         'slip',
//         'departments',
//         'toText',
//         'dateText',
//         'routingPdf',
//         'signatoryName',
//         'signatoryTitle',
//         'isReassignedFrom',
//         'signatoryEsig'
//     ))->setPaper('A4', 'portrait');

//     return $pdf->stream('routing-slip.pdf');
// }


public function pdfSlip($slipId)
{
$user = auth()->user();
$userId = $user->id;

$slip = RoutingSlip::findOrFail($slipId);

// --- Get RoutingPdf entries ---
$routingPdfs = RoutingPdf::where('routing_slip_id', $slip->id)
    ->when($userId != 56, function($query) use ($userId) {
        $query->where(function($q) use ($userId) {
            $q->where('creator_id', $userId)
              ->orWhere('reassigned_to', $userId)
              ->orWhereRaw("FIND_IN_SET(?, routed_users)", [$userId]);
        });
    })
    ->orderBy('id', 'asc')
    ->get();

// --- Get ReassignedUser entries ---
$reassignedUsers = ReassignedUser::where('slip_id', $slip->id)
    ->when($userId != 56, fn($q) => $q->where('reassigned_id', $userId))
    ->orderBy('id', 'asc')
    ->get();

// --- Prepare PDF data ---
$pdfData = collect();

// Track already added entries to avoid duplicates
$addedEntries = [];

// --- Loop RoutingPdf entries ---
foreach ($routingPdfs as $routingPdf) {
    $key = 'pdf_' . $routingPdf->id;
    if (in_array($key, $addedEntries)) continue;
    $addedEntries[] = $key;

    $departments = collect();
    $fromType = null;
    $toText = '';
    $dateText = '';

    $routedUserIds = collect(explode(',', $routingPdf->routed_users))
        ->map(fn($id) => trim($id))
        ->filter()
        ->toArray();

    $isAdminOrRecords = in_array($user->role, ['Administrator', 'records_officer']);
    $isRoutedUser = in_array((string)$user->id, $routedUserIds);

    $reassignedEntry = $reassignedUsers->first(fn($ru) => $ru->reassigned_id == $user->id);
    $isReassignedUser = !is_null($reassignedEntry);

    if ($userId == 56 || $isAdminOrRecords || $isRoutedUser) {
        $departments->push(['type' => 'pres', 'value' => $routingPdf->pres_dept]);
        $fromType = 'pres';
    } elseif ($isReassignedUser) {
        $creator = User::find($reassignedEntry->creator_id);
        if ($creator) {
            $departments->push(['type' => 'reassigned', 'value' => $creator->department]);
            $fromType = 'reassigned';
        }
    }

    // TO + DATE
    if ($fromType === 'pres') {
        $users = User::whereIn('id', $routedUserIds)->get();
        $toText = $users->map(fn($u) => $u->fname . ' ' . $u->lname)->implode(', ');
        $dateText = $routingPdf->created_at->format('m/d/Y');
    } elseif ($fromType === 'reassigned' && $reassignedEntry) {
        $assignedUser = User::find($reassignedEntry->reassigned_id);
        $toText = $assignedUser ? $assignedUser->fname . ' ' . $assignedUser->lname : '';
        $dateText = $reassignedEntry->created_at->format('m/d/Y');
    }

    $departments = $departments->unique(fn($item) => $item['type'] . $item['value']);

    // Signatory
    $signatoryName = 'ALADINO C. MORACA, Ph.D.';
    $signatoryTitle = 'SUC President';
    $isReassignedFrom = false;

    if ($fromType === 'reassigned' && $reassignedEntry) {
        $creator = User::find($reassignedEntry->creator_id);
        if ($creator) {
            $signatoryName = $creator->fname . ' ' . $creator->lname;
            $signatoryTitle = $creator->department;
            $isReassignedFrom = true;
        }
    }

    $esigUserId = $isReassignedFrom && isset($creator) ? $creator->id : 38;
    $signatoryEsig = Esig::where('user_id', $esigUserId)->first();
    if ($signatoryEsig && $signatoryEsig->esig_file) {
        $signatoryEsig->esig_path = public_path('storage/esignature/' . $signatoryEsig->esig_file);
    }

    $pdfData->push([
        'routingPdf' => $routingPdf,
        'departments' => $departments,
        'toText' => $toText,
        'dateText' => $dateText,
        'signatoryName' => $signatoryName,
        'signatoryTitle' => $signatoryTitle,
        'signatoryEsig' => $signatoryEsig,
    ]);
}

// --- Loop ReassignedUser entries (only for 56, skip if already in RoutingPdf) ---
if ($userId == 56) {
    foreach ($reassignedUsers as $reassigned) {
        // Skip if this ReassignedUser is already represented in a RoutingPdf
        $alreadyInPdf = $routingPdfs->contains(function($pdf) use ($reassigned) {
            return $pdf->creator_id == $reassigned->creator_id
                && $pdf->reassigned_to == $reassigned->reassigned_id;
        });
        if ($alreadyInPdf) continue;

        $key = 'reassign_' . $reassigned->id;
        if (in_array($key, $addedEntries)) continue;
        $addedEntries[] = $key;

        $creator = $reassigned->creator;
        $assignedUser = $reassigned->reassignedUser;

        $departments = collect([['type' => 'reassigned', 'value' => $creator->department]]);
        $toText = $assignedUser ? $assignedUser->fname . ' ' . $assignedUser->lname : '';
        $dateText = $reassigned->created_at->format('m/d/Y');

        $signatoryName = $creator->fname . ' ' . $creator->lname;
        $signatoryTitle = $creator->department;
        $esigUserId = $creator->id;
        $signatoryEsig = Esig::where('user_id', $esigUserId)->first();
        if ($signatoryEsig && $signatoryEsig->esig_file) {
            $signatoryEsig->esig_path = public_path('storage/esignature/' . $signatoryEsig->esig_file);
        }

        $pdfData->push([
            'routingPdf' => null,
            'departments' => $departments,
            'toText' => $toText,
            'dateText' => $dateText,
            'signatoryName' => $signatoryName,
            'signatoryTitle' => $signatoryTitle,
            'signatoryEsig' => $signatoryEsig,
        ]);
    }
}

// --- Generate PDF ---
$pdf = Pdf::loadView('pdf.pdfSlip', [
    'slip' => $slip,
    'pdfData' => $pdfData
])->setPaper('A4', 'portrait');

return $pdf->stream('routing-slip.pdf');
}



public function viewistListPres()
{
    $slip = RoutingSlip::where('transaction_type', 1)
        ->whereRaw("
            (LENGTH(routed_users) - LENGTH(REPLACE(routed_users, ',', '')) + 1) >= 3
        ")
        ->orderBy('date_received', 'desc')
        ->get();

    return view('pdf.dist_list_route', [
        'slip'   => $slip,
        'users'  => User::orderBy('fname')->get(),
        'groups' => Group::orderBy('group_name')->get(),
    ]);
}

public function distListPresPdf($id)
{
    $slip = RoutingSlip::findOrFail($id);

    $pdf = Pdf::loadView('pdf.view_dist_list_pdf', [
        'slip' => $slip,
    ]);

    return $pdf->stream('distribution-list-' . $slip->rslip_id . '.pdf');
}

public function viewDirectList()
{
    $slip = RoutingSlip::where('transaction_type', 2)
        ->whereRaw("
            (LENGTH(routed_users) - LENGTH(REPLACE(routed_users, ',', '')) + 1) >= 3
        ")
        ->orderBy('date_received', 'desc')
        ->get();

    return view('pdf.dist_list_direct_personnel', [
        'slip'   => $slip,
        'users'  => User::orderBy('fname')->get(),
        'groups' => Group::orderBy('group_name')->get(),
    ]);
}

public function distListDrirectPdf($id)
{
    $slip = RoutingSlip::findOrFail($id);

    $pdf = Pdf::loadView('pdf.view_direct_personnel', [
        'slip' => $slip,
    ]);

    return $pdf->stream('distribution-list-' . $slip->rslip_id . '.pdf');
}


}
