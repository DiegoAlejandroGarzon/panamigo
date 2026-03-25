<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ZReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'z_number',
        'report_date',
        'start_order_id',
        'end_order_id',
        'total_sales',
        'order_count',
        'total_corrections',
        'corrections_count',
        'user_id',
        'category_summary'
    ];

    protected $casts = [
        'category_summary' => 'array',
        'report_date' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function startOrder()
    {
        return $this->belongsTo(Order::class, 'start_order_id');
    }

    public function endOrder()
    {
        return $this->belongsTo(Order::class, 'end_order_id');
    }
}
