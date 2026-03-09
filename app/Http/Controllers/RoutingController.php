<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RoutingSlip;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Group;
use App\Models\LogsRoute;
use App\Models\LogsTrans;
use App\Models\ReassignedUser;
use App\Models\RoutingPdf;
use Illuminate\Support\Facades\Mail;
use App\Mail\DocumentRoutedNotification;



class RoutingController extends Controller
{
    public function addRoutingPres(Request $request)
{
    $request->validate([
        'control_number' => 'required|string',
        'date_received'  => 'required|date',
        'source'         => 'required|string',
        'subject'        => 'required|string',
        'file'           => 'required|file',
        'validity'       => 'required|digits:4',
        'transaction_type' => 'required|integer',
    ]);


    $filename = null;

    // Handle file upload with date + time
    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $filename = now()->format('Y-m-d_H-i-s') . '_' . $originalName;

        // store in public/documents for accessible link
        $file->storeAs('public/documents', $filename);
    }


    RoutingSlip::create([
        'rslip_id'       => $request->control_number,
        'creator_id'     => auth()->id(),
        'source'         => $request->source,
        'subject'        => $request->subject,
        'file'           => $filename,
        'routing_status' => 1,
        'date_received'  => $request->date_received,
        'validity'       => $request->validity,
        'transaction_type' => $request->transaction_type,
    ]);

    

    return back()->with('success', 'President transaction created successfully.');
}


public function addRoutingPersonnel(Request $request)
{
    $request->validate([
        'control_number' => 'required|string',
        'date_received'  => 'required|date',
        'source'         => 'required|string',
        'subject'        => 'required|string',
        'routed_users'   => 'required|array',
        'file'           => 'required|file',
        'validity'       => 'required|digits:4',
    ]);

    // ================= FILE UPLOAD =================
    $filename = null;

    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $filename = now()->format('Y-m-d_H-i-s') . '_' . $file->getClientOriginalName();
        $file->storeAs('public/documents', $filename);
    }

    // ================= ROUTED USERS =================
    $userIds = array_filter($request->routed_users, fn ($val) => is_numeric($val));
    $routedUsers = implode(',', $userIds);

    // ================= CREATE ROUTING SLIP =================
    $slip = RoutingSlip::create([
        'rslip_id'         => $request->control_number,
        'creator_id'       => auth()->id(),
        'source'           => $request->source,
        'subject'          => $request->subject,
        'routed_users'     => $routedUsers,
        'file'             => $filename,
        'routing_status'   => 3,
        'date_received'    => $request->date_received,
        'validity'         => $request->validity,
        'transaction_type' => $request->transaction_type,
    ]);

    // ================= LOGS_ROUTE (ONE RECORD) =================
    LogsRoute::create([
        'slip_id'      => $slip->id,
        'rslip_id'     => $slip->rslip_id,
        'log_creator'  => auth()->id(),
        'log_action'   => 'Personnel routed document',
        'file'         => $slip->file,
        'routed_users' => $routedUsers,
        'transaction_type' => $slip->transaction_type,
    ]);

    // ================= LOGS_TRANS (ONE PER USER) =================
    foreach ($userIds as $userId) {
        LogsTrans::create([
            'slip_id'        => $slip->id,
            'rslip_id'       => $slip->rslip_id,
            'creator_id'     => auth()->id(),
            'source'         => $slip->source,
            'subject'        => $slip->subject,
            'trans_remarks'  => null,
            'other_remarks'  => null,
            'ass_comment'    => null,
            'r_users'        => $userId,     // ✅ ONE USER PER ROW
            'reassigned_to'  => null,
            'file'           => $slip->file,
            'purge_status'           => null,
            'trans_status'   => 1,            // ✅ PERSONNEL STATUS
            'date_received'  => $slip->date_received,
             'transaction_type' => $slip->transaction_type,
        ]);
    }

    return back()->with('success', 'Personnel transaction created successfully.');
}


