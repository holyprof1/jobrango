<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::table('jb_jobs', function (Blueprint $table): void {
            if (! Schema::hasColumn('jb_jobs', 'application_mode')) {
                $table->string('application_mode', 20)->default('basic')->after('external_apply_behavior');
            }

            if (! Schema::hasColumn('jb_jobs', 'application_form_schema')) {
                $table->json('application_form_schema')->nullable()->after('application_mode');
            }

            if (! Schema::hasColumn('jb_jobs', 'application_form_settings')) {
                $table->json('application_form_settings')->nullable()->after('application_form_schema');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jb_jobs', function (Blueprint $table): void {
            $columns = array_filter([
                Schema::hasColumn('jb_jobs', 'application_form_settings') ? 'application_form_settings' : null,
                Schema::hasColumn('jb_jobs', 'application_form_schema') ? 'application_form_schema' : null,
                Schema::hasColumn('jb_jobs', 'application_mode') ? 'application_mode' : null,
            ]);

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
