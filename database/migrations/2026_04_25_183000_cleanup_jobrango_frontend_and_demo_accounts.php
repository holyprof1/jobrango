<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    private array $footerMenuDefinitions = [
        2 => [
            'name' => 'For Job Seekers',
            'items' => [
                ['title' => 'Browse Jobs', 'url' => '/jobs'],
                ['title' => 'Register', 'url' => '/register'],
                ['title' => 'Sign In', 'url' => '/login'],
                ['title' => 'Job Categories', 'url' => '/job-categories'],
            ],
        ],
        3 => [
            'name' => 'For Employers',
            'items' => [
                ['title' => 'Post a Job', 'url' => '/register'],
                ['title' => 'Companies', 'url' => '/companies'],
                ['title' => 'Find Candidates', 'url' => '/candidates'],
                ['title' => 'Employer Sign In', 'url' => '/login'],
            ],
        ],
        4 => [
            'name' => 'Company',
            'items' => [
                ['title' => 'About', 'url' => '/about-us'],
                ['title' => 'Contact', 'url' => '/contact'],
                ['title' => 'Terms', 'url' => '/terms'],
                ['title' => 'Cookie Policy', 'url' => '/cookie-policy'],
            ],
        ],
    ];

    private array $originalFooterMenuDefinitions = [
        2 => [
            'name' => 'Resources',
            'items' => [
                ['title' => 'About Us', 'url' => '/about-us'],
                ['title' => 'Our Team', 'url' => '#'],
                ['title' => 'Products', 'url' => '#'],
                ['title' => 'Contact', 'url' => '/contact'],
            ],
        ],
        3 => [
            'name' => 'Community',
            'items' => [
                ['title' => 'Feature', 'url' => '/about-us'],
                ['title' => 'Pricing', 'url' => '/pricing-plan'],
                ['title' => 'Credit', 'url' => '#'],
                ['title' => 'FAQ', 'url' => '/faqs'],
            ],
        ],
        4 => [
            'name' => 'Quick links',
            'items' => [
                ['title' => 'iOS', 'url' => '#'],
                ['title' => 'Android', 'url' => '#'],
                ['title' => 'Microsoft', 'url' => '#'],
                ['title' => 'Desktop', 'url' => '#'],
            ],
        ],
    ];

    private array $skillLabels = [
        1 => 'Administration',
        2 => 'Customer Service',
        3 => 'Sales',
        4 => 'Operations',
        5 => 'Logistics',
        6 => 'Healthcare',
        7 => 'Education',
        8 => 'Finance',
        9 => 'Remote',
    ];

    private array $originalSkillLabels = [
        1 => 'JavaScript',
        2 => 'PHP',
        3 => 'Python',
        4 => 'Laravel',
        5 => 'CakePHP',
        6 => 'WordPress',
        7 => 'Flutter',
        8 => 'FilamentPHP',
        9 => 'React.js',
    ];

    public function up(): void
    {
        $now = now();

        $this->upsertSetting('theme-jobbox-seo_description', 'JobRango connects employers and talent finders with job seekers across administration, sales, operations, healthcare, education, logistics, finance, hospitality, skilled trade, remote work, and more.', $now);
        $this->upsertSetting('theme-jobbox-cookie_consent_enable', 'yes', $now);
        $this->upsertSetting('theme-jobbox-cookie_consent_style', 'minimal', $now);
        $this->upsertSetting('theme-jobbox-cookie_consent_message', 'JobRango uses cookies to improve job search, sign-in, and employer dashboard performance.', $now);
        $this->upsertSetting('theme-jobbox-cookie_consent_button_text', 'Accept', $now);
        $this->upsertSetting('theme-jobbox-cookie_consent_learn_more_text', 'Cookie policy', $now);
        $this->upsertSetting('theme-jobbox-cookie_consent_learn_more_url', '/cookie-policy', $now);
        $this->upsertSetting('theme-jobbox-cookie_consent_background_color', '#0b1f4d', $now);
        $this->upsertSetting('theme-jobbox-cookie_consent_text_color', '#ffffff', $now);
        $this->upsertSetting('theme-jobbox-cookie_consent_max_width', '460', $now);
        $this->upsertSetting('theme-jobbox-social_links', json_encode([]), $now);
        $this->upsertSetting('theme-jobbox-copyright', 'Copyright ' . now()->year . ' JobRango. All rights reserved.', $now);

        if (Schema::hasTable('widgets')) {
            DB::table('widgets')->where('id', 1)->update([
                'data' => json_encode([
                    'id' => 'NewsletterWidget',
                    'title' => 'Get new jobs and hiring updates from JobRango',
                    'background_image' => null,
                    'image_left' => null,
                    'image_right' => null,
                ]),
                'updated_at' => $now,
            ]);

            DB::table('widgets')->where('id', 2)->update([
                'data' => json_encode([
                    'introduction' => 'JobRango connects employers and talent finders with job seekers across administration, sales, operations, healthcare, education, logistics, finance, hospitality, skilled trade, remote work, and more.',
                    'facebook_url' => null,
                    'twitter_url' => null,
                    'linkedin_url' => null,
                ]),
                'updated_at' => $now,
            ]);

            DB::table('widgets')->where('id', 3)->update([
                'data' => json_encode([
                    'id' => 'CustomMenuWidget',
                    'name' => 'For Job Seekers',
                    'menu_id' => 'resources',
                ]),
                'updated_at' => $now,
            ]);

            DB::table('widgets')->where('id', 4)->update([
                'data' => json_encode([
                    'id' => 'CustomMenuWidget',
                    'name' => 'For Employers',
                    'menu_id' => 'community',
                ]),
                'updated_at' => $now,
            ]);

            DB::table('widgets')->where('id', 5)->update([
                'data' => json_encode([
                    'id' => 'CustomMenuWidget',
                    'name' => 'Company',
                    'menu_id' => 'quick-links',
                ]),
                'updated_at' => $now,
            ]);

            DB::table('widgets')->whereIn('id', [6, 7])->delete();
        }

        if (Schema::hasTable('menus')) {
            DB::table('menus')->where('id', 2)->update(['name' => 'For Job Seekers']);
            DB::table('menus')->where('id', 3)->update(['name' => 'For Employers']);
            DB::table('menus')->where('id', 4)->update(['name' => 'Company']);
        }

        if (Schema::hasTable('menu_nodes')) {
            foreach ($this->footerMenuDefinitions as $menuId => $definition) {
                DB::table('menu_nodes')->where('menu_id', $menuId)->delete();

                foreach ($definition['items'] as $position => $item) {
                    DB::table('menu_nodes')->insert([
                        'menu_id' => $menuId,
                        'parent_id' => 0,
                        'reference_id' => null,
                        'reference_type' => null,
                        'url' => $item['url'],
                        'icon_font' => null,
                        'position' => $position,
                        'title' => $item['title'],
                        'css_class' => null,
                        'target' => '_self',
                        'has_child' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        if (Schema::hasTable('jb_job_skills')) {
            foreach ($this->skillLabels as $id => $label) {
                DB::table('jb_job_skills')->where('id', $id)->update([
                    'name' => $label,
                    'updated_at' => $now,
                ]);
            }
        }

        if (Schema::hasTable('jb_job_skills_translations')) {
            foreach ($this->skillLabels as $id => $label) {
                DB::table('jb_job_skills_translations')
                    ->where('jb_job_skills_id', $id)
                    ->update(['name' => $label]);
            }
        }

        $jobSeekerId = $this->upsertDemoAccount(
            email: 'jobseeker@jobrango.test',
            firstName: 'Demo',
            lastName: 'Applicant',
            type: 'job-seeker',
            description: 'Local JobRango demo applicant account for testing job search and application flows.',
            address: 'Lagos, Nigeria',
            bio: 'Available for administration, customer service, and operations roles.',
            now: $now,
        );
        $this->upsertSlug($jobSeekerId, 'Botble\\JobBoard\\Models\\Account', 'demo-applicant', $now);

        $employerId = $this->upsertDemoAccount(
            email: 'employer@jobrango.test',
            firstName: 'Demo',
            lastName: 'Employer',
            type: 'employer',
            description: 'Local JobRango demo employer account for testing company and hiring flows.',
            address: 'Victoria Island, Lagos',
            bio: 'Handles hiring for administration, operations, and customer-facing roles.',
            now: $now,
        );
        $this->upsertSlug($employerId, 'Botble\\JobBoard\\Models\\Account', 'demo-employer', $now);

        if (Schema::hasTable('jb_companies')) {
            $company = DB::table('jb_companies')
                ->where('name', 'JobRango Talent Partners')
                ->first();

            $companyData = [
                'unique_id' => $company?->unique_id ?: Str::uuid()->toString(),
                'name' => 'JobRango Talent Partners',
                'email' => 'employer@jobrango.test',
                'description' => 'A local demo employer profile for JobRango frontend and dashboard testing.',
                'content' => 'JobRango Talent Partners uses this company profile to test employer onboarding, job posting, and application review flows in the local environment.',
                'website' => 'https://jobrango.test',
                'logo' => 'companies/1.png',
                'address' => '12 Marina Business District, Lagos',
                'country_id' => 1,
                'phone' => '+2348000000000',
                'number_of_employees' => '11-50',
                'is_verified' => 1,
                'verified_at' => $now,
                'status' => 'published',
                'updated_at' => $now,
            ];

            if ($company) {
                DB::table('jb_companies')->where('id', $company->id)->update($companyData);
                $companyId = $company->id;
            } else {
                $companyData['created_at'] = $now;
                $companyId = DB::table('jb_companies')->insertGetId($companyData);
            }

            if (Schema::hasTable('jb_companies_accounts')) {
                DB::table('jb_companies_accounts')->updateOrInsert([
                    'company_id' => $companyId,
                    'account_id' => $employerId,
                ], []);
            }

            $this->upsertSlug($companyId, 'Botble\\JobBoard\\Models\\Company', 'jobrango-talent-partners', $now);
        }
    }

    public function down(): void
    {
        $now = now();

        $this->upsertSetting('theme-jobbox-seo_description', 'JobBox is a neat, clean and professional job board website script for your organization. It’s easy to build a complete Job Board site with JobBox script.', $now);
        $this->upsertSetting('theme-jobbox-cookie_consent_enable', 'yes', $now);
        $this->upsertSetting('theme-jobbox-cookie_consent_style', 'full-width', $now);
        $this->upsertSetting('theme-jobbox-cookie_consent_message', 'Your experience on this site will be improved by allowing cookies ', $now);
        $this->upsertSetting('theme-jobbox-cookie_consent_button_text', 'Allow cookies', $now);
        $this->upsertSetting('theme-jobbox-cookie_consent_learn_more_text', 'Cookie Policy', $now);
        $this->upsertSetting('theme-jobbox-cookie_consent_learn_more_url', '/cookie-policy', $now);
        $this->upsertSetting('theme-jobbox-cookie_consent_background_color', '#000', $now);
        $this->upsertSetting('theme-jobbox-cookie_consent_text_color', '#fff', $now);
        $this->upsertSetting('theme-jobbox-cookie_consent_max_width', '1170', $now);
        $this->upsertSetting('theme-jobbox-social_links', json_encode([
            [
                ['key' => 'social-name', 'value' => 'Facebook'],
                ['key' => 'social-icon', 'value' => 'socials/facebook.png'],
                ['key' => 'social-url', 'value' => 'https://facebook.com'],
            ],
            [
                ['key' => 'social-name', 'value' => 'Linkedin'],
                ['key' => 'social-icon', 'value' => 'socials/linkedin.png'],
                ['key' => 'social-url', 'value' => 'https://linkedin.com'],
            ],
            [
                ['key' => 'social-name', 'value' => 'Twitter'],
                ['key' => 'social-icon', 'value' => 'socials/twitter.png'],
                ['key' => 'social-url', 'value' => 'https://twitter.com'],
            ],
        ]), $now);
        $this->upsertSetting('theme-jobbox-copyright', '©2025 Archi Elite JSC. All right reserved.', $now);

        if (Schema::hasTable('widgets')) {
            DB::table('widgets')->where('id', 1)->update([
                'data' => json_encode([
                    'id' => 'NewsletterWidget',
                    'title' => 'New Things Will Always <br> Update Regularly',
                    'background_image' => 'general/newsletter-background-image.png',
                    'image_left' => 'general/newsletter-image-left.png',
                    'image_right' => 'general/newsletter-image-right.png',
                ]),
                'updated_at' => $now,
            ]);

            DB::table('widgets')->where('id', 2)->update([
                'data' => json_encode([
                    'introduction' => 'JobBox is the heart of the design community and the best resource to discover and connect with designers and jobs worldwide.',
                    'facebook_url' => '#',
                    'twitter_url' => '#',
                    'linkedin_url' => '#',
                ]),
                'updated_at' => $now,
            ]);

            DB::table('widgets')->where('id', 3)->update([
                'data' => json_encode([
                    'id' => 'CustomMenuWidget',
                    'name' => 'Resources',
                    'menu_id' => 'resources',
                ]),
                'updated_at' => $now,
            ]);

            DB::table('widgets')->where('id', 4)->update([
                'data' => json_encode([
                    'id' => 'CustomMenuWidget',
                    'name' => 'Community',
                    'menu_id' => 'community',
                ]),
                'updated_at' => $now,
            ]);

            DB::table('widgets')->where('id', 5)->update([
                'data' => json_encode([
                    'id' => 'CustomMenuWidget',
                    'name' => 'Quick links',
                    'menu_id' => 'quick-links',
                ]),
                'updated_at' => $now,
            ]);

            if (! DB::table('widgets')->where('id', 6)->exists()) {
                DB::table('widgets')->insert([
                    'id' => 6,
                    'widget_id' => 'CustomMenuWidget',
                    'sidebar_id' => 'footer_sidebar',
                    'theme' => 'jobbox',
                    'position' => 5,
                    'data' => json_encode([
                        'id' => 'CustomMenuWidget',
                        'name' => 'More',
                        'menu_id' => 'more',
                    ]),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            if (! DB::table('widgets')->where('id', 7)->exists()) {
                DB::table('widgets')->insert([
                    'id' => 7,
                    'widget_id' => 'DownloadWidget',
                    'sidebar_id' => 'footer_sidebar',
                    'theme' => 'jobbox',
                    'position' => 6,
                    'data' => json_encode([
                        'app_store_url' => '#',
                        'app_store_image' => 'general/app-store.png',
                        'android_app_url' => '#',
                        'google_play_image' => 'general/android.png',
                    ]),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }
        }

        if (Schema::hasTable('menus')) {
            DB::table('menus')->where('id', 2)->update(['name' => 'Resources']);
            DB::table('menus')->where('id', 3)->update(['name' => 'Community']);
            DB::table('menus')->where('id', 4)->update(['name' => 'Quick links']);
        }

        if (Schema::hasTable('menu_nodes')) {
            foreach ($this->originalFooterMenuDefinitions as $menuId => $definition) {
                DB::table('menu_nodes')->where('menu_id', $menuId)->delete();

                foreach ($definition['items'] as $position => $item) {
                    DB::table('menu_nodes')->insert([
                        'menu_id' => $menuId,
                        'parent_id' => 0,
                        'reference_id' => null,
                        'reference_type' => null,
                        'url' => $item['url'],
                        'icon_font' => null,
                        'position' => $position,
                        'title' => $item['title'],
                        'css_class' => null,
                        'target' => '_self',
                        'has_child' => 0,
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            }
        }

        if (Schema::hasTable('jb_job_skills')) {
            foreach ($this->originalSkillLabels as $id => $label) {
                DB::table('jb_job_skills')->where('id', $id)->update([
                    'name' => $label,
                    'updated_at' => $now,
                ]);
            }
        }

        if (Schema::hasTable('jb_job_skills_translations')) {
            foreach ($this->originalSkillLabels as $id => $label) {
                DB::table('jb_job_skills_translations')
                    ->where('jb_job_skills_id', $id)
                    ->update(['name' => $label]);
            }
        }

        if (Schema::hasTable('jb_companies_accounts') && Schema::hasTable('jb_companies') && Schema::hasTable('jb_accounts')) {
            $companyId = DB::table('jb_companies')->where('name', 'JobRango Talent Partners')->value('id');
            $employerId = DB::table('jb_accounts')->where('email', 'employer@jobrango.test')->value('id');

            if ($companyId && $employerId) {
                DB::table('jb_companies_accounts')
                    ->where('company_id', $companyId)
                    ->where('account_id', $employerId)
                    ->delete();
            }

            DB::table('slugs')
                ->whereIn('key', ['demo-applicant', 'demo-employer', 'jobrango-talent-partners'])
                ->delete();

            DB::table('jb_companies')->where('name', 'JobRango Talent Partners')->delete();
            DB::table('jb_accounts')->whereIn('email', ['jobseeker@jobrango.test', 'employer@jobrango.test'])->delete();
        }
    }

    private function upsertSetting(string $key, ?string $value, $now): void
    {
        if (! Schema::hasTable('settings')) {
            return;
        }

        DB::table('settings')->updateOrInsert(
            ['key' => $key],
            ['value' => $value, 'updated_at' => $now, 'created_at' => $now]
        );
    }

    private function upsertDemoAccount(
        string $email,
        string $firstName,
        string $lastName,
        string $type,
        string $description,
        string $address,
        string $bio,
        $now
    ): int {
        $account = DB::table('jb_accounts')->where('email', $email)->first();

        $payload = [
            'unique_id' => $account?->unique_id ?: Str::uuid()->toString(),
            'first_name' => $firstName,
            'last_name' => $lastName,
            'description' => $description,
            'email' => $email,
            'password' => Hash::make('JobRango123!'),
            'confirmed_at' => $now,
            'type' => $type,
            'address' => $address,
            'bio' => $bio,
            'is_public_profile' => 1,
            'hide_cv' => 0,
            'available_for_hiring' => 1,
            'country_id' => 1,
            'updated_at' => $now,
        ];

        if ($account) {
            DB::table('jb_accounts')->where('id', $account->id)->update($payload);

            return (int) $account->id;
        }

        $payload['created_at'] = $now;

        return (int) DB::table('jb_accounts')->insertGetId($payload);
    }

    private function upsertSlug(int $referenceId, string $referenceType, string $key, $now): void
    {
        if (! Schema::hasTable('slugs')) {
            return;
        }

        DB::table('slugs')->updateOrInsert(
            [
                'reference_id' => $referenceId,
                'reference_type' => $referenceType,
            ],
            [
                'key' => $key,
                'prefix' => '',
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );
    }
};