public function editRoutingPres($id)
    {
        $slip  = RoutingSlip::findOrFail($id);
        $users = User::all();
        $groups = Group::all();

        return view('pages.edit_routing_pres', compact('slip', 'users', 'groups'));
    }

  public function updateRoutingPres(Request $request, $id)
{
    $slip = RoutingSlip::findOrFail($id);

    // Validate only the fields you're updating
    $request->validate([
        'op_ctrl'        => 'required|string',
        'set_users_to'   => 'required|array',
        'trans_remarks'  => 'required|string',
        'set_users_to.*' => 'exists:users,id',
        'file'           => 'nullable|file',
    ]);

    // Keep existing file by default
    $filename = $slip->file;

    // Handle file upload ONLY if a new file is provided
    if ($request->hasFile('file')) {
        // Delete previous file if exists
        if ($slip->file && Storage::exists('public/documents/' . $slip->file)) {
            Storage::delete('public/documents/' . $slip->file);
        }

        $file = $request->file('file');
        $filename = now()->format('Y-m-d_H-i-s') . '_' . $file->getClientOriginalName();
        $file->storeAs('public/documents', $filename);
    }

    // Update only the required fields
    $slip->update([
        'op_ctrl'        => $request->op_ctrl,
        'pres_dept'      => 'Dr. Aladino C. Moraca', 
        'trans_remarks'  => $request->trans_remarks,
        'set_users_to'   => implode(',', $request->set_users_to),
        'file'           => $filename,
        'routing_status' => 2,
    ]);

    return redirect()
        ->route('routingToPres') // redirect back to edit view
        ->with('success', 'Routing slip updated successfully.');
}

public function editRoutingEntry($id)
    {
        $slipEntry  = RoutingSlip::findOrFail($id);
        $users = User::all();
        $groups = Group::all();

        return view('pages.edit_trans_entry', compact('slipEntry', 'users', 'groups'));
    }



    // updated to add the email notif 03/09/2026

// public function updateRoutingEntry(Request $request, $id)
//     {
//         $slip = RoutingSlip::findOrFail($id);

//         // Validate ONLY routed users
//         $request->validate([
//             'routed_users'   => 'required|array',
//             'routed_users.*' => 'string', // user ID or group:ID
//         ]);

//         $finalUserIds = [];

//         foreach ($request->routed_users as $item) {
//             if (str_starts_with($item, 'group:')) {

//                 $groupId = explode(':', $item)[1];
//                 $group   = Group::find($groupId);

//                 if ($group) {
//                     $finalUserIds = array_merge(
//                         $finalUserIds,
//                         $group->users()->pluck('users.id')->toArray()
//                     );
//                 }

//             } else {
//                 // Individual user
//                 $finalUserIds[] = $item;
//             }
//         }

//         // Remove duplicates & ensure valid users only
//         $finalUserIds = User::whereIn('id', array_unique($finalUserIds))
//                             ->pluck('id')
//                             ->toArray();

//         // Update ONLY what is needed
//         $slip->update([
//             'routed_users'   => implode(',', $finalUserIds),
//             'routing_status' => 3,
//         ]);

//         LogsRoute::create([
//         'slip_id'      => $slip->id,
//         'rslip_id'     => $slip->rslip_id,
//         'log_creator'  => auth()->id(),
//         'log_action'   => 'Routed to users',
//         'file'         => $slip->file,
//         'routed_users' => implode(',', $finalUserIds),
//     ]);

//     foreach ($finalUserIds as $userId) {
//     LogsTrans::create([
//         'slip_id'        => $slip->id,                 // routing_slip.id
//         'rslip_id'       => $slip->rslip_id,            // routing_slip.rslip_id
//         'creator_id'     => auth()->id(),               // logged-in user
//         'source'         => $slip->source,
//         'subject'        => $slip->subject,
//         'trans_remarks'  => $slip->trans_remarks,
//         'other_remarks'  => $slip->other_remarks,
//         'ass_comment'    => null,
//         'r_users'        => $userId,                    // ✅ ONE user per row
//         'reassigned_to'  => null,
//         'file'           => $slip->file,
//         'purge_status'           => null,
//         'trans_status'   => 1,
//         'date_received'  => $slip->date_received,
//     ]);
// }

// // ✅ ROUTING PDF SNAPSHOT (ONE RECORD ONLY)
//     RoutingPdf::create([
//     'routing_slip_id' => $slip->id,         // ✅ THIS IS REQUIRED
//     'rslip_id'        => $slip->rslip_id,   // business/control number
//     'op_ctrl'         => $slip->op_ctrl,
//     'creator_id'      => auth()->id(),
//     'pres_id'         => 38,
//     'pres_dept'       => 'From the Office of the University President',
//     'trans_remarks'   => $slip->trans_remarks,
//     'other_remarks'   => $slip->other_remarks,
//     'routed_users'    => implode(',', $finalUserIds),
//     'reassigned_to'   => null,
//     'routing_action'  => 1,
//     'date_received'   => $slip->date_received,
// ]);


