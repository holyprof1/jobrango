<?php

namespace Botble\JobBoard\Http\Controllers\Fronts;

use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\JobBoard\Enums\JobApplicationStatusEnum;
use Botble\JobBoard\Facades\JobBoardHelper;
use Botble\JobBoard\Http\Requests\EditJobApplicationRequest;
use Botble\JobBoard\Models\Account;
use Botble\JobBoard\Models\Job;
use Botble\JobBoard\Models\JobApplication;
use Botble\JobBoard\Supports\ApplicantScreeningManager;
use Botble\JobBoard\Supports\ApplicationFormManager;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Theme\Facades\Theme;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
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
        $applicationQuestions = [];
        $applicationStats = [];
        $applicantFilters = [
            'search' => trim((string) $request->input('search')),
            'status' => (string) $request->input('status'),
            'screening_scope' => (string) $request->input('screening_scope', 'active'),
            'filter_logic' => (string) $request->input('filter_logic', ApplicantScreeningManager::LOGIC_AND),
            'answer_filters' => [],
        ];

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

            $applicationQuestions = ApplicationFormManager::questionsForJob($selectedJob);
            $applicantFilters['answer_filters'] = ApplicantScreeningManager::normalizeStoredRules(
                collect((array) $request->input('answer_filters', []))
                    ->map(fn ($rule) => [
                        'question_key' => Arr::get($rule, 'question_key'),
                        'operator' => Arr::get($rule, 'operator'),
                        'value' => Arr::get($rule, 'value'),
                    ])
                    ->all(),
                $applicationQuestions
            );

            $allApplications = JobApplication::query()
                ->where('job_id', $selectedJob->getKey())
                ->with(['account', 'job.company'])
                ->latest('created_at')
                ->get();

            $applicationStats = [
                'total' => $allApplications->count(),
                'highlighted' => $allApplications->filter(
                    fn (JobApplication $application) => ($application->screening_status ?: ApplicantScreeningManager::STATUS_NEUTRAL) === ApplicantScreeningManager::STATUS_HIGHLIGHTED
                )->count(),
                'disqualified' => $allApplications->filter(
                    fn (JobApplication $application) => ($application->screening_status ?: ApplicantScreeningManager::STATUS_NEUTRAL) === ApplicantScreeningManager::STATUS_DISQUALIFIED
                )->count(),
                'incomplete' => $allApplications->filter(
                    fn (JobApplication $application) => ($application->screening_status ?: ApplicantScreeningManager::STATUS_NEUTRAL) === ApplicantScreeningManager::STATUS_INCOMPLETE
                )->count(),
                'active' => $allApplications->filter(
                    fn (JobApplication $application) => ($application->screening_status ?: ApplicantScreeningManager::STATUS_NEUTRAL) !== ApplicantScreeningManager::STATUS_DISQUALIFIED
                )->count(),
            ];

            $filteredApplications = $allApplications
                ->filter(fn (JobApplication $application) => $this->matchesApplicantFilters($application, $applicantFilters))
                ->values();

            $applications = $this->paginateCollection($filteredApplications, 12, 'page')
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

        $statusOptions = JobApplicationStatusEnum::labels();
        $screeningScopeOptions = ApplicantScreeningManager::screeningStatusOptions();
        $filterLogicOptions = ApplicantScreeningManager::logicOptions();
        $filterOperatorOptions = ApplicantScreeningManager::operatorOptions();

        return JobBoardHelper::scope('dashboard.applicants.index', compact(
            'jobs',
            'selectedJob',
            'applications',
            'applicationQuestions',
            'applicationStats',
            'applicantFilters',
            'statusOptions',
            'screeningScopeOptions',
            'filterLogicOptions',
            'filterOperatorOptions'
        ));
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
            ->with(['account.slugable', 'job.company'])
            ->where('id', $id)
            ->firstOrFail();

        $title = trans('plugins/job-board::messages.view_applicant', ['name' => $jobApplication->full_name]);

        $this->pageTitle($title);

        Theme::breadcrumb()
            ->add(trans('plugins/job-board::messages.my_profile'), route('public.account.dashboard'))
            ->add(trans('plugins/job-board::messages.applicants'), route('public.account.applicants.index'))
            ->add($jobApplication->full_name);

        SeoHelper::setTitle($title);

        $statusOptions = JobApplicationStatusEnum::labels();

        return JobBoardHelper::scope('dashboard.applicants.edit', compact('jobApplication', 'statusOptions'));
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
            ->setPreviousUrl(route('public.account.applicants.edit', $jobApplication->getKey()))
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

    protected function matchesApplicantFilters(JobApplication $application, array $filters): bool
    {
        $search = trim((string) Arr::get($filters, 'search'));

        if ($search !== '') {
            $haystack = collect([
                $application->full_name,
                $application->email,
                $application->phone,
            ])->filter()->implode(' ');

            if (! str_contains(mb_strtolower($haystack), mb_strtolower($search))) {
                return false;
            }
        }

        $status = Arr::get($filters, 'status');

        if ($status && $application->status->getValue() !== $status) {
            return false;
        }

        $screeningStatus = $application->screening_status ?: ApplicantScreeningManager::STATUS_NEUTRAL;
        $screeningScope = Arr::get($filters, 'screening_scope', 'active');

        if ($screeningScope === 'active' && $screeningStatus === ApplicantScreeningManager::STATUS_DISQUALIFIED) {
            return false;
        }

        if (
            $screeningScope &&
            $screeningScope !== 'all' &&
            $screeningScope !== 'active' &&
            $screeningStatus !== $screeningScope
        ) {
            return false;
        }

        $answerFilters = Arr::get($filters, 'answer_filters', []);

        if ($answerFilters !== []) {
            $evaluation = ApplicantScreeningManager::evaluateRules(
                $application->application_answers ?: [],
                $answerFilters,
                (string) Arr::get($filters, 'filter_logic', ApplicantScreeningManager::LOGIC_AND)
            );

            if (! $evaluation['matched']) {
                return false;
            }
        }

        return true;
    }

    protected function paginateCollection(Collection $items, int $perPage, string $pageName = 'page'): LengthAwarePaginator
    {
        $currentPage = LengthAwarePaginator::resolveCurrentPage($pageName);
        $results = $items->forPage($currentPage, $perPage)->values();

        return new LengthAwarePaginator(
            $results,
            $items->count(),
            $perPage,
            $currentPage,
            [
                'path' => LengthAwarePaginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]
        );
    }
}
