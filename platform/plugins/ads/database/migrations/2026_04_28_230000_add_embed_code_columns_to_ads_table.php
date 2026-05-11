<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ads', function (Blueprint $table): void {
            if (! Schema::hasColumn('ads', 'embed_provider')) {
                $table->string('embed_provider', 60)->nullable()->after('google_adsense_slot_id');
            }

            if (! Schema::hasColumn('ads', 'embed_code')) {
                $table->longText('embed_code')->nullable()->after('embed_provider');
            }

            if (! Schema::hasColumn('ads', 'embed_size')) {
                $table->string('embed_size', 30)->nullable()->after('embed_code');
            }

            if (! Schema::hasColumn('ads', 'embed_width')) {
                $table->unsignedInteger('embed_width')->nullable()->after('embed_size');
            }

            if (! Schema::hasColumn('ads', 'embed_height')) {
                $table->unsignedInteger('embed_height')->nullable()->after('embed_width');
            }
        });
    }

    public function down(): void
    {
        Schema::table('ads', function (Blueprint $table): void {
            $columns = [
                'embed_provider',
                'embed_code',
                'embed_size',
                'embed_width',
                'embed_height',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('ads', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
