<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\JobBoard\Database\Traits\HasJobSeeder;

class JobSeeder extends BaseSeeder
{
    use HasJobSeeder;

    public function run(): void
    {
        $this->uploadFiles('jobs');

        // Create job tags using the trait method
        $this->createJobTags($this->getDefaultJobTags());

        // Create jobs using the trait method
        $this->createJobs($this->getDefaultJobNames(), $this->getDefaultJobContent());
    }

    protected function getDefaultJobContent(): string
    {
        return '<h5>Responsibilities</h5>
                <div>
                    <p>Support daily work across the assigned team, branch, customer, or field location while keeping service quality and reporting clear.</p>
                    <ul>
                        <li>Handle assigned tasks accurately and communicate progress with the hiring team or supervisor.</li>
                        <li>Work with customers, colleagues, vendors, or community members in a professional manner.</li>
                        <li>Keep simple records, reports, schedules, inventory notes, or service updates as required for the role.</li>
                        <li>Follow workplace safety, confidentiality, and company policy requirements.</li>
                        <li>Escalate issues early and help the team meet daily operational targets.</li>
                    </ul>
                </div>
                <h5>Qualification</h5>
                <div>
                    <ul>
                        <li>Relevant education, training, certification, or hands-on experience for the role.</li>
                        <li>Good communication, reliability, and willingness to learn.</li>
                        <li>Ability to work on-site, remotely, in shifts, or in the field depending on the vacancy.</li>
                        <li>Previous experience in administration, sales, service, operations, finance, healthcare, education, hospitality, logistics, or skilled trade is an advantage.</li>
                    </ul>
                </div>';
    }

    protected function getDefaultJobNames(): array
    {
        return [
            'Administrative Officer',
            'Office Assistant',
            'Sales Representative',
            'Marketing Executive',
            'Customer Service Representative',
            'Call Centre Agent',
            'Operations Supervisor',
            'Warehouse Assistant',
            'Logistics Coordinator',
            'Delivery Rider',
            'Finance Officer',
            'Accounts Assistant',
            'Healthcare Assistant',
            'Registered Nurse',
            'Pharmacy Technician',
            'Teacher',
            'Training Coordinator',
            'Hotel Front Desk Officer',
            'Restaurant Supervisor',
            'Housekeeping Attendant',
            'Site Supervisor',
            'Electrician',
            'Plumber',
            'Driver',
            'Security Officer',
            'Cleaner and Facility Support',
            'Graduate Trainee',
            'Internship Programme Assistant',
            'Remote Customer Support Associate',
            'Contract Field Enumerator',
            'Procurement Assistant',
            'Inventory Officer',
            'Human Resources Assistant',
            'Payroll Officer',
            'Community Health Worker',
            'School Administrator',
            'Retail Store Associate',
            'Cashier',
            'Business Development Officer',
            'Field Sales Agent',
            'Operations Analyst',
            'Fleet Coordinator',
            'Data Entry Clerk',
            'General Labour Assistant',
            'Facility Maintenance Technician',
            'Clinic Receptionist',
            'Agricultural Field Officer',
            'Legal Administrative Secretary',
            'Loan Officer',
            'Insurance Sales Advisor',
            'Project Coordinator',
        ];
    }

    protected function getDefaultJobTags(): array
    {
        return [
            'Administration',
            'Sales',
            'Customer Service',
            'Logistics',
            'Finance',
            'Healthcare',
            'Education',
            'Field Work',
        ];
    }
}