//         return redirect()
//             ->route('routing')
//             ->with('success', 'Routing slip updated successfully.');
//     }

public function updateRoutingEntry(Request $request, $id)
{
    $slip = RoutingSlip::findOrFail($id);

    $request->validate([
        'routed_users'   => 'required|array',
        'routed_users.*' => 'string',
    ]);

    $finalUserIds = [];

    foreach ($request->routed_users as $item) {

        if (str_starts_with($item, 'group:')) {

            $groupId = explode(':', $item)[1];
            $group   = Group::find($groupId);

            if ($group) {
                $finalUserIds = array_merge(
                    $finalUserIds,
                    $group->users()->pluck('users.id')->toArray()
                );
            }

        } else {
            $finalUserIds[] = $item;
        }
    }

    // Remove duplicates
    $finalUserIds = User::whereIn('id', array_unique($finalUserIds))
                        ->pluck('id')
                        ->toArray();

    // Update routing slip
    $slip->update([
        'routed_users'   => implode(',', $finalUserIds),
        'routing_status' => 3,
    ]);

    LogsRoute::create([
        'slip_id'      => $slip->id,
        'rslip_id'     => $slip->rslip_id,
        'log_creator'  => auth()->id(),
        'log_action'   => 'Routed to users',
        'file'         => $slip->file,
        'routed_users' => implode(',', $finalUserIds),
    ]);

    foreach ($finalUserIds as $userId) {

        LogsTrans::create([
            'slip_id'        => $slip->id,
            'rslip_id'       => $slip->rslip_id,
            'creator_id'     => auth()->id(),
            'source'         => $slip->source,
            'subject'        => $slip->subject,
            'trans_remarks'  => $slip->trans_remarks,
            'other_remarks'  => $slip->other_remarks,
            'ass_comment'    => null,
            'r_users'        => $userId,
            'reassigned_to'  => null,
            'file'           => $slip->file,
            'purge_status'   => null,
            'trans_status'   => 1,
            'date_received'  => $slip->date_received,
        ]);

        // ✅ SEND EMAIL TO EACH USER
        $user = User::find($userId);

        if ($user && $user->email) {

            Mail::to($user->email)->send(
                new DocumentRoutedNotification(
                    $slip,
                    $user->fname . ' ' . $user->lname,
                    $slip->trans_remarks
                )
            );
        }
    }

    // ROUTING PDF SNAPSHOT
    RoutingPdf::create([
        'routing_slip_id' => $slip->id,
        'rslip_id'        => $slip->rslip_id,
        'op_ctrl'         => $slip->op_ctrl,
        'creator_id'      => auth()->id(),
        'pres_id'         => 38,
        'pres_dept'       => 'From the Office of the University President',
        'trans_remarks'   => $slip->trans_remarks,
        'other_remarks'   => $slip->other_remarks,
        'routed_users'    => implode(',', $finalUserIds),
        'reassigned_to'   => null,
        'routing_action'  => 1,
        'date_received'   => $slip->date_received,
    ]);

    return redirect()
        ->route('routing')
        ->with('success', 'Routing slip updated successfully.');
}



public function routeBackToPresident($id)
{
    // Find the routing slip
    $slip = RoutingSlip::findOrFail($id);

    // Update routing_status and reset specific fields
    $slip->update([
        'routing_status'  => 1,
        'op_ctrl'         => null,
        'trans_remarks'   => null,
        'other_remarks'   => null,
        'set_users_to'    => null,
    ]);

    // Add a log in logs_route
    LogsRoute::create([
        'slip_id'      => $slip->id,
        'rslip_id'     => $slip->rslip_id,
        'log_creator'  => auth()->id(),
        'log_action'   => 'Routed back to Edit', // or 'Routed back to President'
        'file'         => $slip->file,
        'routed_users' => $slip->routed_users, // keep current routed users
    ]);

    return redirect()
        ->route('routing')
        ->with('success', 'Routing slip routed back to President successfully.');
}


