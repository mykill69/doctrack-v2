<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogsTrans extends Model
{
    use HasFactory;

    protected $table = 'logs_trans';

    protected $fillable = [
        'slip_id',
        'rslip_id',
        'creator_id',
        'source',
        'subject',
        'trans_remarks',
        'other_remarks',
        'ass_comment',
        'r_users',
        'reassigned_to',
        'file',
        'purge_status',
        'trans_status',
        'date_received',
        'transaction_type',
    ];

    /* ================= Relationships ================= */

    public function routingSlip()
    {
        return $this->belongsTo(RoutingSlip::class, 'slip_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
    public function transactionLogs()
    {
        return $this->hasMany(LogsTrans::class, 'slip_id');
    }
    public function routedUser()
{
    return $this->belongsTo(User::class, 'r_users');
}
    
}
