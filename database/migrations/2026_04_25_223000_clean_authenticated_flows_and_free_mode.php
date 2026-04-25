<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $this->upsertSetting('job_board_enable_credits_system', '0', $now);

        if (Schema::hasTable('menu_nodes')) {
            DB::table('menu_nodes')
                ->where('menu_id', 1)
                ->where('url', '/login')
                ->delete();
        }
    }

    public function down(): void
    {
        $now = now();

        $this->upsertSetting('job_board_enable_credits_system', '1', $now);

        if (
            Schema::hasTable('menu_nodes')
            && ! DB::table('menu_nodes')->where('menu_id', 1)->where('url', '/login')->exists()
        ) {
            DB::table('menu_nodes')->insert([
                'menu_id' => 1,
                'parent_id' => 0,
                'reference_id' => null,
                'reference_type' => null,
                'url' => '/login',
                'icon_font' => null,
                'position' => 4,
                'title' => 'Sign In',
                'css_class' => null,
                'target' => '_self',
                'has_child' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    private function upsertSetting(string $key, string $value, $now): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        DB::table('settings')->updateOrInsert(
            ['key' => $key],
            ['value' => $value, 'created_at' => $now, 'updated_at' => $now]
        );
    }
};
