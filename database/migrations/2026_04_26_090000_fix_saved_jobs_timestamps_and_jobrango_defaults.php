<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        if (Schema::hasTable('jb_saved_jobs')) {
            Schema::table('jb_saved_jobs', function (Blueprint $table): void {
                if (! Schema::hasColumn('jb_saved_jobs', 'created_at')) {
                    $table->timestamp('created_at')->nullable()->after('job_id');
                }

                if (! Schema::hasColumn('jb_saved_jobs', 'updated_at')) {
                    $table->timestamp('updated_at')->nullable()->after('created_at');
                }
            });

            DB::table('jb_saved_jobs')
                ->whereNull('created_at')
                ->update([
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
        }

        if (! Schema::hasTable('jb_currencies')) {
            return;
        }

        DB::table('jb_currencies')->where('title', 'NGN')->update([
            'is_default' => 1,
            'updated_at' => $now,
        ]);

        DB::table('jb_currencies')
            ->where('title', '!=', 'NGN')
            ->update([
                'is_default' => 0,
                'updated_at' => $now,
            ]);

        if (Schema::hasTable('settings')) {
            DB::table('settings')->updateOrInsert(
                ['key' => 'job_board_enable_auto_detect_visitor_currency'],
                ['value' => '0', 'created_at' => $now, 'updated_at' => $now]
            );
        }
    }

    public function down(): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        $now = now();

        DB::table('settings')->updateOrInsert(
            ['key' => 'job_board_enable_auto_detect_visitor_currency'],
            ['value' => '1', 'created_at' => $now, 'updated_at' => $now]
        );
    }
};
