<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InterOfficeLog extends Model
{
    use HasFactory;

    protected $table = 'inter_office_logs';

    protected $fillable = [
        'track_slip',
        'creator_id',
        'user_id',
        'remarks',
        'track_status',
        'view_status',
        'view_date',
    ];

   
    public function interOffice()
    {
        return $this->belongsTo(InterOffice::class, 'track_slip', 'track_slip');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}