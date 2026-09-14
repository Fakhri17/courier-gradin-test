<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('couriers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->string('email')->nullable()->unique();
            $table->unsignedTinyInteger('level');
            $table->string('vehicle_type')->nullable();
            $table->string('vehicle_plate_number')->nullable();
            $table->text('address')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->date('registered_at');
            $table->timestamps();

            $table->index('name');
            $table->index('level');
            $table->index('registered_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('couriers');
    }
};
