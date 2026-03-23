<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id',
    'action',
    'action_type',
    'ip_address',
    'user_agent',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
