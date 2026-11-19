<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach (DB::select("select tablename from pg_tables where schemaname = 'public'") as $row) {
            DB::statement('alter table "public"."'.$row->tablename.'" enable row level security');
        }
    }

    public function down(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            return;
        }

        foreach (DB::select("select tablename from pg_tables where schemaname = 'public'") as $row) {
            DB::statement('alter table "public"."'.$row->tablename.'" disable row level security');
        }
    }
};