public function editRerouteEntry($id)
{
    $slipEntry = RoutingSlip::findOrFail($id);
    $users = User::all();
    $groups = Group::all();

    // Existing routed users
    $existingRoutedIds = array_filter(
        explode(',', $slipEntry->routed_users)
    );

    // Fetch reassigned users NOT already routed
    $filteredReassigned = ReassignedUser::where('slip_id', $slipEntry->id)
        ->where('rslip_id', $slipEntry->rslip_id)
        ->get()
        ->filter(function ($item) use ($existingRoutedIds) {
            return !in_array($item->reassigned_id, $existingRoutedIds);
        });

    // Map reassigned users to full names
    $reassignedUsers = $filteredReassigned->map(function ($item) use ($users) {
        $user = $users->firstWhere('id', $item->reassigned_id);
        return $user ? $user->fname . ' ' . $user->lname : null;
    })->filter()->toArray();

    // // Map creators corresponding only to the filtered reassigned users
    // $reassignedUsersCreators = $filteredReassigned->map(function ($item) use ($users) {
    //     $creator = $users->firstWhere('id', $item->creator_id);
    //     return $creator ? $creator->fname . ' ' . $creator->lname : null;
    // })->filter()->toArray();

    // Map creators corresponding only to the filtered reassigned users
    $reassignedUsersCreators = $filteredReassigned->map(function ($item) use ($users) {
        $creator = $users->firstWhere('id', $item->creator_id);
        return $creator ? $creator->fname . ' ' . $creator->lname : null;
    })
    ->filter()             // remove nulls
    ->unique()             // remove duplicate names
    ->values()             // reindex array
    ->toArray();

    return view('pages.edit_reroute_entry', compact(
        'slipEntry',
        'users',
        'groups',
        'reassignedUsers',
        'reassignedUsersCreators'
    ));
}


// march 3, 2026

// public function updateRerouteEntry(Request $request, $id)
// {
//     $slip = RoutingSlip::findOrFail($id);

//     // Validate routed users
//     $request->validate([
//         'routed_users'   => 'required|array',
//         'routed_users.*' => 'string', // user ID or group:ID
//     ]);

//     $finalUserIds = [];

//     // Process selections: groups or individual users
//     foreach ($request->routed_users as $item) {
//         if (str_starts_with($item, 'group:')) {
//             $groupId = explode(':', $item)[1];
//             $group = Group::find($groupId);

//             if ($group) {
//                 $finalUserIds = array_merge(
//                     $finalUserIds,
//                     $group->users()->pluck('users.id')->toArray()
//                 );
//             }
//         } else {
//             $finalUserIds[] = $item;
//         }
//     }

//     // Remove duplicates & ensure valid users
//     $finalUserIds = User::whereIn('id', array_unique($finalUserIds))
//                         ->pluck('id')
//                         ->toArray();

//     // Merge with existing routed users
//     $existingUserIds = array_filter(
//         explode(',', $slip->routed_users)
//     );

//     $mergedUserIds = array_unique(array_merge($existingUserIds, $finalUserIds));

//     // Update routing slip
//     $slip->update([
//         'routed_users'   => implode(',', $mergedUserIds),
//         'reassigned_to'  => 0,      // reset reassigned
//         'routing_status' => 3,
//     ]);

//     // Create log in logs_route
//     LogsRoute::create([
//         'slip_id'      => $slip->id,
//         'rslip_id'     => $slip->rslip_id,
//         'log_creator'  => auth()->id(),
//         'log_action'   => 'Rerouted to users',
//         'file'         => $slip->file,
//         'routed_users' => implode(',', $mergedUserIds),
//     ]);


// // Only create LogsTrans for newly added users
// $newUserIds = array_diff($mergedUserIds, $existingUserIds);

// $creatorIdsForNewUsers = [];

// foreach ($newUserIds as $userId) {
//     $logTrans = LogsTrans::create([
//         'slip_id'        => $slip->id,            
//         'rslip_id'       => $slip->rslip_id,     
//         'creator_id'     => auth()->id(),        
//         'source'         => $slip->source,
//         'subject'        => $slip->subject,
//         'trans_remarks'  => $slip->trans_remarks,
//         'other_remarks'  => $slip->other_remarks,
//         'ass_comment'    => null,
//         'r_users'        => $userId,             
//         'reassigned_to'  => null,
//         'file'           => $slip->file,
//         'purge_status'           => null,
//         'trans_status'   => 1,
//         'date_received'  => $slip->date_received,
//     ]);

