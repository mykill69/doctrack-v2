<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ManagementLog extends Model
{
    use HasFactory;

    protected $fillable = [
            'user_id',
            'model_type',
            'model_id',
            'action',
            'changes',
        ];

        protected $casts = [
            'changes' => 'array', // JSON -> array
        ];

        public function user()
        {
            return $this->belongsTo(User::class);
        }
}
