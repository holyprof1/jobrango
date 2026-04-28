<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('jb_applications', function (Blueprint $table): void {
            if (! Schema::hasColumn('jb_applications', 'application_answers')) {
                $table->json('application_answers')->nullable()->after('cover_letter');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jb_applications', function (Blueprint $table): void {
            if (Schema::hasColumn('jb_applications', 'application_answers')) {
                $table->dropColumn('application_answers');
            }
        });
    }
};
