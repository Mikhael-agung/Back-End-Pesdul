<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_type_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('old_type');
            $table->string('new_type');
            $table->timestamp('changed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_type_logs');
    }
};