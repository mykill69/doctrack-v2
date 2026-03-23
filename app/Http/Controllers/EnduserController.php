<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Group;
use App\Models\Office;
use App\Models\Esig;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;




class EnduserController extends Controller
{
    public function userPassword($id)
    
{
    
    if (auth()->user()->role === 'Administrator') {
            return redirect()->back()->with('error', 'Administrators do not have access to this page.');
        }

    $user = auth()->user();

    $offices = Office::orderBy('office_name', 'asc')->get();

    $userEsig = Esig::where('user_id', $user->id)->first();

    return view('enduser.changepass', [
        'user'     => $user,
        'offices'  => $offices,
        'userEsig' => $userEsig,
        'users'    => User::orderBy('fname')->get(),   
        'groups'   => Group::orderBy('group_name')->get(), 
    ]);
}


public function passChange(Request $request, $id)
{
    $validator = Validator::make($request->all(), [
        'email' => 'nullable|string|max:255|unique:users,email,' . $id,
        'password' => 'nullable|string|min:8|confirmed',
        'department' => 'nullable|string|max:255',
        'esig_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    $user = User::find($id);
    if (!$user) {
        return redirect()->back()->with('error', 'User not found');
    }

    // ✅ Update password if provided
    if ($request->filled('password')) {
        $user->password = Hash::make($request->password);
    }

    // ✅ Update department if provided
    if ($request->filled('department')) {
        $user->department = $request->department;
    }

    // ✅ Handle E-signature upload
    // if ($request->hasFile('esig_file')) {
    //     $file = $request->file('esig_file');
    //     $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    //     $extension = $file->getClientOriginalExtension();
    //     $filename = $user->fname . '_' . $originalName . '.' . $extension;

    //     $i = 1;
    //     $storagePath = storage_path('app/esignature');

    //     while (file_exists($storagePath . '/' . $filename)) {
    //         $filename = $user->fname . '_' . $originalName . ' Copy ' . $i . '.' . $extension;
    //         $i++;
    //     }

    //     // Delete old file if exists
    //     $existingEsig = Esig::where('user_id', $user->id)->first();
    //     if ($existingEsig && $existingEsig->esig_file) {
    //         $oldPath = $storagePath . '/' . $existingEsig->esig_file;
    //         if (file_exists($oldPath)) {
    //             unlink($oldPath);
    //         }
    //     }

    //     // Save new file
    //     $file->storeAs('esignature', $filename);

    //     Esig::updateOrCreate(
    //         ['user_id' => $user->id],
    //         ['esig_file' => $filename]
    //     );
    // }


if ($request->hasFile('esig_file')) {
    $file = $request->file('esig_file');

    // Read raw bytes
    $raw = file_get_contents($file->getRealPath());

    // Convert to text-safe payload then encrypt (avoids null-byte edge cases)
    $encrypted = Crypt::encryptString(base64_encode($raw)); 

    // Create a non-guessable encrypted filename
    $encName = (string) Str::uuid() . '.enc';

    // Delete old encrypted file if exists
    $existingEsig = Esig::where('user_id', $user->id)->first();
    if ($existingEsig && $existingEsig->esig_file) {
        Storage::disk('esignature')->delete($existingEsig->esig_file);
    }

    // Save encrypted content to private disk
    Storage::disk('esignature')->put($encName, $encrypted);

    // Save metadata
    Esig::updateOrCreate(
        ['user_id' => $user->id],
        [
            'esig_file' => $encName,
            'esig_mime' => $file->getMimeType(),          
            'esig_ext'  => $file->getClientOriginalExtension(), 
        ]
    );
}



    // ✅ Save changes to user
    $user->save();

    return redirect()->route('userPassword', ['id' => $id])
        ->with('success', 'User updated successfully.');
}



public function showEsig(User $user)
{
    // Allow only the owner (or add role checks if needed)
    if (auth()->id() !== $user->id) {
        abort(403);
    }

    $esig = Esig::where('user_id', $user->id)->firstOrFail();

    // Read encrypted payload from private disk
    $ciphertext = Storage::disk('esignature')->get($esig->esig_file);

    // Decrypt and decode back to raw bytes
    $raw = base64_decode(Crypt::decryptString($ciphertext)); 

    $mime = $esig->esig_mime ?? 'application/octet-stream';
    $ext  = $esig->esig_ext ?? 'bin';

    return response($raw, 200)
        ->header('Content-Type', $mime)
        ->header('Content-Disposition', 'inline; filename="esignature.' . $ext . '"')
        ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
}


}
