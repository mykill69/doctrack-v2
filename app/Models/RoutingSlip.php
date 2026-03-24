<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoutingSlip extends Model
{
    use HasFactory;

    protected $table = 'routing_slip';

protected $fillable = [
    'rslip_id',
    'op_ctrl',
    'creator_id',
    'pres_dept',
    'source',
    'subject',
    'trans_remarks',
    'other_remarks',
    'ass_comment',
    'set_users_to',
    'routed_users',
    'reassigned_to',
    'file',
    'purge_status',
    'routing_status',
    'date_received',
    'validity',
    'validity_status',
    'transaction_type',
];

public function creator()
{
    return $this->belongsTo(User::class, 'creator_id');
}

}