//     // Find the ReassignedUser record for this new user
//     $reassignedRecord = ReassignedUser::where('slip_id', $slip->id)
//         ->where('rslip_id', $slip->rslip_id)
//         ->where('reassigned_id', $userId)
//         ->first();

//     if ($reassignedRecord) {
//         $creatorIdsForNewUsers[] = $reassignedRecord->creator_id;
//     }
// }

// // Remove duplicates and keep only unique creator IDs
// $creatorIdsForNewUsers = array_unique($creatorIdsForNewUsers);

// // Save in RoutingPdf
// RoutingPdf::create([
//     'routing_slip_id' => $slip->id,
//     'rslip_id'        => $slip->rslip_id,
//     'op_ctrl'         => $slip->op_ctrl,
//     'creator_id'      => auth()->id(),
//     'pres_id'         => null,
//     'pres_dept'       => null,
//     'trans_remarks'   => $slip->trans_remarks,
//     'other_remarks'   => $slip->other_remarks,
//     'routed_users'    => implode(',', $creatorIdsForNewUsers), // only creators of newly reassigned users
//     'reassigned_to'   => implode(',', $finalUserIds),          // new assigned users
//     'routing_action'  => 2,
//     'date_received'   => $slip->date_received,
// ]);

//     return redirect()
//         ->route('routing')
//         ->with('success', 'Routing slip successfully rerouted.');
// }



