<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('daily_stocks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained()->restrictOnDelete();
            $table->date('date');
            $table->unsignedInteger('stock');
            $table->unsignedInteger('sold')->default(0);
            $table->timestamps();

            $table->unique(['menu_id', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('daily_stocks');
    }
};