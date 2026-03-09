<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\LogsTrans;
use App\Models\User;
use App\Models\Group;
use App\Models\LogsRoute;
use App\Models\ReassignedUser;  
use App\Models\RoutingSlip;
use Illuminate\Support\Facades\Log;


class RoutingTimelineController extends Controller
{

public function routingTimeline($id, Request $request)
{
    $slip_id = $request->query('slip_id');

    $log = LogsTrans::where('id', $id)
                    ->where('slip_id', $slip_id)
                    ->firstOrFail();

    $logs = LogsTrans::where('slip_id', $slip_id)->orderBy('created_at')->get();

    return view('timeline.routingTimeline', [
        'logs'   => $logs,
        'log'    => $log,
        'users'  => User::orderBy('fname')->get(),
        'groups' => Group::orderBy('group_name')->get(),
    ]);
}

public function searchTimeline(Request $request)
{
    $query = $request->input('query');
    $currentUser = auth()->user();

    // Base query
    $logsQuery = LogsTrans::with('creator');

    // Restrict access for non-admin users
    if ($currentUser->role !== 'Administrator') {
        $logsQuery->where(function($q) use ($currentUser) {
            $q->where('creator_id', $currentUser->id) // logs created by current user
              ->orWhereRaw("FIND_IN_SET(?, r_users)", [$currentUser->id]); // or where user is in r_users
        });
    }

    // Apply search query if exists
    if ($query) {
    $logsQuery->where(function($q) use ($query) {
        $q->where('rslip_id', $query) // exact match for rslip_id
          ->orWhere('trans_remarks', 'like', "%{$query}%"); // partial match for remarks
    });
}

    $logs = $logsQuery->orderBy('created_at', 'desc')->get();
    $log = $logs->first();

    // Return view with a flag to show access denied if no logs
    return view('timeline.routingTimeline', [
        'logs'   => $logs,
        'log'    => $log,
        'users'  => User::orderBy('fname')->get(),
        'groups' => Group::orderBy('group_name')->get(),
        'accessDenied' => $logs->isEmpty() && $currentUser->role !== 'Administrator',
    ]);
}

public function acknowledgeLog(Request $request)
{
    $logId = $request->log_id;
    $userId = auth()->id();

    // Find the log
    $log = LogsTrans::findOrFail($logId);

    // Update trans_status
    $log->trans_status = 3;
    $log->save();

    LogsRoute::create([
        'slip_id'     => $log->slip_id,
        'rslip_id'    => $log->rslip_id,
        'log_creator' => $userId,
        'log_action'  => 'Acknowledged the document',
        'file'        => $log->file,
        'routed_users'=> null,
    ]);

    return response()->json(['success' => true, 'message' => 'Document acknowledged']);
}

public function rerouteLog(Request $request)
{
    $request->validate([
        'log_id'        => 'required|exists:logs_trans,id',
        'slip_id'       => 'required|exists:routing_slip,id',
        'routed_to'     => 'required|array',
        'routed_to.*'   => 'exists:users,id',
        'comments'      => 'nullable|string',
    ]);

    DB::beginTransaction();

    try {

        // 1️⃣ Update logs_trans
        $log = LogsTrans::where('id', $request->log_id)
                        ->where('slip_id', $request->slip_id)
                        ->firstOrFail();

        $log->update([
            'ass_comment'   => $request->comments,
            'reassigned_to' => implode(',', $request->routed_to),
            'trans_status'  => 2,
        ]);

        // 2️⃣ Update routing_slip (FLAG + COMMENT)
        RoutingSlip::where('id', $request->slip_id)->update([
            'routing_status' => 2,
            'reassigned_to'  => 1, // flag
            'ass_comment'    => $request->comments,
        ]);

        // 3️⃣ Insert reassigned_users (MULTIPLE ROWS)
        $userId = auth()->id();

        foreach ($request->routed_to as $assignedUserId) {
            ReassignedUser::create([
                'rslip_id'      => $log->rslip_id,
                'slip_id'       => $log->slip_id,
                'creator_id'    => $userId,
                'reassigned_id' => $assignedUserId,
                'status'        => 1,
            ]);
        }

        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Document successfully re-routed.',
        ]);

    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Reroute failed: '.$e->getMessage());

        return response()->json([
            'success' => false,
            'message' => 'Something went wrong.',
        ], 500);
    }
}





}
