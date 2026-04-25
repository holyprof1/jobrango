<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private string $homepage2Content = <<<'HTML'
<div>[search-box title="Find your next move with JobRango" highlight_text="JobRango" description="Search better roles, discover credible hiring teams, and move from first application to next big step with a cleaner job board experience." counter_title_1="Open Roles" counter_number_1="51" counter_title_2="Hiring Teams" counter_number_2="20" counter_title_3="Job Categories" counter_number_3="10" counter_title_4="Featured Locations" counter_number_4="7" background_image="pages/banner-section-search-box.png" style="style-2" trending_keywords="Administration,Sales & Marketing,Customer Service,Remote Work"][/search-box]</div><div>[job-of-the-day title="Fresh roles worth a closer look" subtitle="A practical mix of administration, sales, service, operations, finance, healthcare, education, hospitality, and skilled trade openings." job_categories="1,2,3,4,5,6,7,8" style="style-2"][/job-of-the-day]</div><div>[popular-category title="Explore by category" subtitle="Start broad, then narrow into the paths that match your experience and preferred work style."][/popular-category]</div><div>[job-by-location title="Where opportunity is moving" description="Browse the strongest location clusters, then filter down to the roles that fit your next move." city="12,46,69,111,121,116,62" style="style-2"][/job-by-location]</div><div>[top-companies title="Teams hiring on JobRango" description="Meet the companies actively posting opportunities and building strong employer presence." style="style-2"][/top-companies]</div>
HTML;

    private string $homepage2OriginalContent = <<<'HTML'
<div>[search-box title="Find your range of work with JobRango" highlight_text="JobRango" description="Search better roles, discover credible hiring teams, and move from first application to next big step with a cleaner job board experience." counter_title_1="Open Roles" counter_number_1="51" counter_title_2="Hiring Teams" counter_number_2="20" counter_title_3="Job Categories" counter_number_3="10" counter_title_4="Career Guides" counter_number_4="3" background_image="pages/banner-section-search-box.png" style="style-2" trending_keywords="Administration,Sales & Marketing,Customer Service,Remote Work"][/search-box]</div><div>[job-of-the-day title="Fresh roles worth a closer look" subtitle="A practical mix of administration, sales, service, operations, finance, healthcare, education, hospitality, and skilled trade openings." job_categories="1,2,3,4,5,6,7,8" style="style-2"][/job-of-the-day]</div><div>[popular-category title="Explore by category" subtitle="Start broad, then narrow into the paths that match your experience and preferred work style."][/popular-category]</div><div>[job-by-location title="Where opportunity is moving" description="Browse the strongest location clusters, then filter down to the roles that fit your next move." city="12,46,69,111,121,116,62" style="style-2"][/job-by-location]</div><div>[top-companies title="Teams hiring on JobRango" description="Meet the companies actively posting opportunities and building strong employer presence." style="style-2"][/top-companies]</div><div>[news-and-blogs title="Career notes and hiring insight" subtitle="Useful reading for applicants and employers who want a smarter next move." button_text="Load More Posts" button_link="#" style="style-2"][/news-and-blogs]</div>
HTML;

    private string $candidatesPageContent = <<<'HTML'
<div>[job-candidates title="Browse Talent" description="Employers and talent teams can review candidate profiles, skills, experience, and availability in one place." number_per_page="9" style="grid"][/job-candidates]</div>
HTML;

    private string $candidatesPageOriginalContent = <<<'HTML'
