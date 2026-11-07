<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->string('code')->unique();
            $table->string('order_type');
            $table->string('qr_token')->nullable()->unique();
            $table->string('status')->index();
            $table->unsignedInteger('total');
            $table->dateTime('expires_at');
            $table->dateTime('paid_at')->nullable();
            $table->dateTime('scanned_at')->nullable();
            $table->dateTime('done_at')->nullable();
            $table->dateTime('picked_up_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'scanned_at']);
            $table->index('paid_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};