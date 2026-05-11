<?php

namespace Botble\JobBoard\Http\Requests;

use Botble\JobBoard\Enums\JobStatusEnum;
use Botble\JobBoard\Enums\ModerationStatusEnum;
use Botble\JobBoard\Enums\SalaryTypeEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Validator;
use Illuminate\Validation\Rule;

class JobRequest extends Request
{
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:120'],
            'description' => ['nullable', 'string', 'max:400'],
            'content' => ['nullable', 'string', 'max:100000'],
            'status' => Rule::in(JobStatusEnum::values()),
            'moderation_status' => Rule::in(ModerationStatusEnum::values()),
            'salary_type' => Rule::in(SalaryTypeEnum::values()),
            'latitude' => ['max:20', 'nullable', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'longitude' => [
                'max:20',
                'nullable',
                'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/',
            ],
            'zip_code' => ['nullable', 'string', 'max:20'],
            'custom_fields.*.name' => ['required', 'string', 'max:255'],
            'custom_fields.*.value' => ['required', 'string', 'max:255'],
            'number_of_positions' => ['required', 'integer', 'max:10000'],
            'apply_url' => ['nullable', 'url', 'max:2048'],
            'external_apply_behavior' => ['nullable', 'in:,disabled,new_tab,current_tab'],
            'is_remote' => ['sometimes', 'boolean'],
            'unique_id' => $this->getUniqueIdRules(),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $isPublishing = is_in_admin()
                ? $this->input('status', JobStatusEnum::PUBLISHED) === JobStatusEnum::PUBLISHED
                : true;

            if (! $isPublishing || $this->boolean('is_remote')) {
                return;
            }

            $hasLocation = filled($this->input('address'))
                || (int) $this->input('country_id')
                || (int) $this->input('state_id')
                || (int) $this->input('city_id')
                || (filled($this->input('latitude')) && filled($this->input('longitude')));

            if (! $hasLocation) {
                $validator->errors()->add('is_remote', __('Choose Remote or add a job location before publishing.'));
            }
        });
    }

    protected function getUniqueIdRules(): array|string
    {
        $rules = 'nullable|string|unique:jb_jobs,unique_id,' . $this->route('job.id');

        // If auto-generate is enabled and the field is hidden in the form, make it optional
        if (setting('job_board_auto_generate_unique_id', false)) {
            if (is_in_admin() && setting('job_board_hide_unique_id_field_in_admin_form', false)) {
                return 'nullable|string';
            }

            if (! is_in_admin() && setting('job_board_hide_unique_id_field_in_front_form', false)) {
                return 'nullable|string';
            }
        }

        return $rules;
    }
}
