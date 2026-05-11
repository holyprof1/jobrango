<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jb_jobs', function (Blueprint $table): void {
            if (! Schema::hasColumn('jb_jobs', 'source_name')) {
                $table->string('source_name', 120)->nullable()->after('unique_id');
            }

            if (! Schema::hasColumn('jb_jobs', 'source_type')) {
                $table->string('source_type', 80)->nullable()->after('source_name');
            }

            if (! Schema::hasColumn('jb_jobs', 'source_job_id')) {
                $table->string('source_job_id', 191)->nullable()->after('source_type');
            }

            if (! Schema::hasColumn('jb_jobs', 'source_url')) {
                $table->text('source_url')->nullable()->after('source_job_id');
            }

            if (! Schema::hasColumn('jb_jobs', 'source_payload_hash')) {
                $table->string('source_payload_hash', 64)->nullable()->after('source_url');
            }

            if (! Schema::hasColumn('jb_jobs', 'imported_at')) {
                $table->timestamp('imported_at')->nullable()->after('source_payload_hash');
            }

            if (! Schema::hasColumn('jb_jobs', 'last_synced_at')) {
                $table->timestamp('last_synced_at')->nullable()->after('imported_at');
            }

            if (! Schema::hasColumn('jb_jobs', 'source_updated_at')) {
                $table->timestamp('source_updated_at')->nullable()->after('last_synced_at');
            }
        });

        Schema::table('jb_companies', function (Blueprint $table): void {
            if (! Schema::hasColumn('jb_companies', 'source_name')) {
                $table->string('source_name', 120)->nullable()->after('unique_id');
            }

            if (! Schema::hasColumn('jb_companies', 'source_company_id')) {
                $table->string('source_company_id', 191)->nullable()->after('source_name');
            }

            if (! Schema::hasColumn('jb_companies', 'imported_at')) {
                $table->timestamp('imported_at')->nullable()->after('source_company_id');
            }

            if (! Schema::hasColumn('jb_companies', 'last_synced_at')) {
                $table->timestamp('last_synced_at')->nullable()->after('imported_at');
            }
        });

        Schema::table('jb_jobs', function (Blueprint $table): void {
            $table->index(['source_name', 'source_job_id'], 'jb_jobs_source_lookup_index');
            $table->index(['source_name', 'last_synced_at'], 'jb_jobs_source_synced_index');
        });

        Schema::table('jb_companies', function (Blueprint $table): void {
            $table->index(['source_name', 'source_company_id'], 'jb_companies_source_lookup_index');
        });
    }

    public function down(): void
    {
        Schema::table('jb_jobs', function (Blueprint $table): void {
            if (Schema::hasIndex('jb_jobs', 'jb_jobs_source_lookup_index')) {
                $table->dropIndex('jb_jobs_source_lookup_index');
            }

            if (Schema::hasIndex('jb_jobs', 'jb_jobs_source_synced_index')) {
                $table->dropIndex('jb_jobs_source_synced_index');
            }
        });

        Schema::table('jb_companies', function (Blueprint $table): void {
            if (Schema::hasIndex('jb_companies', 'jb_companies_source_lookup_index')) {
                $table->dropIndex('jb_companies_source_lookup_index');
            }
        });

        Schema::table('jb_jobs', function (Blueprint $table): void {
            $columns = [
                'source_name',
                'source_type',
                'source_job_id',
                'source_url',
                'source_payload_hash',
                'imported_at',
                'last_synced_at',
                'source_updated_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('jb_jobs', $column)) {
                    $table->dropColumn($column);
                }
            }
        });

        Schema::table('jb_companies', function (Blueprint $table): void {
            $columns = [
                'source_name',
                'source_company_id',
                'imported_at',
                'last_synced_at',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('jb_companies', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
