<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Group;
use App\Models\RoutingSlip;
use App\Models\LogsTrans;
use App\Models\Office;
use App\Models\SystemLog;
use App\Models\LogsRoute;
use App\Models\ManagementLog;
use App\Models\InterOffice;
use App\Models\InterOfficeLog;


class PagesController extends Controller
{
    //

// public function dashboard()
// {
//     $userId = auth()->id();
//     $role   = auth()->user()->role;

//     // -----------------------------
//     // Pending Count (same logic as pending())
//     // -----------------------------
//     $pendingLogs = LogsTrans::where('trans_status', 1)
//         ->when($role === 'staff', function ($query) use ($userId) {
//             $query->where('r_users', 'LIKE', "%{$userId}%"); // staff sees only assigned
//         })
//         ->when(in_array($role, ['Administrator', 'records_officer']), function ($query) use ($userId) {
//             $query->where(function ($q) use ($userId) {
//                 $q->where('creator_id', $userId)
//                   ->orWhere('r_users', 'LIKE', "%{$userId}%"); // admin/records sees assigned or created
//             });
//         })
//         ->orderBy('date_received', 'desc')
//         ->get();

//     $pendingCount = $pendingLogs->groupBy('slip_id')->count(); // staff with no r_users => 0

//     // -----------------------------
//     // Existing dashboard data
//     // -----------------------------
//     $servedLogs = RoutingSlip::whereIn('routing_status', [3])
//         ->where(function ($query) use ($userId) {
//             $query->where('creator_id', $userId)
//                   ->orWhere('routed_users', 'LIKE', "%{$userId}%");
//         })
//         ->where(function ($q) {
//             $q->whereNull('validity_status')
//               ->orWhere('validity_status', '<>', 1);
//         })
//         ->orderBy('date_received', 'desc')
//         ->get();

//     $dashDocs = $servedLogs->groupBy('id');

//     // -----------------------------
//     // Pass everything to view
//     // -----------------------------
//     return view('home.dashboard', [
//         'pendingDocs'  => $dashDocs,
//         'users'        => User::orderBy('fname')->get(),
//         'groups'       => Group::orderBy('group_name')->get(),
//         'pendingCount' => $pendingCount,
//     ]);
// }


public function dashboard()
{
    $userId = auth()->id();
    $role   = auth()->user()->role;

    // -----------------------------
    // Pending Count for LogsTrans
    // -----------------------------
    $pendingLogs = LogsTrans::where('trans_status', 1)
        ->when($role === 'staff', function ($query) use ($userId) {
            $query->where('r_users', 'LIKE', "%{$userId}%");
        })
        ->when(in_array($role, ['Administrator', 'records_officer']), function ($query) use ($userId) {
            $query->where(function ($q) use ($userId) {
                $q->where('creator_id', $userId)
                  ->orWhere('r_users', 'LIKE', "%{$userId}%");
            });
        })
        ->orderBy('date_received', 'desc')
        ->get();

    $pendingCount = $pendingLogs->groupBy('slip_id')->count();

    // -----------------------------
    // Pending Count for InterOfficeLogs
    // -----------------------------
$interOfficeCount = InterOfficeLog::where('track_status', 1)
    ->where(function ($query) use ($userId) {
        // Condition 1: current user is creator of the inter_office
        $query->whereHas('interOffice', function ($q) use ($userId) {
            $q->where('creator_id', $userId);
        })
        // Condition 2: current user is assigned (user_id)
        ->orWhere('user_id', $userId);
    })
    ->distinct('track_slip')   // count unique transactions
    ->count('track_slip');

    
    // -----------------------------
    // Existing served logs
    // -----------------------------
    $servedLogs = RoutingSlip::whereIn('routing_status', [3])
        ->where(function ($query) use ($userId) {
            $query->where('creator_id', $userId)
                  ->orWhere('routed_users', 'LIKE', "%{$userId}%");
        })
        ->where(function ($q) {
            $q->whereNull('validity_status')
              ->orWhere('validity_status', '<>', 1);
        })
        ->orderBy('date_received', 'desc')
        ->get();

    $dashDocs = $servedLogs->groupBy('id');

    // -----------------------------
    // Pass everything to view
    // -----------------------------
    return view('home.dashboard', [
        'pendingDocs'       => $dashDocs,
        'users'             => User::orderBy('fname')->get(),
        'groups'            => Group::orderBy('group_name')->get(),
        'pendingCount'      => $pendingCount,
        'interOfficeCount'  => $interOfficeCount, // for sidebar badge
    ]);
}



public function routingPending()
{
    $userId = auth()->id();
    $role   = auth()->user()->role;

    // Pending Count
    $pendingLogs = LogsTrans::where('trans_status', 1)
        ->when($role === 'staff', fn($q) => $q->where('r_users', 'LIKE', "%{$userId}%"))
        ->when(in_array($role, ['Administrator', 'records_officer']), fn($q) => $q->where(fn($q2) => $q2->where('creator_id', $userId)->orWhere('r_users', 'LIKE', "%{$userId}%")))
        ->orderBy('date_received', 'desc')
        ->get();

    $pendingCount = $pendingLogs->groupBy('slip_id')->count();

     $routedPending = RoutingSlip::where('transaction_type', 1)
                    ->where('routing_status', '=', 3)
                    ->orderBy('date_received', 'desc')
                    ->get();
    return view('pages.routedPresPending', [
        'routedPending' => $routedPending,
        'users'  => User::orderBy('fname')->get(),
        'groups' => Group::orderBy('group_name')->get(),
        'pendingCount' => $pendingCount,
    ]);
}

