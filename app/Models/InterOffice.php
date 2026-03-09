<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterOffice extends Model
{
    use HasFactory;

    protected $table = 'inter_office';

    protected $fillable = [
        'track_slip','creator_id','user_id','trans_type','subject','file','track_status'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    // Return assigned users as a collection
    public function assignedUsers()
    {
        $ids = explode(',', $this->user_id); // split string into array
        return User::whereIn('id', $ids)->get();
    }

    public function logs()
    {
        return $this->hasMany(InterOfficeLog::class, 'track_slip', 'track_slip');
    }
}