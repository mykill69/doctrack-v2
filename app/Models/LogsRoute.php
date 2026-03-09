<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogsRoute extends Model
{
    use HasFactory;

    protected $table = 'logs_route';

    protected $fillable = [
        'slip_id',
        'rslip_id',
        'log_creator',
        'log_action',
        'file',
        'routed_users',
    ];

    /* ================= Relationships ================= */

    public function routingSlip()
    {
        return $this->belongsTo(RoutingSlip::class, 'slip_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'log_creator');
    }
    
// 👇 ADD THIS
   public function reassigned()
{
    return $this->hasMany(
        ReassignedUser::class,
        'slip_id',
        'slip_id'
    );
}

}
