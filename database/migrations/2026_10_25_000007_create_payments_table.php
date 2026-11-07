<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('gateway_order_id')->unique(); // order_id di Midtrans, unik per percobaan bayar
            $table->string('transaction_id')->nullable();
            $table->string('status')->default('pending'); // pending, paid, failed, expired
            $table->unsignedInteger('gross_amount');
            $table->text('qr_string')->nullable();
            $table->text('qr_url')->nullable();
            $table->dateTime('expires_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};