    public function routing()
    {
        $userId = auth()->id();
        $role   = auth()->user()->role;

    // Pending Count
        $pendingLogs = LogsTrans::where('trans_status', 1)
            ->when($role === 'staff', fn($q) => $q->where('r_users', 'LIKE', "%{$userId}%"))
            ->when(in_array($role, ['Administrator', 'records_officer']), fn($q) => $q->where(fn($q2) => $q2->where('creator_id', $userId)->orWhere('r_users', 'LIKE', "%{$userId}%")))
            ->orderBy('date_received', 'desc')
            ->get();

        $pendingCount = $pendingLogs->groupBy('slip_id')->count();

        $routedBackDocs = RoutingSlip::where('transaction_type', 1)
                        ->where('routing_status', '=', 2)
                        ->orderBy('date_received', 'desc')
                        ->get();

                        $interOfficeCount = InterOfficeLog::where('track_status', 1)
        ->where(function ($query) use ($userId) {
            // Condition 1: current user is creator of the inter_office
            $query->whereHas('interOffice', function ($q) use ($userId) {
                $q->where('creator_id', $userId);
            })
            // Condition 2: current user is assigned (user_id)
            ->orWhere('user_id', $userId);
        })
        ->distinct('track_slip')   // count unique transactions
        ->count('track_slip');

        return view('pages.routing', [
            'routedBackDocs' => $routedBackDocs,
            'users'  => User::orderBy('fname')->get(),
            'groups' => Group::orderBy('group_name')->get(),
            'pendingCount'   => $pendingCount,
            'interOfficeCount' => $interOfficeCount,

        ]);
    }

    public function routingToPres()
    {
        $userId = auth()->id();
        $role   = auth()->user()->role;

        // Pending Count
        $pendingLogs = LogsTrans::where('trans_status', 1)
            ->when($role === 'staff', fn($q) => $q->where('r_users', 'LIKE', "%{$userId}%"))
            ->when(in_array($role, ['Administrator', 'records_officer']), fn($q) => $q->where(fn($q2) => $q2->where('creator_id', $userId)->orWhere('r_users', 'LIKE', "%{$userId}%")))
            ->orderBy('date_received', 'desc')
            ->get();

        $pendingCount = $pendingLogs->groupBy('slip_id')->count();

        // Fetch all documents routed to President's Office (transaction_type = 1)
        $routedDocs = RoutingSlip::where('transaction_type', 1)
                        ->where('routing_status', '=', 1)
                        ->orderBy('date_received', 'desc')
                        ->get();

        return view('pages.routing_to_pres', [
            'routedDocs' => $routedDocs,
            'users'      => User::orderBy('fname')->get(),
            'groups'     => Group::orderBy('group_name')->get(),
            'pendingCount' => $pendingCount,
        ]);
    }

// public function pending()
// {
//     $userId = auth()->id();

//     // Fetch logs that the current user can see
//     $pendingLogs = LogsTrans::where('trans_status', 1)
//         ->where(function ($query) use ($userId) {
//             $query->where('creator_id', $userId)
//                   ->orWhere('r_users', 'LIKE', "%{$userId}%");
//         })
//         ->orderBy('date_received', 'desc')
//         ->get();

//     // Group logs by slip_id
//     $pendingDocs = $pendingLogs->groupBy('slip_id');

//     $count = $pendingDocs->count();

//     return view('pages.pending', [
//         'pendingDocs' => $pendingDocs,
//         'users'       => User::orderBy('fname')->get(),
//         'groups'      => Group::orderBy('group_name')->get(),
//         'count'       => $count,
//     ]);
// }

