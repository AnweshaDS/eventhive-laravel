<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('ticket_type_id')->constrained('ticket_types')->onDelete('cascade');
                $table->integer('quantity')->default(1);
                $table->decimal('total_amount', 10, 2);
                $table->enum('payment_status', ['pending', 'paid', 'refunded'])->default('pending');
                $table->enum('booking_status', ['confirmed', 'cancelled'])->default('confirmed');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};