public function updateRerouteEntry(Request $request, $id)
{
    // Find the routing slip
    $slip = RoutingSlip::findOrFail($id);

    // Validate routed users
    $request->validate([
        'routed_users'   => 'required|array',
        'routed_users.*' => 'string', // user ID or group:ID
    ]);

    $finalUserIds = [];

    // Process selections: groups or individual users
    foreach ($request->routed_users as $item) {
        if (str_starts_with($item, 'group:')) {
            $groupId = explode(':', $item)[1];
            $group = Group::find($groupId);

            if ($group) {
                $finalUserIds = array_merge(
                    $finalUserIds,
                    $group->users()->pluck('users.id')->toArray()
                );
            }
        } else {
            $finalUserIds[] = $item;
        }
    }

    // Remove duplicates & ensure valid users
    $finalUserIds = User::whereIn('id', array_unique($finalUserIds))
                        ->pluck('id')
                        ->toArray();

    // Merge with existing routed users
    $existingUserIds = array_filter(explode(',', $slip->routed_users));
    $mergedUserIds = array_unique(array_merge($existingUserIds, $finalUserIds));

    // Update routing slip
    $slip->update([
        'routed_users'   => implode(',', $mergedUserIds),
        'reassigned_to'  => 0,      // reset reassigned
        'routing_status' => 3,
    ]);

    // Log the reroute action
    LogsRoute::create([
        'slip_id'      => $slip->id,
        'rslip_id'     => $slip->rslip_id,
        'log_creator'  => auth()->id(),
        'log_action'   => 'Rerouted to users',
        'file'         => $slip->file,
        'routed_users' => implode(',', $mergedUserIds),
    ]);

    // Handle only newly added users
    $newUserIds = array_diff($mergedUserIds, $existingUserIds);
    $creatorIdsForNewUsers = [];


    // March 9, 2026 - updated to add email notification and fix creator mapping
    // foreach ($newUserIds as $userId) {

    //     // Create individual LogsTrans entry
    //     LogsTrans::create([
    //         'slip_id'        => $slip->id,
    //         'rslip_id'       => $slip->rslip_id,
    //         'creator_id'     => auth()->id(),
    //         'source'         => $slip->source,
    //         'subject'        => $slip->subject,
    //         'trans_remarks'  => $slip->trans_remarks,
    //         'other_remarks'  => $slip->other_remarks,
    //         'ass_comment'    => null,
    //         'r_users'        => $userId,
    //         'reassigned_to'  => null,
    //         'file'           => $slip->file,
    //         'purge_status'   => null,
    //         'trans_status'   => 1,
    //         'date_received'  => $slip->date_received,
    //     ]);

    //     // Get the reassigned user record
    //     $reassignedRecord = ReassignedUser::where('slip_id', $slip->id)
    //         ->where('rslip_id', $slip->rslip_id)
    //         ->where('reassigned_id', $userId)
    //         ->first();

    //     if ($reassignedRecord) {
    //         // Add creator to PDF log
    //         $creatorIdsForNewUsers[] = $reassignedRecord->creator_id;

    //         // Set original creator if not already set
    //         if (!$reassignedRecord->original_creator_id) {
    //             $reassignedRecord->update([
    //                 'original_creator_id' => 56,
    //             ]);
    //         }
    //     }
    // }

    foreach ($newUserIds as $userId) {

    // Create individual LogsTrans entry
    LogsTrans::create([
        'slip_id'        => $slip->id,
        'rslip_id'       => $slip->rslip_id,
        'creator_id'     => auth()->id(),
        'source'         => $slip->source,
        'subject'        => $slip->subject,
        'trans_remarks'  => $slip->trans_remarks,
        'other_remarks'  => $slip->other_remarks,
        'ass_comment'    => null,
        'r_users'        => $userId,
        'reassigned_to'  => null,
        'file'           => $slip->file,
        'purge_status'   => null,
        'trans_status'   => 1,
        'date_received'  => $slip->date_received,
    ]);

    // ✅ SEND EMAIL NOTIFICATION
    $user = User::find($userId);

    if ($user && $user->email) {
        Mail::to($user->email)->send(
            new DocumentRoutedNotification(
                $slip,
                $user->fname . ' ' . $user->lname,
                $slip->trans_remarks
            )
        );
    }

    // Get the reassigned user record
    $reassignedRecord = ReassignedUser::where('slip_id', $slip->id)
        ->where('rslip_id', $slip->rslip_id)
        ->where('reassigned_id', $userId)
        ->first();

    if ($reassignedRecord) {

        $creatorIdsForNewUsers[] = $reassignedRecord->creator_id;

        if (!$reassignedRecord->original_creator_id) {
            $reassignedRecord->update([
                'original_creator_id' => 56,
            ]);
        }
    }
}

    // Remove duplicates
    $creatorIdsForNewUsers = array_unique($creatorIdsForNewUsers);

    // Save to RoutingPdf
    RoutingPdf::create([
        'routing_slip_id' => $slip->id,
        'rslip_id'        => $slip->rslip_id,
        'op_ctrl'         => $slip->op_ctrl,
        'creator_id'      => auth()->id(),
        'pres_id'         => null,
        'pres_dept'       => null,
        'trans_remarks'   => $slip->trans_remarks,
        'other_remarks'   => $slip->other_remarks,
        'routed_users'    => implode(',', $creatorIdsForNewUsers), // creators of newly reassigned users
        'reassigned_to'   => implode(',', $finalUserIds),          // new assigned users
        'routing_action'  => 2,
        'date_received'   => $slip->date_received,
    ]);

    return redirect()
        ->route('routing')
        ->with('success', 'Routing slip successfully rerouted.');
}

// for recall funtion
public function editRecall($id)
{
    $slipEntry = RoutingSlip::findOrFail($id);

    // Fetch all users
    $users = User::all();

    // Fetch all groups (FIX for undefined $groups)
    $groups = Group::all();

    // Get routed user IDs from routing_slip
    $routedUserIds = array_filter(
        array_map('trim', explode(',', $slipEntry->routed_users ?? ''))
    );

    // Map routed users to full names
    $routedUsers = $users->whereIn('id', $routedUserIds)
        ->map(function ($user) {
            return $user->fname . ' ' . $user->lname;
        })
        ->values()
        ->toArray();

    return view('pages.recallEntry', compact(
        'slipEntry',
        'users',
        'groups',
        'routedUsers'
    ));
}

// public function updateRecall(Request $request, $id)
// {
//     $slip = RoutingSlip::findOrFail($id);

//     // Validate routed users
//     $request->validate([
//         'routed_users'   => 'required|array',
//         'routed_users.*' => 'string', // user ID or group:ID
//         'file'           => 'nullable|file|max:20480',
//     ]);

//     /* =========================
//        FILE HANDLING
//     ==========================*/
//     $fileName = $slip->file; // default: retain existing

//     if ($request->hasFile('file')) {
//         $file = $request->file('file');
//         $fileName = time() . '_' . $file->getClientOriginalName();
//         $file->storeAs('documents', $fileName);
//     }

