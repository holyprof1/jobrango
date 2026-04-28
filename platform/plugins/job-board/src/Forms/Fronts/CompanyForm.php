<?php

namespace Botble\JobBoard\Forms\Fronts;

use Botble\Base\Forms\FieldOptions\DescriptionFieldOption;
use Botble\Base\Forms\Fields\HtmlField;
use Botble\Base\Forms\Fields\TextareaField;
use Botble\Base\Forms\FormAbstract;
use Botble\JobBoard\Facades\JobBoardHelper;
use Botble\JobBoard\Forms\Fields\CustomEditorField;
use Botble\JobBoard\Http\Requests\AccountCompanyRequest;
use Botble\JobBoard\Models\Company;
use Botble\Location\Fields\Options\SelectLocationFieldOption;
use Botble\Location\Fields\SelectLocationField;

class CompanyForm extends FormAbstract
{
    public function setup(): void
    {
        $this
            ->setupModel(new Company())
            ->setValidatorClass(AccountCompanyRequest::class)
            ->columns(12)
            ->disablePermalinkField()
            ->setFormOption('enctype', 'multipart/form-data')
            ->template(JobBoardHelper::viewPath('dashboard.forms.base'))
            ->add('company_branding_notice', HtmlField::class, [
                'html' => '<div class="col-12"><div class="jobrango-advanced-settings"><div class="jobrango-advanced-settings__intro"><span class="jobrango-overview__eyebrow">' . e(__('Branding')) . '</span><h4>' . e(__('Company profile basics')) . '</h4><p>' . e(__('Keep this simple: add your logo, summary, contact details, and a few facts candidates care about.')) . '</p></div></div></div>',
            ])
            ->add('name', 'text', [
                'label' => trans('plugins/job-board::forms.company_name'),
                'required' => true,
                'attr' => [
                    'placeholder' => trans('core/base::forms.name_placeholder'),
                    'data-counter' => 120,
                ],
            ])
            ->add('logo', 'mediaImage', [
                'label' => trans('plugins/job-board::forms.logo'),
            ])
            ->add('cover_image', 'mediaImage', [
                'label' => trans('plugins/job-board::forms.cover_image'),
            ])
            ->add('description', TextareaField::class, DescriptionFieldOption::make())
            ->add('content', CustomEditorField::class, [
                'label' => __('About the company'),
                'attr' => [
                    'rows' => 4,
                    'placeholder' => trans('core/base::forms.description_placeholder'),
                ],
            ])
            ->add('email', 'email', [
                'label' => trans('plugins/job-board::forms.email'),
                'attr' => [
                    'placeholder' => trans('plugins/job-board::forms.email_placeholder'),
                    'data-counter' => 120,
                ],
                'colspan' => 6,
            ])
            ->add('phone', 'text', [
                'label' => trans('plugins/job-board::forms.phone'),
                'attr' => [
                    'placeholder' => trans('plugins/job-board::forms.phone_placeholder'),
                    'data-counter' => 30,
                ],
                'colspan' => 6,
            ])
            ->add('website', 'text', [
                'label' => trans('plugins/job-board::forms.website'),
                'attr' => [
                    'placeholder' => trans('plugins/job-board::forms.website_placeholder'),
                    'data-counter' => 120,
                ],
                'colspan' => 12,
            ])
            ->add('ceo', 'text', [
                'label' => trans('plugins/job-board::forms.company_ceo'),
                'attr' => [
                    'placeholder' => trans('plugins/job-board::forms.company_ceo'),
                    'data-counter' => 120,
                ],
                'colspan' => 6,
            ])
            ->add('year_founded', 'number', [
                'label' => trans('plugins/job-board::forms.year_founded'),
                'attr' => [
                    'placeholder' => trans('plugins/job-board::forms.year_founded_placeholder'),
                    'data-counter' => 10,
                ],
                'colspan' => 3,
            ])
            ->add('number_of_offices', 'number', [
                'label' => trans('plugins/job-board::forms.number_of_offices'),
                'attr' => [
                    'placeholder' => trans('plugins/job-board::forms.number_of_offices_placeholder'),
                    'data-counter' => 10,
                ],
                'colspan' => 3,
            ])
            ->add('number_of_employees', 'text', [
                'label' => trans('plugins/job-board::forms.number_of_employees'),
                'attr' => [
                    'placeholder' => trans('plugins/job-board::forms.number_of_employees_placeholder'),
                    'data-counter' => 10,
                ],
                'colspan' => 6,
            ])
            ->add('annual_revenue', 'text', [
                'label' => trans('plugins/job-board::forms.annual_revenue'),
                'attr' => [
                    'placeholder' => trans('plugins/job-board::forms.annual_revenue_placeholder'),
                    'data-counter' => 10,
                ],
                'colspan' => 6,
            ])
            ->when(is_plugin_active('location'), function (FormAbstract $form): void {
                $form->add(
                    'location_data',
                    SelectLocationField::class,
                    SelectLocationFieldOption::make()
                );
            })
            ->add('address', 'text', [
                'label' => trans('plugins/job-board::forms.address'),
                'attr' => [
                    'placeholder' => trans('plugins/job-board::forms.address'),
                    'data-counter' => 120,
                ],
                'colspan' => 6,
            ])
            ->add('postal_code', 'text', [
                'label' => trans('plugins/job-board::forms.postal_code'),
                'attr' => [
                    'placeholder' => trans('plugins/job-board::forms.postal_code'),
                    'data-counter' => 20,
                ],
                'colspan' => 6,
            ])
            ->setBreakFieldPoint('cover_image')
            ->addMetaBoxes([
                'social_links' => [
                    'title' => trans('plugins/job-board::forms.social_links'),
                    'content' => view(
                        JobBoardHelper::viewPath('dashboard.forms.social-links'),
                        ['company' => $this->getModel()]
                    )->render(),
                ],
            ]);
    }
}
