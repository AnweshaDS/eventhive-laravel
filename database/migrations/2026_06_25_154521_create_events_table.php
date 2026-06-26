<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('events')) {
            Schema::create('events', function (Blueprint $table) {
                $table->id();
                $table->foreignId('organizer_id')->constrained('users')->onDelete('cascade');
                $table->string('title');
                $table->text('description')->nullable();
                $table->string('category')->nullable();
                $table->string('venue')->nullable();
                $table->string('city')->nullable();
                $table->dateTime('event_date');
                $table->dateTime('event_end')->nullable();
                $table->string('banner_image')->nullable();
                $table->enum('status', ['draft', 'published', 'cancelled', 'completed'])->default('draft');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};