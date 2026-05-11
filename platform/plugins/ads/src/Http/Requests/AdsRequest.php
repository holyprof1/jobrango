<?php

namespace Botble\Ads\Http\Requests;

use Botble\Ads\Facades\AdsManager;
use Botble\Ads\Models\Ads;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class AdsRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'key' => 'required|max:120|unique:ads,key,' . $this->route('ads.id'),
            'location' => 'sometimes|' . Rule::in(array_keys(AdsManager::getLocations())),
            'order' => ['required', 'integer', 'min:0', 'max:127'],
            'status' => Rule::in(BaseStatusEnum::values()),
            'expired_at' => ['nullable', 'date'],
            'ads_type' => ['required', 'in:custom_ad,google_adsense,embed_code'],
            'google_adsense_slot_id' => [
                Rule::requiredIf(fn () => $this->input('ads_type') === 'google_adsense'),
                'nullable',
                'string',
                'max:255',
            ],
            'embed_provider' => [
                Rule::requiredIf(fn () => $this->input('ads_type') === 'embed_code'),
                'nullable',
                Rule::in(['adsterra', 'custom']),
            ],
            'embed_size' => [
                Rule::requiredIf(fn () => $this->input('ads_type') === 'embed_code'),
                'nullable',
                Rule::in(array_keys(Ads::getEmbedSizeChoices())),
            ],
            'embed_width' => [
                Rule::requiredIf(
                    fn () => $this->input('ads_type') === 'embed_code' && $this->input('embed_size') === Ads::EMBED_SIZE_CUSTOM
                ),
                'nullable',
                'integer',
                'min:1',
                'max:5000',
            ],
            'embed_height' => [
                Rule::requiredIf(
                    fn () => $this->input('ads_type') === 'embed_code' && $this->input('embed_size') === Ads::EMBED_SIZE_CUSTOM
                ),
                'nullable',
                'integer',
                'min:1',
                'max:5000',
            ],
            'embed_code' => [
                Rule::requiredIf(fn () => $this->input('ads_type') === 'embed_code'),
                'nullable',
                'string',
            ],
        ];
    }
}
