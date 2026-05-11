<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('jb_applications', function (Blueprint $table): void {
            if (! Schema::hasColumn('jb_applications', 'screening_status')) {
                $table->string('screening_status', 60)->nullable()->after('status');
            }

            if (! Schema::hasColumn('jb_applications', 'screening_summary')) {
                $table->json('screening_summary')->nullable()->after('screening_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jb_applications', function (Blueprint $table): void {
            foreach (['screening_summary', 'screening_status'] as $column) {
                if (Schema::hasColumn('jb_applications', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
