<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $footerMenuDefinitions = [
        2 => [
            'items' => [
                ['title' => 'Browse Jobs', 'url' => '/jobs'],
                ['title' => 'Register', 'url' => '/register'],
                ['title' => 'Sign In', 'url' => '/login'],
                ['title' => 'Saved Jobs', 'url' => '/account/saved-jobs'],
            ],
        ],
        3 => [
            'items' => [
                ['title' => 'Post a Job', 'url' => '/register'],
                ['title' => 'Companies', 'url' => '/companies'],
                ['title' => 'Find Candidates', 'url' => '/candidates'],
                ['title' => 'Employer Login/Register', 'url' => '/login'],
            ],
        ],
        4 => [
            'items' => [
                ['title' => 'About', 'url' => '/about-us'],
                ['title' => 'Contact', 'url' => '/contact'],
                ['title' => 'Terms', 'url' => '/terms'],
                ['title' => 'Privacy', 'url' => '/privacy-policy'],
            ],
        ],
    ];

    public function up(): void
    {
        $now = now();

        if (Schema::hasTable('users')) {
            DB::table('users')
                ->where('id', 1)
                ->update([
                    'username' => 'admin',
                    'email' => 'admin@jobrango.test',
                    'first_name' => 'JobRango',
                    'last_name' => 'Admin',
                    'password' => Hash::make('JobRango123!'),
                    'updated_at' => $now,
                ]);
        }

        $this->upsertSetting('is_completed_get_started', '1', $now);
        $this->upsertSetting('shortcode_cache_enabled', '1', $now);
        $this->upsertSetting('shortcode_cache_ttl', '1800', $now);
        $this->upsertSetting('widget_cache_enabled', '1', $now);
        $this->upsertSetting('widget_cache_ttl', '1800', $now);
        $this->upsertSetting('job_board_listing_closed_job', '0', $now);
        $this->upsertSetting('job_board_listing_expired_job', '0', $now);
        $this->upsertSetting('job_board_accessible_closed_job', '1', $now);
        $this->upsertSetting('job_board_accessible_expired_job', '1', $now);

        if (! Schema::hasTable('menu_nodes')) {
            return;
        }

        foreach ($this->footerMenuDefinitions as $menuId => $definition) {
            DB::table('menu_nodes')->where('menu_id', $menuId)->delete();

            foreach ($definition['items'] as $position => $item) {
                DB::table('menu_nodes')->insert([
                    'menu_id' => $menuId,
                    'parent_id' => 0,
                    'reference_id' => null,
                    'reference_type' => null,
                    'url' => $item['url'],
                    'icon_font' => null,
                    'position' => $position,
                    'title' => $item['title'],
                    'css_class' => null,
                    'target' => '_self',
                    'has_child' => 0,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }
    }

    public function down(): void
    {
        $now = now();

        if (Schema::hasTable('users')) {
            DB::table('users')
                ->where('id', 1)
                ->update([
                    'username' => 'admin',
                    'password' => Hash::make('12345678'),
                    'updated_at' => $now,
                ]);
        }

        $this->upsertSetting('is_completed_get_started', '0', $now);
        $this->upsertSetting('shortcode_cache_enabled', '0', $now);
        $this->upsertSetting('widget_cache_enabled', '0', $now);
        $this->upsertSetting('job_board_listing_closed_job', '1', $now);
    }

    private function upsertSetting(string $key, ?string $value, $now): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        DB::table('settings')->updateOrInsert(
            ['key' => $key],
            ['value' => $value, 'updated_at' => $now, 'created_at' => $now]
        );
    }
};
