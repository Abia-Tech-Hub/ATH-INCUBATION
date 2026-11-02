<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('service');
            $table->string('day');   // YYYY-MM-DD
            $table->string('time');  // HH:MM
            $table->string('name');
            $table->string('phone', 30);
            $table->string('email');
            $table->string('status')->default('confirmed');
            $table->timestamps();

            $table->index(['day', 'time']);
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
