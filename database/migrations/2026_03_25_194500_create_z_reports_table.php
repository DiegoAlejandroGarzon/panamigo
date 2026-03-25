<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('z_reports', function (Blueprint $table) {
            $table->id();
            $table->integer('z_number')->unique();
            $table->date('report_date');
            $table->unsignedBigInteger('start_order_id');
            $table->unsignedBigInteger('end_order_id');
            $table->decimal('total_sales', 15, 2);
            $table->integer('order_count');
            $table->decimal('total_corrections', 15, 2)->default(0);
            $table->integer('corrections_count')->default(0);
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('z_reports');
    }
};
