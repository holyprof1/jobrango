<?php

namespace Database\Seeders;

use Botble\Base\Facades\MetaBox;
use Botble\Base\Supports\BaseSeeder;
use Botble\JobBoard\Models\Category;
use Botble\Slug\Facades\SlugHelper;

class JobCategorySeeder extends BaseSeeder
{
    public function run(): void
    {
        $this->uploadFiles('job-categories');

        Category::query()->truncate();

        $data = [
            'Administration',
            'Sales & Marketing',
            'Customer Service',
            'Operations',
            'Logistics & Delivery',
            'Finance & Accounting',
            'Healthcare',
            'Education & Training',
            'Hospitality',
            'Construction & Skilled Trade',
        ];

        $imageData = [
            'management',
            'marketing',
            'customer',
            'research',
            'retail',
            'finance',
            'human',
            'content',
            'marketing',
            'lightning',
        ];

        foreach ($data as $index => $item) {
            $category = Category::query()->create([
                'name' => $item,
                'order' => $index,
                'is_featured' => $index < 8,
            ]);

            if (isset($imageData[$index])) {
                MetaBox::saveMetaBoxData($category, 'icon_image', 'general/' . $imageData[$index] . '.png');
            }

            MetaBox::saveMetaBoxData(
                $category,
                'job_category_image',
                'job-categories/img-cover-' . rand(1, 3) . '.png'
            );

            SlugHelper::createSlug($category);
        }
    }
}
