<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('jb_jobs')) {
            return;
        }

        $now = now();

        $jobs = DB::table('jb_jobs')
            ->where('status', 'published')
            ->where(function ($query) use ($now): void {
                $query
                    ->whereNull('expire_date')
                    ->orWhere('expire_date', '<', $now)
                    ->orWhereNull('application_closing_date')
                    ->orWhere('application_closing_date', '<', $now);
            })
            ->select('id')
            ->orderBy('id')
            ->get();

        foreach ($jobs as $index => $job) {
            $closingDate = $now->copy()->addDays(14 + ($index % 10));
            $expireDate = $closingDate->copy()->addDays(30);

            DB::table('jb_jobs')
                ->where('id', $job->id)
                ->update([
                    'application_closing_date' => $closingDate,
                    'expire_date' => $expireDate,
                    'updated_at' => $now,
                ]);
        }
    }

    public function down(): void
    {
        // Demo date normalization is intentionally not rolled back.
    }
};