    public function pending()
    {
        $userId = auth()->id();
        $role   = auth()->user()->role;

        $pendingLogs = LogsTrans::where('trans_status', 1)
            ->when($role === 'staff', function ($query) use ($userId) {
                $query->where('r_users', 'LIKE', "%{$userId}%");
            })
            ->when(in_array($role, ['Administrator', 'records_officer']), function ($query) use ($userId) {
                $query->where(function ($q) use ($userId) {
                    $q->where('creator_id', $userId)
                    ->orWhere('r_users', 'LIKE', "%{$userId}%");
                });
            })
            ->orderBy('date_received', 'desc')
            ->get();

        $pendingDocs = $pendingLogs->groupBy('slip_id');

        $interOfficeCount = InterOfficeLog::where('track_status', 1)
        ->where(function ($query) use ($userId) {
            // Condition 1: current user is creator of the inter_office
            $query->whereHas('interOffice', function ($q) use ($userId) {
                $q->where('creator_id', $userId);
            })
            // Condition 2: current user is assigned (user_id)
            ->orWhere('user_id', $userId);
        })
        ->distinct('track_slip')   // count unique transactions
        ->count('track_slip');

        return view('pages.pending', [
            'pendingDocs' => $pendingDocs,
            'users'       => User::orderBy('fname')->get(),
            'groups'      => Group::orderBy('group_name')->get(),
            'pendingCount' => $pendingDocs->count(), // same variable name
            'interOfficeCount' => $interOfficeCount,
        ]);
    }

    public function usersView()
    {
        $offices = Office::orderBy('office_name', 'asc')->get();

        $userId = auth()->id();
        $role   = auth()->user()->role;

        // Pending Count
        $pendingLogs = LogsTrans::where('trans_status', 1)
            ->when($role === 'staff', fn($q) => $q->where('r_users', 'LIKE', "%{$userId}%"))
            ->when(in_array($role, ['Administrator', 'records_officer']), fn($q) => $q->where(fn($q2) => $q2->where('creator_id', $userId)->orWhere('r_users', 'LIKE', "%{$userId}%")))
            ->orderBy('date_received', 'desc')
            ->get();

        $pendingCount = $pendingLogs->groupBy('slip_id')->count();
        return view('pages.users_view', [
            'users'  => User::orderBy('fname')->get(),
            'groups' => Group::orderBy('group_name')->get(),
            'offices' => $offices,
            'pendingCount' => $pendingCount,
        ]);
    }

