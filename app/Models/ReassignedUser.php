<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReassignedUser extends Model
{
    use HasFactory;

    protected $table = 'reassigned_users'; // 👈 MUST match table name

    protected $fillable = [
    'rslip_id',
    'slip_id',
    'creator_id',
    'reassigned_id',
    'status',
    'original_creator_id', 
];

 public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function reassignedUser()
    {
        return $this->belongsTo(User::class, 'reassigned_id');
    }
    public function originalCreator()
{
    return $this->belongsTo(User::class, 'original_creator_id');
}
}