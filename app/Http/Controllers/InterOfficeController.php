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
use App\Models\InterOffice;
use App\Models\InterOfficeLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\DoctrackNotification;

class InterOfficeController extends Controller
{
    public function interOffice()
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


    // ✅ Latest first
$interOffices = InterOffice::where(function ($query) use ($userId) {
    $query->where('creator_id', $userId)
          ->orWhereRaw("FIND_IN_SET(?, user_id)", [$userId]); // checks if user_id column contains this user
})
->orderBy('created_at', 'desc')
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
    return view('pages.inter-office', [
        'users'        => User::orderBy('fname')->get(),
        'groups'       => Group::orderBy('group_name')->get(),
        'interOffices' => $interOffices, // pass to view
        'pendingCount'   => $pendingCount,
        'interOfficeCount' => $interOfficeCount, // pass count to view
    ]);
}

public function storeInterOffice(Request $request)
{
    $request->validate([
        'user_ids'   => 'required|array',
        'user_ids.*' => 'exists:users,id',
        'trans_type' => 'required|string',
        'subject'    => 'required|string',
        'file'       => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png',
        'remarks'    => 'nullable|string',
    ]);

    DB::transaction(function () use ($request) {

    // ✅ Generate random 8-digit number prefixed with DTS
    $trackSlip = 'DTS' . mt_rand(10000000, 99999999);

    $filePath = null;

    // -----------------------------
    // HANDLE FILE
    // -----------------------------
    if ($request->hasFile('file')) {
        $file = $request->file('file');

        $originalName = $file->getClientOriginalName();
        $filename     = pathinfo($originalName, PATHINFO_FILENAME);
        $extension    = $file->getClientOriginalExtension();

        $directory = 'inter_office_files';
        $newName   = $originalName;
        $counter   = 1;

        while (Storage::disk('public')->exists($directory . '/' . $newName)) {
            $suffix  = $counter === 1 ? ' (copy)' : ' (copy ' . $counter . ')';
            $newName = $filename . $suffix . '.' . $extension;
            $counter++;
        }

        $filePath = $file->storeAs($directory, $newName, 'public');
    }

    // -----------------------------
    // SAVE INTER OFFICE
    // -----------------------------
    $interOffice = InterOffice::create([
        'track_slip'   => $trackSlip,
        'creator_id'   => auth()->id(),
        'user_id'      => implode(',', $request->user_ids),
        'trans_type'   => $request->trans_type,
        'subject'      => $request->subject,
        'file'         => $filePath,
        'track_status' => 1,
    ]);

    // -----------------------------
    // SAVE LOGS & SEND EMAIL
    // -----------------------------
    foreach ($request->user_ids as $userId) {

        InterOfficeLog::create([
            'track_slip'   => $trackSlip,
            'creator_id'   => auth()->id(),
            'user_id'      => $userId,
            'remarks'      => $request->remarks,
            'track_status' => 1,
            'view_status'  => 0,
            'view_date'    => null,
        ]);

        // ✅ Send email to the user
        $user = User::find($userId);

        if ($user && $user->email) {
            Mail::to($user->email)->send(
                new DoctrackNotification($interOffice, $user->fname . ' ' . $user->lname)
            );
        }
    }
});

    return redirect()
        ->back()
        ->with('success', 'Inter-office transaction saved and routed.');
}


public function viewInterOffice($track_slip)
{
    $userId = auth()->id();

    // Get logs for this transaction
    $logs = InterOfficeLog::where('track_slip', $track_slip)
        ->where(function ($query) use ($userId) {
            $query->whereHas('interOffice', function ($q) use ($userId) {
                $q->where('creator_id', $userId);
            })
            ->orWhere('user_id', $userId);
        })
        ->orderBy('created_at', 'asc')
        ->get();

    // If user is not allowed
    if ($logs->isEmpty()) {
        abort(403, 'Unauthorized access');
    }

    // Get main inter-office record
    $interOffice = $logs->first()->interOffice;

    return view('pages.inter-office-view', [
        'users'        => User::orderBy('fname')->get(),
        'groups'       => Group::orderBy('group_name')->get(),
        'interOffice' => $interOffice,
        'logs'        => $logs,
    ]);
}

public function updateStatus(Request $request, $id)
{
    $request->validate([
        'track_status' => 'required|integer|in:2,3',
        'remarks' => 'nullable|string',
    ]);

    $log = InterOfficeLog::findOrFail($id);

    $log->update([
        'track_status' => $request->track_status,
        'remarks'      => $request->remarks ?? $log->remarks,
        'view_status'  => 1,
        'view_date'    => now(),
    ]);

    return redirect()->back()->with('success', 'Updated successfully.');
}

public function returnLog(Request $request, $id)
{
    $request->validate([
        'remarks' => 'required|string',
    ]);

    $log = InterOfficeLog::find($id);

    if (!$log) {
        return response()->json(['success' => false, 'message' => 'Log not found'], 404);
    }

    $log->update([
        'track_status' => 4,
        'remarks'      => $request->remarks,
        'view_status'  => 1,
        'view_date'    => now(),
    ]);

    return response()->json(['success' => true]);
}

public function addEntry(Request $request)
{
    $request->validate([
        'inter_office_id' => 'required|exists:inter_office,id',
        'user_id'        => 'required|array',
        'user_id.*'      => 'exists:users,id',
        'file'           => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png',
    ]);

    $interOffice = InterOffice::findOrFail($request->inter_office_id);

    /*
    |------------------------------------------
    | MERGE EXISTING + NEW USERS
    |------------------------------------------
    */

    $existingIds = $interOffice->user_id
        ? explode(',', $interOffice->user_id)
        : [];

    $newIds = $request->user_id;

    $mergedIds = array_unique(array_merge($existingIds, $newIds));

    $interOffice->update([
        'user_id' => implode(',', $mergedIds)
    ]);

    /*
    |------------------------------------------
    | FILE UPLOAD
    |------------------------------------------
    */

    if ($request->hasFile('file')) {

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $filename = pathinfo($originalName, PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();

        $directory = 'inter_office_files';
        $newName = $originalName;
        $counter = 1;

        while (Storage::disk('public')->exists($directory.'/'.$newName)) {
            $suffix = $counter === 1 ? ' (copy)' : ' (copy '.$counter.')';
            $newName = $filename.$suffix.'.'.$extension;
            $counter++;
        }

        $filePath = $file->storeAs($directory, $newName, 'public');

        $interOffice->update([
            'file' => $filePath
        ]);
    }

    /*
    |------------------------------------------
    | CREATE LOGS FOR NEW USERS ONLY
    |------------------------------------------
    */

    foreach ($newIds as $userId) {

        $exists = $interOffice->logs()
            ->where('user_id', $userId)
            ->exists();

        if (!$exists) {

            $interOffice->logs()->create([
                'track_slip'   => $interOffice->track_slip,
                'creator_id'   => auth()->id(),
                'user_id'      => $userId,
                'remarks'      => '',
                'track_status' => 1,
                'view_status'  => 0,
                'view_date'    => null,
            ]);
        }
    }

    return redirect()->back()->with('success', 'Entry added successfully.');
}

}
