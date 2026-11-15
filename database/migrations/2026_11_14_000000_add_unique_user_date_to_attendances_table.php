<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('attendances')
            ->select('user_id', 'date')
            ->groupBy('user_id', 'date')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            $rows = DB::table('attendances')
                ->where('user_id', $duplicate->user_id)
                ->where('date', $duplicate->date)
                ->orderBy('id')
                ->get();

            $keep = $rows->first();
            $checkIn = $rows->pluck('check_in')->filter()->sort()->first();
            $checkOut = $rows->pluck('check_out')->filter()->sort()->last();

            DB::table('attendances')->where('id', $keep->id)->update([
                'check_in' => $checkIn,
                'check_out' => $checkOut,
            ]);

            DB::table('attendances')
                ->where('user_id', $duplicate->user_id)
                ->where('date', $duplicate->date)
                ->where('id', '!=', $keep->id)
                ->delete();
        }

        Schema::table('attendances', function (Blueprint $table) {
            $table->unique(['user_id', 'date'], 'attendances_user_date_unique');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique('attendances_user_date_unique');
        });
    }
};
