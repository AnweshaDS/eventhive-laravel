<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('qr_tickets')) {
            Schema::create('qr_tickets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');
                $table->string('qr_code');
                $table->string('verify_token')->unique();
                $table->enum('scan_status', ['unused', 'scanned'])->default('unused');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('qr_tickets');
    }
};