<?php

namespace Botble\Ads\Models;

use Botble\Ads\Database\Factories\AdsFactory;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Botble\Media\Facades\RvMedia;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ads extends BaseModel
{
    use HasFactory;

    public const EMBED_SIZE_CUSTOM = 'custom';

    protected $table = 'ads';

    protected $fillable = [
        'name',
        'key',
        'status',
        'open_in_new_tab',
        'expired_at',
        'location',
        'image',
        'tablet_image',
        'mobile_image',
        'url',
        'clicked',
        'order',
        'ads_type',
        'google_adsense_slot_id',
        'embed_provider',
        'embed_code',
        'embed_size',
        'embed_width',
        'embed_height',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
        'expired_at' => 'date',
        'open_in_new_tab' => 'boolean',
    ];

    public function scopeNotExpired(Builder $query): Builder
    {
        return $query->where(function (Builder $query): void {
            $query
                ->whereNull('expired_at')
                ->orWhereDate('expired_at', '>=', Carbon::now());
        });
    }

    protected function randomHash(): Attribute
    {
        return Attribute::get(fn () => hash('sha1', $this->key . $this->getKey()));
    }

    protected function imageUrl(): Attribute
    {
        return Attribute::get(
            function (): ?string {
                if (config('plugins.ads.general.use_real_image_url')) {
                    return RvMedia::getImageUrl($this->image);
                }

                return $this->parseImageUrl();
            }
        );
    }

    protected function tabletImageUrl(): Attribute
    {
        return Attribute::get(
            function (): ?string {
                if (config('plugins.ads.general.use_real_image_url')) {
                    return RvMedia::getImageUrl($this->tablet_image ?: $this->image);
                }

                return $this->parseImageUrl('tablet');
            }
        );
    }

    protected function mobileImageUrl(): Attribute
    {
        return Attribute::get(
            function (): ?string {
                if (config('plugins.ads.general.use_real_image_url')) {
                    return RvMedia::getImageUrl(($this->mobile_image ?: $this->tablet_image) ?: $this->image);
                }

                return $this->parseImageUrl('mobile');
            }
        );
    }

    protected function clickUrl(): Attribute
    {
        return Attribute::get(
            fn ($_, array $attributes = []): string =>
                route('public.ads-click.alternative', [
                    'randomHash' => $this->random_hash,
                    'adsKey' => $attributes['key'],
                ])
        );
    }

    public function parseImageUrl(string $size = 'default'): string
    {
        return route('public.ads-click.image', [
            'randomHash' => $this->random_hash,
            'adsKey' => $this->key,
            'size' => $size,
            'hashName' => md5($this->key),
        ]);
    }

    public static function getEmbedSizeChoices(): array
    {
        return [
            '160x300' => '160 x 300',
            '160x600' => '160 x 600',
            '300x250' => '300 x 250',
            '320x50' => '320 x 50',
            '728x90' => '728 x 90',
            '468x60' => '468 x 60',
            self::EMBED_SIZE_CUSTOM => trans('plugins/ads::ads.custom_size'),
        ];
    }

    public function getEmbedDimensions(): array
    {
        $width = (int) $this->embed_width;
        $height = (int) $this->embed_height;

        if ($this->embed_size && $this->embed_size !== self::EMBED_SIZE_CUSTOM && str_contains($this->embed_size, 'x')) {
            [$presetWidth, $presetHeight] = array_pad(
                array_map('intval', explode('x', strtolower($this->embed_size), 2)),
                2,
                0
            );

            $width = $presetWidth ?: $width;
            $height = $presetHeight ?: $height;
        }

        return [
            'width' => $width > 0 ? $width : null,
            'height' => $height > 0 ? $height : null,
        ];
    }

    protected static function newFactory()
    {
        return AdsFactory::new();
    }
}
