<?php

namespace Botble\JobBoard\Forms\Fronts;

use Botble\Base\Forms\FormAbstract;
use Botble\Base\Forms\Fields\SelectField;
use Botble\JobBoard\Facades\JobBoardHelper;
use Botble\JobBoard\Forms\Fields\CustomEditorField;
use Botble\JobBoard\Forms\JobForm as FormsJobForm;
use Botble\JobBoard\Http\Requests\AccountJobRequest;
use Botble\JobBoard\Models\Account;
use Botble\JobBoard\Models\Currency;
use Botble\JobBoard\Models\Job;

class JobForm extends FormsJobForm
{
    public function setup(): void
    {
        parent::setup();

        /**
         * @var Account $account
         */
        $account = auth('account')->user();
        $companies = $account->companies->pluck('name', 'id')->all();
        $allowedCurrencies = Currency::query()
            ->whereIn('title', ['NGN', 'USD'])
            ->orderByRaw("CASE WHEN title = 'NGN' THEN 0 ELSE 1 END")
            ->oldest('title')
            ->get();

        if ($allowedCurrencies->isEmpty()) {
            $allowedCurrencies = Currency::query()
                ->oldest('order')
                ->oldest('title')
                ->get();
        }

        $currencyChoices = $allowedCurrencies->pluck('title', 'id')->all();
        $defaultCurrencyId = $allowedCurrencies->firstWhere('title', 'NGN')?->getKey() ?: array_key_first($currencyChoices);
        $selectedCurrencyId = $this->getModel()->currency_id ?: old('currency_id', $defaultCurrencyId);

        $this
            ->disablePermalinkField()
            ->template(JobBoardHelper::viewPath('dashboard.forms.base'))
            ->hasFiles()
            ->setValidatorClass(AccountJobRequest::class)
            ->remove('is_featured')
            ->remove('moderation_status')
            ->remove('content')
            ->remove('company_id')
            ->remove('number_of_positions')
            ->remove('salary_type')
            ->remove('hide_salary')
            ->remove('application_closing_date')
            ->remove('apply_url')
            ->remove('external_apply_behavior')
            ->remove('hide_company')
            ->remove('never_expired')
            ->remove('auto_renew')
            ->remove('status')
            ->remove('is_freelance')
            ->remove('career_level_id')
            ->remove('functional_area_id')
            ->remove('degree_level_id')
            ->remove('job_experience_id')
            ->remove('tag')
            ->remove('skills[]')
            ->removeMetaBox('image')
            ->removeMetaBox('colleagues')
            ->removeMetaBox('custom_fields_box')
            ->when(JobBoardHelper::isUniqueIdFieldHiddenInFrontForm(), function (FormAbstract $form): void {
                $form->remove('unique_id');
            })
            ->modify('description', 'textarea', [
                'label' => __('Short Summary'),
                'attr' => [
                    'rows' => 4,
                    'maxlength' => 400,
                    'placeholder' => __('Add a short summary candidates will see before opening the full job details.'),
                ],
            ], true)
            ->addAfter('description', 'content', CustomEditorField::class, [
                'label' => __('Job Description & Application Instructions'),
                'attr' => [
                    'model' => Job::class,
                ],
            ])
            ->remove('currency_id')
            ->addAfter('salary_range', 'currency_id', SelectField::class, [
                'label' => trans('plugins/job-board::forms.currency'),
                'choices' => $currencyChoices,
                'selected' => $selectedCurrencyId,
                'colspan' => 3,
            ])
            ->addAfter('currency_id', 'jobrango_advanced_notice', 'html', [
                'html' => '<div class="col-12"><details class="jobrango-advanced-settings"><summary>' . e(__('Advanced Settings')) . '</summary><div class="jobrango-advanced-settings__body"><p class="mb-2">' . e(__('Technical fields like moderation status, redirect behavior, visibility, coordinates, SEO, and internal IDs are kept out of the main posting flow.')) . '</p><p class="mb-0">' . e(__('If you need those controls later, they can be added without making the everyday job posting experience heavier.')) . '</p></div></details></div>',
            ]);

        if (count($companies) === 1) {
            $this->addBefore('address', 'company_id', 'hidden', [
                'default_value' => array_key_first($companies),
            ]);
        } else {
            $this->addBefore('address', 'company_id', 'customSelect', [
                'label' => trans('plugins/job-board::messages.company'),
                'required' => true,
                'wrapper' => [
                    'class' => 'form-group col-md-6',
                ],
                'choices' => $companies,
            ]);
        }
    }
}