    public function createUser(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'fname' => 'required',
            'lname' => 'required',
            'department' => 'required',
            'role' => 'required',
        ]);

        User::create([
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'fname' => $request->fname,
            'mname' => $request->mname,
            'lname' => $request->lname,
            'department' => $request->department,
            'role' => $request->role,
        ]);

        return redirect()->route('usersView')->with('success', 'User added successfully');
    }


    public function userEdit(Request $request, $id)
    {
        $user = User::findOrFail($id);
    $request->validate([
        'email' => 'nullable|email|unique:users,email,' . $id,
        'password' => 'nullable|min:6|confirmed',
        'fname' => 'nullable|string',
        'lname' => 'nullable|string',
    ]);
        // Only update if field is present in request
        $user->fname = $request->filled('fname') ? $request->fname : $user->fname;
        $user->mname = $request->filled('mname') ? $request->mname : $user->mname;
        $user->lname = $request->filled('lname') ? $request->lname : $user->lname;
        $user->email = $request->filled('email') ? $request->email : $user->email;
        $user->department = $request->filled('department') ? $request->department : $user->department;
        $user->role = $request->filled('role') ? $request->role : $user->role;

        // Only update password if a new one is provided
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return redirect()->route('usersView')->with('success', 'User updated successfully');
    }

    public function updateDpa(Request $request)
{
    /** @var \App\Models\User $user */
    $user = auth()->user();

    if (!$user) {
        return response()->json(['error' => 'Unauthenticated'], 401);
    }

    $user->dpa = $request->dpa === null ? null : 1;
    $user->save();

    return response()->json(['message' => 'DPA status updated.']);
}


    public function logsView()
    {
        $systemLogs = SystemLog::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $offices = Office::orderBy('office_name', 'asc')->get();
    $userId = auth()->id();
        $role   = auth()->user()->role;

        // Pending Count
        $pendingLogs = LogsTrans::where('trans_status', 1)
            ->when($role === 'staff', fn($q) => $q->where('r_users', 'LIKE', "%{$userId}%"))
            ->when(in_array($role, ['Administrator', 'records_officer']), fn($q) => $q->where(fn($q2) => $q2->where('creator_id', $userId)->orWhere('r_users', 'LIKE', "%{$userId}%")))
            ->orderBy('date_received', 'desc')
            ->get();

        $pendingCount = $pendingLogs->groupBy('slip_id')->count();
        return view('logs.all_logs', [
            'systemLogs' => $systemLogs,
            'users'  => User::orderBy('fname')->get(),
            'groups' => Group::orderBy('group_name')->get(),
            'offices' => $offices,
            'pendingCount' => $pendingCount,
        ]);
    }

    public function tranLogsView()
    {
        $userId = auth()->id();
        $role   = auth()->user()->role;

        // Pending Count
        $pendingLogs = LogsTrans::where('trans_status', 1)
            ->when($role === 'staff', fn($q) => $q->where('r_users', 'LIKE', "%{$userId}%"))
            ->when(in_array($role, ['Administrator', 'records_officer']), fn($q) => $q->where(fn($q2) => $q2->where('creator_id', $userId)->orWhere('r_users', 'LIKE', "%{$userId}%")))
            ->orderBy('date_received', 'desc')
            ->get();

        $pendingCount = $pendingLogs->groupBy('slip_id')->count();
        $logsRoute = LogsRoute::with([
                'creator',
                'routingSlip',
                'reassigned.creator',
                'reassigned.reassignedUser'
            ])
            ->orderBy('created_at', 'desc')
            ->get();

        $mergedLogs = collect();

        foreach ($logsRoute as $log) {

            // Resolve routed users to names
            $routedUserNames = collect();

            if ($log->routed_users) {
                $ids = explode(',', $log->routed_users);

                $routedUserNames = User::whereIn('id', $ids)
                    ->get()
                    ->map(fn ($u) => $u->fname . ' ' . $u->lname)
                    ->implode(', ');
            }

            // 1️⃣ Normal routing log
            $mergedLogs->push([
                'user'         => $log->creator,
                'text'         => $log->log_action,
                'file'         => $log->file,
                'rslip'        => optional($log->routingSlip)->rslip_id,
                'rslipObj'     => $log->routingSlip,      // ← Add this line!
                'routed_users' => $routedUserNames,
                'date'         => $log->created_at,
            ]);

            // 2️⃣ Re-route logs
            foreach ($log->reassigned as $reroute) {
                $mergedLogs->push([
                    'user'         => $reroute->creator,
                    'text'         => 'Re-routed document to ' .
                                    optional($reroute->reassignedUser)->fname . ' ' .
                                    optional($reroute->reassignedUser)->lname,
                    'file'         => $log->file,
                    'rslip'        => $reroute->rslip_id,
                    'rslipObj'     => $log->routingSlip,      // include the routingSlip too
                    'routed_users' => null,
                    'date'         => $reroute->created_at,
                ]);
            }
        }

        // Sort by date DESC
        $mergedLogs = $mergedLogs->sortByDesc('date')->values();

        return view('logs.trans_logs', [
            'logs'   => $mergedLogs,
            'users'  => User::orderBy('fname')->get(),
            'groups' => Group::orderBy('group_name')->get(),
            'pendingCount' => $pendingCount,
        ]);
    }


    public function managementLogs()
    {
        $managementLogs = ManagementLog::with('user')
            ->orderBy('created_at', 'desc')
            ->get();

        $offices = Office::orderBy('office_name', 'asc')->get();
    $userId = auth()->id();
        $role   = auth()->user()->role;

        // Pending Count
        $pendingLogs = LogsTrans::where('trans_status', 1)
            ->when($role === 'staff', fn($q) => $q->where('r_users', 'LIKE', "%{$userId}%"))
            ->when(in_array($role, ['Administrator', 'records_officer']), fn($q) => $q->where(fn($q2) => $q2->where('creator_id', $userId)->orWhere('r_users', 'LIKE', "%{$userId}%")))
            ->orderBy('date_received', 'desc')
            ->get();

        $pendingCount = $pendingLogs->groupBy('slip_id')->count();
        return view('logs.managementLogs', [
            'systemLogs' => $managementLogs,
            'users'  => User::orderBy('fname')->get(),
            'groups' => Group::orderBy('group_name')->get(),
            'offices' => $offices,
            'pendingCount' => $pendingCount,
        ]);
    }


    // Offices
    public function offices()
    {
        $user = auth()->user();
        $userRole = $user->role;

        // Only allow certain roles to access this page
        if (!in_array($userRole, ['Administrator', 'records_officer', 'super_user'])) {
            abort(403, 'Unauthorized');
        }

        $offices = Office::orderBy('office_name')->get();
        $groups  = Group::orderBy('group_name')->get();
        $users = User::orderBy('fname')->get();

        return view('pages.offices', compact('offices', 'groups','users'));
    }

    public function update(Request $request, Office $office)
    {
        $data = $request->only(['office_name', 'office_abbr']);
        $data = array_filter($data, fn($v) => !is_null($v));

        // Update office
        $office->update($data);

        // Build readable changes text
        $changes = [];

        if (isset($data['office_name'])) {
            $changes[] = 'Office Name: ' . $data['office_name'];
        }

        if (isset($data['office_abbr'])) {
            $changes[] = 'Office Abbreviation: ' . $data['office_abbr'];
        }

        // Save as plain text
        ManagementLog::create([
            'user_id'    => Auth::id(),
            'model_type' => 'Office',
            'model_id'   => $office->id,
            'action'     => 'updated',
            'changes'    => implode(', ', $changes), // ✅ STRING
        ]);


        return response()->json([
            'success' => true,
            'updated' => $data
        ]);
    }

    public function destroy(Office $office)
    {
        $office->delete();

        return response()->json(['success' => 'Office deleted successfully.']);
    }

    public function store(Request $request)
    {
        $request->validate([
            'office_name' => 'required|string|max:255',
            'office_abbr' => 'required|string|max:50',
        ]);

        $office = Office::create($request->only('office_name', 'office_abbr'));

        // Log creation
        ManagementLog::create([
            'user_id'    => Auth::id(),
            'model_type' => 'Office',
            'model_id'   => $office->id,
            'action'     => 'created',
            'changes'    => $office->toArray(),
        ]);

        return response()->json(['success' => 'Office added successfully.']);
    }

    public function addGroup(Request $request)
    {
        // Validate input
        $request->validate([
            'group_name' => 'required|string|max:255|unique:groups,group_name',
        ]);

        // Save to the database
        Group::create([
            'group_name' => $request->group_name,
        ]);

        return back()->with('success', 'Group added successfully.');
    }

    public function userGroups()
    {
        $user = auth()->user();
        $userRole = $user->role;

        // Access control similar to offices
        if (!in_array($userRole, ['Administrator', 'records_officer', 'super_user'])) {
            abort(403, 'Unauthorized');
        }

        $offices = Office::orderBy('office_name')->get();
        $groups  = Group::orderBy('group_name')->get();
        $users = User::orderBy('fname')->get();
    

        return view('pages.userGroups', compact(
            'groups','offices','users'));
    }

    // Update group
    public function updateGroup(Request $request, Group $group)
    {
        $data = $request->only(['group_name']);
        $data = array_filter($data, fn($v) => !is_null($v));

        // Update group
        $group->update($data);

        // Plain text change
        $changeText = 'Group Name: ' . $data['group_name'];

        ManagementLog::create([
            'user_id'    => Auth::id(),
            'model_type' => 'Group',
            'model_id'   => $group->id,
            'action'     => 'updated',
            'changes'    => $changeText,
        ]);


        return response()->json(['success' => true, 'message' => 'Group updated successfully.']);
    }

    // Delete group
    public function destroyGroup(Group $group)
    {
        $group->delete();

        return response()->json(['success' => 'Group deleted successfully.']);
    }

    // Store new group
    public function storeGroup(Request $request)
    {
        $request->validate([
            'group_name' => 'required|string|max:255',
        ]);

        $group = \App\Models\Group::create($request->only('group_name'));

        // Log creation
        ManagementLog::create([
            'user_id'    => Auth::id(),
            'model_type' => 'Group',
            'model_id'   => $group->id,
            'action'     => 'created',
            'changes'    => $group->toArray(),
        ]);
        return response()->json(['success' => 'Group added successfully.']);
    }

}
