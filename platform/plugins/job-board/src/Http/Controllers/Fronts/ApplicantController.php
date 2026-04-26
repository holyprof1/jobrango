<?php

namespace Botble\JobBoard\Http\Controllers\Fronts;

use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\JobBoard\Facades\JobBoardHelper;
use Botble\JobBoard\Forms\Fronts\ApplicantForm;
use Botble\JobBoard\Http\Requests\EditJobApplicationRequest;
use Botble\JobBoard\Models\Account;
use Botble\JobBoard\Models\Job;
use Botble\JobBoard\Models\JobApplication;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ApplicantController extends BaseController
{
    public function index(Request $request)
    {
        /**
         * @var Account $account
         */
        $account = auth('account')->user();

        $selectedJobId = (int) $request->input('job_id');

        $jobs = Job::query()
            ->byAccount($account->getKey())
            ->with('company')
            ->withCount([
                'applicants',
                'applicants as new_applicants_count' => function ($query): void {
                    $query->where('status', 'pending');
                },
            ])
            ->latest('jb_jobs.created_at')
            ->paginate(10, ['*'], 'jobs_page')
            ->withQueryString();

        $selectedJob = null;
        $applications = null;

        if ($selectedJobId) {
            $selectedJob = Job::query()
                ->byAccount($account->getKey())
                ->with('company')
                ->withCount([
                    'applicants',
                    'applicants as new_applicants_count' => function ($query): void {
                        $query->where('status', 'pending');
                    },
                ])
                ->findOrFail($selectedJobId);

            $applications = JobApplication::query()
                ->where('job_id', $selectedJob->getKey())
                ->with(['account', 'job.company'])
                ->latest('created_at')
                ->paginate(12)
                ->withQueryString();
        }

        $title = $selectedJob
            ? __('Applicants for :job', ['job' => $selectedJob->name])
            : trans('plugins/job-board::messages.applicants');

        $this->pageTitle($title);

        Theme::breadcrumb()
            ->add(trans('plugins/job-board::messages.my_profile'), route('public.account.dashboard'))
            ->add(trans('plugins/job-board::messages.applicants'));

        SeoHelper::setTitle($title);

        return JobBoardHelper::scope('dashboard.applicants.index', compact('jobs', 'selectedJob', 'applications'));
    }

    public function edit(int|string $id)
    {
        /**
         * @var Account $account
         */
        $account = auth('account')->user();

        $jobApplication = JobApplication::query()
            ->select(['*'])
            ->whereHas('job.company.accounts', function (Builder $query) use ($account): void {
                $query->where('account_id', $account->getKey());
            })
            ->with(['account'])
            ->where('id', $id)
            ->firstOrFail();

        $title = trans('plugins/job-board::messages.view_applicant', ['name' => $jobApplication->full_name]);

        $this->pageTitle($title);

        SeoHelper::setTitle($title);

        return ApplicantForm::createFromModel($jobApplication)->renderForm();
    }

    public function update(int|string $id, EditJobApplicationRequest $request)
    {
        /**
         * @var Account $account
         */
        $account = auth('account')->user();

        $jobApplication = JobApplication::query()
            ->select(['*'])
            ->whereHas('job.company.accounts', function (Builder $query) use ($account): void {
                $query->where('account_id', $account->getKey());
            })
            ->where('id', $id)
            ->firstOrFail();

        $jobApplication->fill($request->only(['status']));
        $jobApplication->save();

        event(new UpdatedContentEvent(JOB_APPLICATION_MODULE_SCREEN_NAME, $request, $jobApplication));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('public.account.applicants.index'))
            ->withUpdatedSuccessMessage();
    }

    public function destroy(int|string $id)
    {
        /**
         * @var Account $account
         */
        $account = auth('account')->user();

        $jobApplication = JobApplication::query()
            ->select(['*'])
            ->whereHas('job.company.accounts', function (Builder $query) use ($account): void {
                $query->where('account_id', $account->getKey());
            })
            ->where('id', $id)
            ->firstOrFail();

        return DeleteResourceAction::make($jobApplication);
    }
}
