<?php

namespace Botble\JobBoard\Models;

use Botble\Base\Casts\SafeContent;
use Botble\Base\Models\BaseModel;
use Botble\JobBoard\Enums\JobApplicationStatusEnum;
use Botble\JobBoard\Supports\ApplicantScreeningManager;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;

class JobApplication extends BaseModel
{
    protected $table = 'jb_applications';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone',
        'email',
        'resume',
        'cover_letter',
        'application_answers',
        'message',
        'job_id',
        'account_id',
        'status',
        'screening_status',
        'screening_summary',
    ];

    protected $casts = [
        'status' => JobApplicationStatusEnum::class,
        'application_answers' => 'array',
        'screening_summary' => 'array',
        'first_name' => SafeContent::class,
        'last_name' => SafeContent::class,
        'message' => SafeContent::class,
    ];

    public function job(): BelongsTo
    {
        return $this->belongsTo(Job::class, 'job_id')->withDefault();
    }

    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id')->withDefault();
    }

    public function getFullNameAttribute(): string
    {
        if ($this->account->id && $this->account->is_public_profile) {
            return $this->account->name;
        }

        return $this->first_name . ' ' . $this->last_name;
    }

    public function getDisplayIdAttribute(): string
    {
        return 'JR-APP-' . $this->getKey();
    }

    public function getJobUrlAttribute(): string
    {
        $url = '';
        if (! $this->job->is_expired) {
            $url = $this->job->url;
        }

        return $url;
    }

    protected function screeningStatusLabel(): Attribute
    {
        return Attribute::get(fn () => ApplicantScreeningManager::screeningStatusLabel($this->screening_status));
    }

    protected function screeningStatusColor(): Attribute
    {
        return Attribute::get(fn () => ApplicantScreeningManager::screeningStatusColor($this->screening_status));
    }
}
