<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoutingPdf extends Model
{
    protected $table = 'routing_pdf';

    protected $fillable = [
        'routing_slip_id',
        'rslip_id',
        'op_ctrl',
        'creator_id',
        'pres_id',
        'pres_dept',
        'trans_remarks',
        'other_remarks',
        'routed_users',
        'reassigned_to',
        'routing_action',
        'date_received',
    ];

    public function slip()
{
    return $this->belongsTo(RoutingSlip::class, 'routing_slip_id', 'id');
}

}