<div>[job-candidates title="Browse Candidates" description="Lorem ipsum dolor sit amet consectetur adipisicing elit. Vero repellendus magni, atque &#x3C;br&#x3E; delectus molestias quis?" number_per_page="9" style="grid"][/job-candidates]</div><div>[news-and-blogs title="News and Blog" subtitle="Get the latest news, updates and tips" style="style-2"][/news-and-blogs]</div>
HTML;

    public function up(): void
    {
        if (Schema::hasTable('pages')) {
            DB::table('pages')
                ->where('id', 2)
                ->update([
                    'content' => $this->homepage2Content,
                    'updated_at' => now(),
                ]);

            DB::table('pages')
                ->where('id', 9)
                ->update([
                    'content' => $this->candidatesPageContent,
                    'updated_at' => now(),
                ]);
        }

        if (! Schema::hasTable('menu_nodes')) {
            return;
        }

        DB::table('menu_nodes')
            ->whereIn('id', [9, 10, 11, 12, 13, 15, 16, 18, 19, 20, 21, 22, 23, 24, 26, 27, 28, 29])
            ->delete();

        DB::table('menu_nodes')->where('id', 1)->update([
            'parent_id' => 0,
            'reference_id' => 2,
            'reference_type' => 'Botble\\Page\\Models\\Page',
            'url' => null,
            'icon_font' => null,
            'position' => 0,
            'title' => 'Home',
            'target' => '_self',
            'has_child' => 0,
            'updated_at' => now(),
        ]);

        DB::table('menu_nodes')->where('id', 8)->update([
            'parent_id' => 0,
            'reference_id' => null,
            'reference_type' => null,
            'url' => '/jobs',
            'icon_font' => null,
            'position' => 1,
            'title' => 'Jobs',
            'target' => '_self',
            'has_child' => 0,
            'updated_at' => now(),
        ]);

        DB::table('menu_nodes')->where('id', 14)->update([
            'parent_id' => 0,
            'reference_id' => 8,
            'reference_type' => 'Botble\\Page\\Models\\Page',
            'url' => '/companies',
            'icon_font' => null,
            'position' => 2,
            'title' => 'Companies',
            'target' => '_self',
            'has_child' => 0,
            'updated_at' => now(),
        ]);

        DB::table('menu_nodes')->where('id', 17)->update([
            'parent_id' => 0,
            'reference_id' => null,
            'reference_type' => null,
            'url' => '/register',
            'icon_font' => null,
            'position' => 3,
            'title' => 'For Employers',
            'target' => '_self',
            'has_child' => 0,
            'updated_at' => now(),
        ]);

        DB::table('menu_nodes')->where('id', 25)->update([
            'parent_id' => 0,
            'reference_id' => null,
            'reference_type' => null,
            'url' => '/login',
            'icon_font' => null,
            'position' => 4,
            'title' => 'Sign In',
            'target' => '_self',
            'has_child' => 0,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        if (Schema::hasTable('pages')) {
            DB::table('pages')
                ->where('id', 2)
                ->update([
                    'content' => $this->homepage2OriginalContent,
                    'updated_at' => now(),
                ]);

            DB::table('pages')
                ->where('id', 9)
                ->update([
                    'content' => $this->candidatesPageOriginalContent,
                    'updated_at' => now(),
                ]);
        }

        if (! Schema::hasTable('menu_nodes')) {
            return;
        }

        DB::table('menu_nodes')->where('id', 8)->update([
            'parent_id' => 0,
            'reference_id' => 8,
            'reference_type' => 'Botble\\Page\\Models\\Page',
            'url' => '/companies',
            'icon_font' => null,
            'position' => 0,
            'title' => 'Find a Job',
            'target' => '_self',
            'has_child' => 1,
            'updated_at' => now(),
        ]);

        DB::table('menu_nodes')->where('id', 14)->update([
            'parent_id' => 0,
            'reference_id' => 8,
            'reference_type' => 'Botble\\Page\\Models\\Page',
            'url' => '/companies',
            'icon_font' => null,
            'position' => 0,
            'title' => 'Companies',
            'target' => '_self',
            'has_child' => 1,
            'updated_at' => now(),
        ]);

        DB::table('menu_nodes')->where('id', 17)->update([
            'parent_id' => 0,
            'reference_id' => 9,
            'reference_type' => 'Botble\\Page\\Models\\Page',
            'url' => '/candidates',
            'icon_font' => null,
            'position' => 0,
            'title' => 'Candidates',
            'target' => '_self',
            'has_child' => 1,
            'updated_at' => now(),
        ]);

        DB::table('menu_nodes')->where('id', 25)->update([
            'parent_id' => 20,
            'reference_id' => null,
            'reference_type' => null,
            'url' => '/login',
            'icon_font' => 'fi fi-rr-fingerprint',
            'position' => 0,
            'title' => 'Sign in',
            'target' => '_self',
            'has_child' => 0,
            'updated_at' => now(),
        ]);

        DB::table('menu_nodes')->insert([
            ['id' => 9, 'menu_id' => 1, 'parent_id' => 8, 'reference_id' => null, 'reference_type' => null, 'url' => '/jobs?layout=grid', 'icon_font' => 'fi fi-rr-briefcase', 'position' => 0, 'title' => 'Jobs Grid', 'css_class' => null, 'target' => '_self', 'has_child' => 0, 'created_at' => '2025-10-26 20:13:26', 'updated_at' => '2025-10-26 20:13:26'],
            ['id' => 10, 'menu_id' => 1, 'parent_id' => 8, 'reference_id' => null, 'reference_type' => null, 'url' => '/jobs', 'icon_font' => 'fi fi-rr-briefcase', 'position' => 0, 'title' => 'Jobs List', 'css_class' => null, 'target' => '_self', 'has_child' => 0, 'created_at' => '2025-10-26 20:13:26', 'updated_at' => '2025-10-26 20:13:26'],
            ['id' => 11, 'menu_id' => 1, 'parent_id' => 8, 'reference_id' => null, 'reference_type' => null, 'url' => '', 'icon_font' => 'fi fi-rr-briefcase', 'position' => 0, 'title' => 'Job Details', 'css_class' => null, 'target' => '_self', 'has_child' => 0, 'created_at' => '2025-10-26 20:13:26', 'updated_at' => '2025-10-26 20:13:26'],
            ['id' => 12, 'menu_id' => 1, 'parent_id' => 8, 'reference_id' => null, 'reference_type' => null, 'url' => '', 'icon_font' => 'fi fi-rr-briefcase', 'position' => 0, 'title' => 'Job External', 'css_class' => null, 'target' => '_self', 'has_child' => 0, 'created_at' => '2025-10-26 20:13:26', 'updated_at' => '2025-10-26 20:13:26'],
            ['id' => 13, 'menu_id' => 1, 'parent_id' => 8, 'reference_id' => null, 'reference_type' => null, 'url' => '', 'icon_font' => 'fi fi-rr-briefcase', 'position' => 0, 'title' => 'Job Hide Company', 'css_class' => null, 'target' => '_self', 'has_child' => 0, 'created_at' => '2025-10-26 20:13:26', 'updated_at' => '2025-10-26 20:13:26'],
            ['id' => 15, 'menu_id' => 1, 'parent_id' => 14, 'reference_id' => 8, 'reference_type' => 'Botble\\Page\\Models\\Page', 'url' => '/companies', 'icon_font' => 'fi fi-rr-briefcase', 'position' => 0, 'title' => 'Companies', 'css_class' => null, 'target' => '_self', 'has_child' => 0, 'created_at' => '2025-10-26 20:13:26', 'updated_at' => '2025-10-26 20:13:26'],
            ['id' => 16, 'menu_id' => 1, 'parent_id' => 14, 'reference_id' => null, 'reference_type' => null, 'url' => '', 'icon_font' => 'fi fi-rr-info', 'position' => 0, 'title' => 'Company Details', 'css_class' => null, 'target' => '_self', 'has_child' => 0, 'created_at' => '2025-10-26 20:13:26', 'updated_at' => '2025-10-26 20:13:26'],
            ['id' => 18, 'menu_id' => 1, 'parent_id' => 17, 'reference_id' => 9, 'reference_type' => 'Botble\\Page\\Models\\Page', 'url' => '/candidates', 'icon_font' => 'fi fi-rr-user', 'position' => 0, 'title' => 'Candidates Grid', 'css_class' => null, 'target' => '_self', 'has_child' => 0, 'created_at' => '2025-10-26 20:13:26', 'updated_at' => '2025-10-26 20:13:26'],
            ['id' => 19, 'menu_id' => 1, 'parent_id' => 17, 'reference_id' => null, 'reference_type' => null, 'url' => '', 'icon_font' => 'fi fi-rr-info', 'position' => 0, 'title' => 'Candidate Details', 'css_class' => null, 'target' => '_self', 'has_child' => 0, 'created_at' => '2025-10-26 20:13:26', 'updated_at' => '2025-10-26 20:13:26'],
            ['id' => 20, 'menu_id' => 1, 'parent_id' => 0, 'reference_id' => null, 'reference_type' => null, 'url' => '#', 'icon_font' => null, 'position' => 0, 'title' => 'Pages', 'css_class' => null, 'target' => '_self', 'has_child' => 1, 'created_at' => '2025-10-26 20:13:26', 'updated_at' => '2025-10-26 20:13:26'],
            ['id' => 21, 'menu_id' => 1, 'parent_id' => 20, 'reference_id' => 10, 'reference_type' => 'Botble\\Page\\Models\\Page', 'url' => '/about-us', 'icon_font' => 'fi fi-rr-star', 'position' => 0, 'title' => 'About Us', 'css_class' => null, 'target' => '_self', 'has_child' => 0, 'created_at' => '2025-10-26 20:13:26', 'updated_at' => '2025-10-26 20:13:26'],
            ['id' => 22, 'menu_id' => 1, 'parent_id' => 20, 'reference_id' => 11, 'reference_type' => 'Botble\\Page\\Models\\Page', 'url' => '/pricing-plan', 'icon_font' => 'fi fi-rr-database', 'position' => 0, 'title' => 'Pricing Plan', 'css_class' => null, 'target' => '_self', 'has_child' => 0, 'created_at' => '2025-10-26 20:13:26', 'updated_at' => '2025-10-26 20:13:26'],
            ['id' => 23, 'menu_id' => 1, 'parent_id' => 20, 'reference_id' => 10, 'reference_type' => 'Botble\\Page\\Models\\Page', 'url' => '/about-us', 'icon_font' => 'fi fi-rr-paper-plane', 'position' => 0, 'title' => 'Contact Us', 'css_class' => null, 'target' => '_self', 'has_child' => 0, 'created_at' => '2025-10-26 20:13:26', 'updated_at' => '2025-10-26 20:13:26'],
            ['id' => 24, 'menu_id' => 1, 'parent_id' => 20, 'reference_id' => null, 'reference_type' => null, 'url' => '/register', 'icon_font' => 'fi fi-rr-user-add', 'position' => 0, 'title' => 'Register', 'css_class' => null, 'target' => '_self', 'has_child' => 0, 'created_at' => '2025-10-26 20:13:26', 'updated_at' => '2025-10-26 20:13:26'],
            ['id' => 26, 'menu_id' => 1, 'parent_id' => 20, 'reference_id' => null, 'reference_type' => null, 'url' => '/password/request', 'icon_font' => 'fi fi-rr-settings', 'position' => 0, 'title' => 'Reset Password', 'css_class' => null, 'target' => '_self', 'has_child' => 0, 'created_at' => '2025-10-26 20:13:26', 'updated_at' => '2025-10-26 20:13:26'],
            ['id' => 27, 'menu_id' => 1, 'parent_id' => 0, 'reference_id' => 13, 'reference_type' => 'Botble\\Page\\Models\\Page', 'url' => '/blog', 'icon_font' => null, 'position' => 0, 'title' => 'Blog', 'css_class' => null, 'target' => '_self', 'has_child' => 1, 'created_at' => '2025-10-26 20:13:26', 'updated_at' => '2025-10-26 20:13:26'],
            ['id' => 28, 'menu_id' => 1, 'parent_id' => 27, 'reference_id' => 13, 'reference_type' => 'Botble\\Page\\Models\\Page', 'url' => '/blog', 'icon_font' => 'fi fi-rr-edit', 'position' => 0, 'title' => 'Blog Grid', 'css_class' => null, 'target' => '_self', 'has_child' => 0, 'created_at' => '2025-10-26 20:13:26', 'updated_at' => '2025-10-26 20:13:26'],
            ['id' => 29, 'menu_id' => 1, 'parent_id' => 27, 'reference_id' => null, 'reference_type' => null, 'url' => '', 'icon_font' => 'fi fi-rr-document-signed', 'position' => 0, 'title' => 'Blog Single', 'css_class' => null, 'target' => '_self', 'has_child' => 0, 'created_at' => '2025-10-26 20:13:26', 'updated_at' => '2025-10-26 20:13:26'],
        ]);
    }
};
