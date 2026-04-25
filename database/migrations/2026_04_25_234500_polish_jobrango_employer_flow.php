<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $this->upsertSetting('job_board_enable_lat_long_fields', '0', $now);
        $this->upsertSetting('job_board_hide_unique_id_field_in_admin_form', '1', $now);
        $this->upsertSetting('job_board_hide_unique_id_field_in_front_form', '1', $now);
        $this->upsertSetting('job_board_enable_post_approval', '1', $now);

        if (! Schema::hasTable('jb_currencies')) {
            return;
        }

        $ngnId = $this->upsertCurrency(
            title: 'NGN',
            symbol: 'NGN',
            order: 0,
            now: $now,
            exchangeRate: 1
        );

        $this->upsertCurrency(
            title: 'USD',
            symbol: '$',
            order: 1,
            now: $now,
            exchangeRate: 1
        );

        if (! $ngnId || ! Schema::hasTable('jb_jobs')) {
            return;
        }

        $allowedCurrencyIds = DB::table('jb_currencies')
            ->whereIn('title', ['NGN', 'USD'])
            ->pluck('id')
            ->all();

        DB::table('jb_jobs')
            ->where(function ($query) use ($allowedCurrencyIds): void {
                $query
                    ->whereNull('currency_id')
                    ->orWhereNotIn('currency_id', $allowedCurrencyIds);
            })
            ->update([
                'currency_id' => $ngnId,
                'updated_at' => $now,
            ]);
    }

    public function down(): void
    {
        $now = now();

        $this->upsertSetting('job_board_enable_lat_long_fields', '1', $now);
        $this->upsertSetting('job_board_hide_unique_id_field_in_admin_form', '0', $now);
        $this->upsertSetting('job_board_hide_unique_id_field_in_front_form', '0', $now);
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

    private function upsertCurrency(string $title, string $symbol, int $order, $now, float $exchangeRate): ?int
    {
        $existingId = DB::table('jb_currencies')->where('title', $title)->value('id');

        DB::table('jb_currencies')->updateOrInsert(
            ['title' => $title],
            [
                'symbol' => $symbol,
                'is_prefix_symbol' => true,
                'order' => $order,
                'decimals' => 2,
                'is_default' => $title === 'USD' ? DB::table('jb_currencies')->where('title', $title)->value('is_default') ?? 0 : 0,
                'exchange_rate' => $exchangeRate,
                'number_format_style' => 'us',
                'space_between_price_and_currency' => 0,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        return $existingId ?: DB::table('jb_currencies')->where('title', $title)->value('id');
    }
};
