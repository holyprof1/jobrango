<?php

namespace Botble\JobBoard\Forms\Fronts;

use Botble\Base\Facades\BaseHelper;
use Botble\Base\Forms\FormAbstract;
use Botble\Base\Forms\Fields\DatePickerField;
use Botble\Base\Forms\Fields\SelectField;
use Botble\Base\Forms\Fields\TextField;
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
            ]);

        $this
            ->addAfter('tag', 'jobrango_advanced_notice', 'html', [
                'html' => '<div class="col-12"><div class="jobrango-advanced-settings"><div class="jobrango-advanced-settings__intro"><span class="jobrango-overview__eyebrow">' . e(__('Advanced Settings')) . '</span><h4>' . e(__('Final visibility and timing controls')) . '</h4><p>' . e(__('Use these only when this role needs extra visibility, deadline, or lifecycle adjustments.')) . '</p></div></div></div>',
            ])
            ->addAfter('jobrango_advanced_notice', 'application_closing_date', DatePickerField::class, [
                'label' => trans('plugins/job-board::forms.application_closing_date'),
                'value' => $this->getModel()->application_closing_date ? BaseHelper::formatDate($this->getModel()->application_closing_date) : '',
                'colspan' => 6,
            ])
            ->addAfter('application_closing_date', 'hide_company', 'onOff', [
                'label' => trans('plugins/job-board::forms.hide_company_details'),
                'default_value' => false,
            ])
            ->addAfter('hide_company', 'never_expired', 'onOff', [
                'label' => trans('plugins/job-board::forms.never_expired'),
                'default_value' => true,
                'help_block' => [
                    'text' => trans('plugins/job-board::forms.never_expired_helper_text'),
                ],
            ])
            ->addAfter('never_expired', 'auto_renew', 'onOff', [
                'label' => trans('plugins/job-board::forms.auto_renew_label', ['days' => JobBoardHelper::jobExpiredDays()]),
                'default_value' => false,
                'help_block' => [
                    'text' => trans('plugins/job-board::forms.auto_renew_helper_text'),
                ],
            ]);

        if (! JobBoardHelper::isUniqueIdFieldHiddenInFrontForm()) {
            $this->addAfter('auto_renew', 'unique_id', TextField::class, [
                'label' => trans('plugins/job-board::job-board.form.unique_id'),
                'value' => $this->getModel()->getKey() ? $this->getModel()->unique_id : $this->getModel()->generateUniqueId(),
                'attr' => [
                    'placeholder' => trans('plugins/job-board::job-board.form.unique_id_placeholder', ['unique_id' => $this->getModel()->generateUniqueId(true)]),
                ],
            ]);
        }

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