//     /* =========================
//        PROCESS ROUTED USERS
//     ==========================*/
//     $finalUserIds = [];

//     foreach ($request->routed_users as $item) {
//         // GROUP
//         if (str_starts_with($item, 'group:')) {
//             $groupId = explode(':', $item)[1];
//             $group = Group::find($groupId);

//             if ($group) {
//                 $finalUserIds = array_merge(
//                     $finalUserIds,
//                     $group->users()->pluck('users.id')->toArray()
//                 );
//             }
//         }
//         // INDIVIDUAL USER
//         else {
//             $finalUserIds[] = $item;
//         }
//     }

//     // Ensure valid unique users
//     $finalUserIds = User::whereIn('id', array_unique($finalUserIds))
//         ->pluck('id')
//         ->toArray();

//     /* =========================
//        MERGE WITH EXISTING
//     ==========================*/
//     $existingUserIds = array_filter(
//         array_map('trim', explode(',', $slip->routed_users ?? ''))
//     );

//     $mergedUserIds = array_unique(
//         array_merge($existingUserIds, $finalUserIds)
//     );

//     /* =========================
//        UPDATE ROUTING SLIP
//     ==========================*/
//     $slip->update([
//         'routed_users'   => implode(',', $mergedUserIds),
//         'file'           => $fileName,
//         'routing_status' => 3,
//     ]);

//     /* =========================
//        LOGS_ROUTE (RECALL)
//     ==========================*/
//     LogsRoute::create([
//         'slip_id'      => $slip->id,
//         'rslip_id'     => $slip->rslip_id,
//         'log_creator'  => auth()->id(),
//         'log_action'   => 'Recall transaction',
//         'file'         => $fileName,
//         'routed_users' => implode(',', $mergedUserIds),
//     ]);

//     /* =========================
//        LOGS_TRANS (ONLY NEW USERS)
//     ==========================*/
//     $newUserIds = array_diff($mergedUserIds, $existingUserIds);

//     foreach ($newUserIds as $userId) {
//         LogsTrans::create([
//             'slip_id'       => $slip->id,
//             'rslip_id'      => $slip->rslip_id,
//             'creator_id'    => auth()->id(),
//             'source'        => $slip->source,
//             'subject'       => $slip->subject,
//             'trans_remarks' => $slip->trans_remarks,
//             'other_remarks' => $slip->other_remarks,
//             'ass_comment'   => null,
//             'r_users'       => $userId,
//             'reassigned_to' => null,
//             'file'          => $fileName,
//             'purge_status'          => null,
//             'trans_status'  => 1,
//             'date_received' => $slip->date_received,
//         ]);
//     }
//         RoutingPdf::updateOrCreate(
//             ['rslip_id' => $slip->rslip_id], 
//             [
//                 'routing_slip_id' => $slip->id,                     
//                 'op_ctrl'         => $slip->op_ctrl,
//                 'creator_id'      => auth()->id(),
//                 'pres_id'         => null,                           
//                 'pres_dept'       => null,                           
//                 'trans_remarks'   => $slip->trans_remarks,
//                 'other_remarks'   => $slip->other_remarks,
//                 'routed_users'    => implode(',', $mergedUserIds),  
//                 'reassigned_to'   => null,  
//                 'routing_action'  => 1,                              
//                 'date_received'   => $slip->date_received,
//             ]
//         );
//     return redirect()
//         ->route('routing')
//         ->with('success', 'Recall successfully processed.');
// }


