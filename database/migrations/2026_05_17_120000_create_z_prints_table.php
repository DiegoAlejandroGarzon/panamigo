<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('z_prints', function (Blueprint $table) {
            $table->id();
            $table->date('print_date');
            $table->decimal('reported_total', 15, 2);
            $table->integer('reported_count');
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('z_prints');
    }
};
