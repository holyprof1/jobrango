<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('jb_jobs', function (Blueprint $table): void {
            if (! Schema::hasColumn('jb_jobs', 'is_remote')) {
                $table->boolean('is_remote')->default(false)->after('application_closing_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jb_jobs', function (Blueprint $table): void {
            if (Schema::hasColumn('jb_jobs', 'is_remote')) {
                $table->dropColumn('is_remote');
            }
        });
    }
};