public function updateRecall(Request $request, $id)
{
    $slip = RoutingSlip::findOrFail($id);

    // Validate routed users
    $request->validate([
        'routed_users'   => 'required|array',
        'routed_users.*' => 'string', // user ID or group:ID
        'file'           => 'nullable|file|max:20480',
    ]);

    /* =========================
       FILE HANDLING
    ==========================*/
    $fileName = $slip->file;

if ($request->hasFile('file')) {

    if ($slip->file && Storage::disk('public')->exists('documents/' . $slip->file)) {
        Storage::disk('public')->delete('documents/' . $slip->file);
    }

    $file = $request->file('file');

    $originalName = preg_replace('/\s+/', ' ', $file->getClientOriginalName());

    $fileName = now()->format('Y-m-d_H-i-s') . '_' . $originalName;

    $file->storeAs('documents', $fileName, 'public');
}


    /* =========================
       PROCESS ROUTED USERS
    ==========================*/
    $finalUserIds = [];

    foreach ($request->routed_users as $item) {
        // GROUP
        if (str_starts_with($item, 'group:')) {
            $groupId = explode(':', $item)[1];
            $group = Group::find($groupId);

            if ($group) {
                $finalUserIds = array_merge(
                    $finalUserIds,
                    $group->users()->pluck('users.id')->toArray()
                );
            }
        }
        // INDIVIDUAL USER
        else {
            $finalUserIds[] = $item;
        }
    }

    // Ensure valid unique users
    $finalUserIds = User::whereIn('id', array_unique($finalUserIds))
        ->pluck('id')
        ->toArray();

    /* =========================
       MERGE WITH EXISTING
    ==========================*/
    $existingUserIds = array_filter(
        array_map('trim', explode(',', $slip->routed_users ?? ''))
    );

    $mergedUserIds = array_unique(
        array_merge($existingUserIds, $finalUserIds)
    );

    /* =========================
       UPDATE ROUTING SLIP
    ==========================*/
    $slip->update([
        'routed_users'   => implode(',', $mergedUserIds),
        'file'           => $fileName,
        'routing_status' => 3,
    ]);

if ($request->hasFile('file')) {
    LogsTrans::where('rslip_id', $slip->rslip_id)
        ->update(['file' => $fileName]);
}
    /* =========================
       LOGS_ROUTE (RECALL)
    ==========================*/
    LogsRoute::create([
        'slip_id'      => $slip->id,
        'rslip_id'     => $slip->rslip_id,
        'log_creator'  => auth()->id(),
        'log_action'   => 'Recall transaction',
        'file'         => $fileName,
        'routed_users' => implode(',', $mergedUserIds),
    ]);

    /* =========================
       LOGS_TRANS (ONLY NEW USERS)
    ==========================*/
    $newUserIds = array_diff($mergedUserIds, $existingUserIds);

    // foreach ($newUserIds as $userId) {
    //     LogsTrans::create([
    //         'slip_id'       => $slip->id,
    //         'rslip_id'      => $slip->rslip_id,
    //         'creator_id'    => auth()->id(),
    //         'source'        => $slip->source,
    //         'subject'       => $slip->subject,
    //         'trans_remarks' => $slip->trans_remarks,
    //         'other_remarks' => $slip->other_remarks,
    //         'ass_comment'   => null,
    //         'r_users'       => $userId,
    //         'reassigned_to' => null,
    //         'file'          => $fileName,
    //         'purge_status'          => null,
    //         'trans_status'  => 1,
    //         'date_received' => $slip->date_received,
    //     ]);
    // }

    foreach ($newUserIds as $userId) {

    LogsTrans::create([
        'slip_id'       => $slip->id,
        'rslip_id'      => $slip->rslip_id,
        'creator_id'    => auth()->id(),
        'source'        => $slip->source,
        'subject'       => $slip->subject,
        'trans_remarks' => $slip->trans_remarks,
        'other_remarks' => $slip->other_remarks,
        'ass_comment'   => null,
        'r_users'       => $userId,
        'reassigned_to' => null,
        'file'          => $fileName,
        'purge_status'  => null,
        'trans_status'  => 1,
        'date_received' => $slip->date_received,
    ]);

    // ✅ SEND EMAIL NOTIFICATION
    $user = User::find($userId);

    if ($user && $user->email) {

        Mail::to($user->email)->send(
            new DocumentRoutedNotification(
                $slip,
                $user->fname . ' ' . $user->lname,
                $slip->trans_remarks
            )
        );
    }
}
        RoutingPdf::updateOrCreate(
            ['rslip_id' => $slip->rslip_id], 
            [
                'routing_slip_id' => $slip->id,                     
                'op_ctrl'         => $slip->op_ctrl,
                'creator_id'      => auth()->id(),
                'pres_id'         => null,                           
                'pres_dept'       => null,                           
                'trans_remarks'   => $slip->trans_remarks,
                'other_remarks'   => $slip->other_remarks,
                'routed_users'    => implode(',', $mergedUserIds),  
                'reassigned_to'   => null,  
                'routing_action'  => 1,                              
                'date_received'   => $slip->date_received,
            ]
        );
    return redirect()
        ->route('routing')
        ->with('success', 'Recall successfully processed.');
}

}
