<?php

return [
    'default_author_email' => env('JOB_BOARD_IMPORTER_AUTHOR_EMAIL'),
    'default_country' => env('JOB_BOARD_IMPORTER_COUNTRY', 'Nigeria'),
    'default_expire_days' => (int) env('JOB_BOARD_IMPORTER_EXPIRE_DAYS', 30),
    'deactivate_missing_jobs' => (bool) env('JOB_BOARD_IMPORTER_DEACTIVATE_MISSING', true),
    'sources' => [
        /*
         * Enable the sources you want and adjust selectors/tokens to match the
         * upstream board. HTML sources are best for boards like Jobberman where
         * a stable public API is not exposed.
         */
        'jobberman' => [
            'enabled' => false,
            'driver' => 'html',
            'label' => 'Jobberman',
            'base_url' => 'https://www.jobberman.com',
            'list_urls' => [
                'https://www.jobberman.com/jobs',
            ],
            'listing_link_selector' => 'a[href*="/job"]',
            'listing_link_patterns' => [
                '#/job/#i',
            ],
            'company_name_selector' => '[data-testid="job-company-name"], .company-name, [class*="company"] a',
            'title_selector' => 'h1, [data-testid="job-title"]',
            'description_selector' => '[data-testid="job-description"], .job-description, .markdown',
            'location_selector' => '[data-testid="job-location"], [class*="location"]',
            'apply_url_selector' => 'a[href*="apply"], a[href*="application"]',
            'country' => 'Nigeria',
            'types' => ['Full Time'],
            'tags' => ['Nigeria Jobs', 'Imported'],
        ],
        'myjobmag' => [
            'enabled' => false,
            'driver' => 'html',
            'label' => 'MyJobMag',
            'base_url' => 'https://www.myjobmag.com',
            'list_urls' => [
                'https://www.myjobmag.com/jobs',
            ],
            'listing_link_selector' => 'a[href*="/job/"], a[href*="/jobs/"]',
            'listing_link_patterns' => [
                '#/job/#i',
                '#/jobs/#i',
            ],
            'company_name_selector' => '.company, .company-name, [itemprop="hiringOrganization"]',
            'title_selector' => 'h1',
            'description_selector' => '.job-ad-desc, .job-description, article',
            'location_selector' => '.location, [itemprop="jobLocation"]',
            'apply_url_selector' => 'a[href*="apply"], a.apply-button',
            'country' => 'Nigeria',
            'tags' => ['Nigeria Jobs', 'Imported'],
        ],
        'hotnigerianjobs' => [
            'enabled' => false,
            'driver' => 'html',
            'label' => 'Hot Nigerian Jobs',
            'base_url' => 'https://www.hotnigerianjobs.com',
            'list_urls' => [
                'https://www.hotnigerianjobs.com/hotjobs/',
            ],
            'listing_link_selector' => 'a[href*="/hotjobs/"]',
            'listing_link_patterns' => [
                '#/hotjobs/#i',
            ],
            'company_name_selector' => '.company, b',
            'title_selector' => 'h1, .jobheader',
            'description_selector' => '.jobdetail, .job_details, article',
            'location_selector' => '.location, [class*="location"]',
            'apply_url_selector' => 'a[href*="apply"], a[target="_blank"]',
            'country' => 'Nigeria',
            'tags' => ['Nigeria Jobs', 'Imported'],
        ],
        'greenhouse_demo' => [
            'enabled' => false,
            'driver' => 'greenhouse',
            'label' => 'Greenhouse Demo',
            'board_token' => env('JOB_BOARD_GREENHOUSE_BOARD'),
            'company_name' => env('JOB_BOARD_GREENHOUSE_COMPANY'),
            'tags' => ['Greenhouse', 'Imported'],
        ],
        'lever_demo' => [
            'enabled' => false,
            'driver' => 'lever',
            'label' => 'Lever Demo',
            'site' => env('JOB_BOARD_LEVER_SITE'),
            'company_name' => env('JOB_BOARD_LEVER_COMPANY'),
            'tags' => ['Lever', 'Imported'],
        ],
        'smartrecruiters_demo' => [
            'enabled' => false,
            'driver' => 'smartrecruiters',
            'label' => 'SmartRecruiters Demo',
            'company_identifier' => env('JOB_BOARD_SMARTRECRUITERS_COMPANY'),
            'company_name' => env('JOB_BOARD_SMARTRECRUITERS_NAME'),
            'tags' => ['SmartRecruiters', 'Imported'],
        ],
        'workable_demo' => [
            'enabled' => false,
            'driver' => 'workable',
            'label' => 'Workable Demo',
            'account' => env('JOB_BOARD_WORKABLE_ACCOUNT'),
            'company_name' => env('JOB_BOARD_WORKABLE_COMPANY'),
            'tags' => ['Workable', 'Imported'],
        ],
    ],
];
