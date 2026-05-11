<?php

namespace Botble\Ads\Forms;

use Botble\Ads\Facades\AdsManager;
use Botble\Ads\Http\Requests\AdsRequest;
use Botble\Ads\Models\Ads;
use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FieldOptions\CodeEditorFieldOption;
use Botble\Base\Forms\FieldOptions\DatePickerFieldOption;
use Botble\Base\Forms\FieldOptions\HtmlFieldOption;
use Botble\Base\Forms\FieldOptions\MediaImageFieldOption;
use Botble\Base\Forms\FieldOptions\NameFieldOption;
use Botble\Base\Forms\FieldOptions\SelectFieldOption;
use Botble\Base\Forms\FieldOptions\SortOrderFieldOption;
use Botble\Base\Forms\FieldOptions\StatusFieldOption;
use Botble\Base\Forms\FieldOptions\TextFieldOption;
use Botble\Base\Forms\Fields\CodeEditorField;
use Botble\Base\Forms\Fields\DatePickerField;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\MediaImageField;
use Botble\Base\Forms\Fields\NumberField;
use Botble\Base\Forms\Fields\OnOffField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
use Botble\Base\Forms\FormAbstract;
use Carbon\Carbon;
use Illuminate\Support\Str;

class AdsForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->model(Ads::class)
            ->setValidatorClass(AdsRequest::class)
            ->add('name', TextField::class, NameFieldOption::make()->required())
            ->add('key', TextField::class, [
                'label' => trans('plugins/ads::ads.key'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('plugins/ads::ads.key'),
                    'data-counter' => 255,
                ],
                'default_value' => $this->generateAdsKey(),
            ])
            ->add('order', NumberField::class, SortOrderFieldOption::make())
            ->add(
                'ads_type',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/ads::ads.ads_type'))
                    ->choices([
                        'custom_ad' => trans('plugins/ads::ads.custom_ad'),
                        'google_adsense' => 'Google AdSense',
                        'embed_code' => trans('plugins/ads::ads.embed_code_ad'),
                    ])
            )
            ->addOpenCollapsible('ads_type', 'google_adsense', $this->getModel()->ads_type)
            ->add(
                'google_adsense_slot_id',
                TextField::class,
                TextFieldOption::make()
                    ->label(trans('plugins/ads::ads.google_adsense_slot_id'))
                    ->placeholder('E.g: 1234567890')
            )
            ->addCloseCollapsible('ads_type', 'google_adsense')
            ->addOpenCollapsible('ads_type', 'embed_code', $this->getModel()->ads_type)
            ->add(
                'embed_code_help',
                HtmlField::class,
                HtmlFieldOption::make()->content(
                    sprintf(
                        '<div class="form-text mb-3">%s</div>',
                        trans('plugins/ads::ads.embed_code_helper', [
                            'sizes' => implode(', ', array_keys(Ads::getEmbedSizeChoices())),
                        ])
                    )
                )
            )
            ->add(
                'embed_provider',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/ads::ads.embed_provider'))
                    ->choices([
                        'adsterra' => 'Adsterra',
                        'custom' => trans('plugins/ads::ads.custom_embed'),
                    ])
            )
            ->add(
                'embed_size',
                SelectField::class,
                SelectFieldOption::make()
                    ->label(trans('plugins/ads::ads.embed_size'))
                    ->choices(Ads::getEmbedSizeChoices())
                    ->helperText(trans('plugins/ads::ads.embed_size_helper'))
            )
            ->add('embed_width', NumberField::class, [
                'label' => trans('plugins/ads::ads.embed_width'),
                'attr' => [
                    'min' => 1,
                    'placeholder' => '320',
                ],
                'helper_block' => [
                    'text' => trans('plugins/ads::ads.embed_dimension_helper'),
                ],
            ])
            ->add('embed_height', NumberField::class, [
                'label' => trans('plugins/ads::ads.embed_height'),
                'attr' => [
                    'min' => 1,
                    'placeholder' => '50',
                ],
            ])
            ->add(
                'embed_code',
                CodeEditorField::class,
                CodeEditorFieldOption::make()
                    ->label(trans('plugins/ads::ads.embed_code'))
                    ->mode('html')
                    ->helperText(trans('plugins/ads::ads.embed_code_example'))
            )
            ->addCloseCollapsible('ads_type', 'embed_code')
            ->addOpenCollapsible('ads_type', 'custom_ad', $this->getModel()->ads_type ?? 'custom_ad')
            ->add('url', TextField::class, [
                'label' => trans('plugins/ads::ads.url'),
                'attr' => [
                    'placeholder' => trans('plugins/ads::ads.url'),
                    'data-counter' => 255,
                ],
            ])
            ->add('open_in_new_tab', OnOffField::class, [
                'label' => trans('plugins/ads::ads.open_in_new_tab'),
                'default_value' => true,
            ])
            ->add('image', MediaImageField::class, MediaImageFieldOption::make())
            ->add('tablet_image', MediaImageField::class, [
                'label' => trans('plugins/ads::ads.tablet_image'),
                'help_block' => [
                    'text' => trans('plugins/ads::ads.tablet_image_helper'),
                ],
            ])
            ->add('mobile_image', MediaImageField::class, [
                'label' => trans('plugins/ads::ads.mobile_image'),
                'help_block' => [
                    'text' => trans('plugins/ads::ads.mobile_image_helper'),
                ],
            ])
            ->addCloseCollapsible('ads_type', 'custom_ad')
            ->add('status', SelectField::class, StatusFieldOption::make())
            ->when(($adLocations = AdsManager::getLocations()) && count($adLocations) > 1, function () use ($adLocations): void {
                $this->add(
                    'location',
                    SelectField::class,
                    SelectFieldOption::make()
                        ->label(trans('plugins/ads::ads.location'))
                        ->helperText(trans('plugins/ads::ads.location_helper'))
                        ->choices($adLocations)
                        ->searchable()
                        ->required()
                );
            })
            ->add(
                'expired_at',
                DatePickerField::class,
                DatePickerFieldOption::make()
                    ->label(trans('plugins/ads::ads.expired_at'))
                    ->helperText(__('Leave this empty to keep the ad active until you disable it.'))
            )
            ->setBreakFieldPoint('status');
    }

    protected function generateAdsKey(): string
    {
        do {
            $key = strtoupper(Str::random(12));
        } while (Ads::query()->where('key', $key)->exists());

        return $key;
    }
}
