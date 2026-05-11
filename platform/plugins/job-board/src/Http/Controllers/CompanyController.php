<?php

namespace Botble\JobBoard\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Events\BeforeEditContentEvent;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Facades\Assets;
use Botble\Base\Http\Actions\DeleteResourceAction;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Supports\Breadcrumb;
use Botble\JobBoard\Events\AdminApprovedCompanyEvent;
use Botble\JobBoard\Facades\JobBoardHelper;
use Botble\JobBoard\Forms\CompanyForm;
use Botble\JobBoard\Http\Requests\AjaxCompanyRequest;
use Botble\JobBoard\Http\Requests\CompanyRequest;
use Botble\JobBoard\Http\Resources\CompanyResource;
use Botble\JobBoard\Models\Company;
use Botble\JobBoard\Services\StoreCompanyAccountService;
use Botble\JobBoard\Tables\CompanyTable;
use Botble\Language\Facades\Language;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyController extends BaseController
{
    protected function breadcrumb(): Breadcrumb
    {
        return parent::breadcrumb()
            ->add(trans('plugins/job-board::job-board.name'))
            ->add(trans('plugins/job-board::company.name'), route('companies.index'));
    }

    public function index(CompanyTable $table)
    {
        $this->pageTitle(trans('plugins/job-board::company.name'));

        return $table->renderTable();
    }

    public function create()
    {
        $this->pageTitle(trans('plugins/job-board::company.create'));

        return CompanyForm::create()->renderForm();
    }

    public function store(CompanyRequest $request, StoreCompanyAccountService $storeCompanyAccountService)
    {
        $data = $request->input();
        $this->syncVerificationData(
            $data,
            $request->has('is_verified')
                ? $request->boolean('is_verified')
                : true,
            Auth::id(),
            $request->input('verification_note')
        );

        if (empty($data['is_verified'])) {
            $data['is_featured'] = false;
        }

        /**
         * @var Company $company
         */
        $company = Company::query()->create($data);

        event(new CreatedContentEvent(COMPANY_MODULE_SCREEN_NAME, $request, $company));

        $storeCompanyAccountService->execute($request, $company);

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('companies.index'))
            ->setNextUrl(route('companies.edit', $company->getKey()))
            ->withCreatedSuccessMessage();
    }

    public function edit(Company $company, Request $request)
    {
        event(new BeforeEditContentEvent($request, $company));

        $this->pageTitle(trans('core/base::forms.edit_item', ['name' => $company->name]));

        return CompanyForm::createFromModel($company)->renderForm();
    }

    public function update(Company $company, CompanyRequest $request, StoreCompanyAccountService $storeCompanyAccountService)
    {
        $isApproved = $company->status->getValue() == BaseStatusEnum::PENDING && $request->input('status') == BaseStatusEnum::PUBLISHED;
        $data = $request->input();

        $this->syncVerificationData(
            $data,
            $request->boolean('is_verified'),
            Auth::id(),
            $request->input('verification_note'),
            $company
        );

        if (empty($data['is_verified'])) {
            $data['is_featured'] = false;
        }

        $company->fill($data);
        $company->save();

        if ($isApproved) {
            AdminApprovedCompanyEvent::dispatch($company);
        }

        $storeCompanyAccountService->execute($request, $company);

        event(new UpdatedContentEvent(COMPANY_MODULE_SCREEN_NAME, $request, $company));

        return $this
            ->httpResponse()
            ->setPreviousUrl(route('companies.index'))
            ->withUpdatedSuccessMessage();
    }

    public function destroy(Company $company)
    {
        return DeleteResourceAction::make($company);
    }

    public function getList(Request $request)
    {
        $keyword = $request->input('q');

        if (! $keyword) {
            return $this
                ->httpResponse()
                ->setData([]);
        }

        if (
            is_plugin_active('language') &&
            is_plugin_active('language-advanced') &&
            Language::getCurrentLocale() != Language::getDefaultLocale()
        ) {
            $data = Company::query()
                ->where(function ($query) use ($keyword): void {
                    $query->where('name', 'LIKE', '%' . $keyword . '%')
                        ->orWhereHas('translations', function ($query) use ($keyword): void {
                            $query->where('name', 'LIKE', '%' . $keyword . '%');
                        });
                })
                ->select(['id', 'name'])
                ->paginate(10);
        } else {
            $data = Company::query()
                ->where('name', 'LIKE', '%' . $keyword . '%')
                ->select(['id', 'name'])
                ->paginate(10);
        }

        return $this
            ->httpResponse()
            ->setData(CompanyResource::collection($data));
    }

    public function ajaxGetCompany(Company $company)
    {
        return $this
            ->httpResponse()
            ->setData(new CompanyResource($company));
    }

    public function ajaxCreateCompany(AjaxCompanyRequest $request)
    {
        $data = $request->input();
        $data['status'] = $data['status'] ?? BaseStatusEnum::PUBLISHED;
        $data['is_featured'] = false;

        $this->syncVerificationData(
            $data,
            true,
            Auth::id()
        );

        $company = Company::query()->create($data);

        event(new CreatedContentEvent(COMPANY_MODULE_SCREEN_NAME, $request, $company));

        return $this
            ->httpResponse()
            ->setData(new CompanyResource($company));
    }

    public function getAllCompanies()
    {
        return Company::query()->pluck('name')->all();
    }

    public function analytics(Company $company)
    {
        Assets::addScripts(['counterup', 'equal-height'])
            ->addStylesDirectly('vendor/core/core/dashboard/css/dashboard.css');

        $this->pageTitle(trans('plugins/job-board::messages.analytics_for_company', ['name' => $company->name]));

        $company->loadCount('jobs');

        return view('plugins/job-board::company.analytics', compact('company'));
    }

    public function view(Company $company)
    {
        $this->pageTitle(trans('plugins/job-board::company.viewing', ['name' => $company->name]));

        $company->loadCount(['jobs', 'reviews']);

        return view('plugins/job-board::companies.view', compact('company'));
    }

    public function verify(Company $company, Request $request)
    {
        if ($company->is_verified) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/job-board::company.already_verified'));
        }

        if ($company->status !== BaseStatusEnum::PUBLISHED) {
            $company->status = BaseStatusEnum::PUBLISHED;
        }

        $company->markAsVerified(Auth::id(), Carbon::now(), $request->input('verification_note'));
        $company->save();

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/job-board::company.verified_successfully'));
    }

    public function unverify(Company $company, Request $request)
    {
        if (! $company->is_verified) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(trans('plugins/job-board::company.not_verified_yet'));
        }

        $company->markAsUnverified($request->input('verification_note'));
        $company->is_featured = false;
        $company->save();

        return $this
            ->httpResponse()
            ->setMessage(trans('plugins/job-board::company.unverified_successfully'));
    }

    public function toggleHomepage(Company $company, Request $request)
    {
        if ($request->boolean('is_featured') && ! $company->is_verified) {
            return $this
                ->httpResponse()
                ->setError()
                ->setMessage(__('Only verified companies can be shown on the homepage.'));
        }

        $company->is_featured = $request->boolean('is_featured');
        $company->save();

        return $this
            ->httpResponse()
            ->setMessage($company->is_featured ? __('Company will now appear on the homepage.') : __('Company removed from homepage selections.'));
    }

    public function toggleVerification(Company $company, Request $request)
    {
        if ($request->boolean('is_verified')) {
            if ($company->status !== BaseStatusEnum::PUBLISHED) {
                $company->status = BaseStatusEnum::PUBLISHED;
            }

            $company->markAsVerified(Auth::id(), Carbon::now());
            $message = trans('plugins/job-board::company.verified_successfully');
        } else {
            $company->markAsUnverified();
            $company->is_featured = false;
            $message = trans('plugins/job-board::company.unverified_successfully');
        }

        $company->save();

        return $this
            ->httpResponse()
            ->setMessage($message);
    }

    protected function syncVerificationData(
        array &$data,
        bool $isVerified,
        ?int $verifiedBy = null,
        ?string $verificationNote = null,
        ?Company $company = null
    ): void {
        $data['is_verified'] = $isVerified;

        if ($isVerified) {
            $data['verified_at'] = $company?->verified_at ?: Carbon::now();
            $data['verified_by'] = $company?->verified_by ?: $verifiedBy;
        } else {
            $data['verified_at'] = null;
            $data['verified_by'] = null;
            $data['is_featured'] = false;
        }

        if ($verificationNote !== null) {
            $data['verification_note'] = $verificationNote;
        }
    }
}
