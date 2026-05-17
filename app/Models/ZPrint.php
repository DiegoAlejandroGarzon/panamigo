<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ZPrint extends Model
{
    protected $fillable = [
        'print_date',
        'reported_total',
        'reported_count',
        'user_id',
    ];

    protected $casts = [
        'print_date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
