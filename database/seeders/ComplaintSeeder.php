<?php

namespace Database\Seeders;

use App\Models\StaffComplaint;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('staff_complaints')) {
            return;
        }

        $complaints = [
            [
                'name' => 'John Smith', 'email' => 'john.smith@email.com', 'phone' => '+1234567890',
                'date' => '2026-01-15', 'subject' => 'Service Quality Issue', 'priority' => 'high', 'status' => 'pending',
            ],
            [
                'name' => 'Sarah Johnson', 'email' => 'sarah.j@email.com', 'phone' => '+1234567891',
                'date' => '2026-01-14', 'subject' => 'Billing Dispute', 'priority' => 'medium', 'status' => 'in_progress',
            ],
            [
                'name' => 'Michael Brown', 'email' => 'm.brown@email.com', 'phone' => '+1234567892',
                'date' => '2026-01-13', 'subject' => 'Delayed Lab Result', 'priority' => 'high', 'status' => 'pending',
            ],
            [
                'name' => 'Emily Davis', 'email' => 'emily.d@email.com', 'phone' => '+1234567893',
                'date' => '2026-01-12', 'subject' => 'Appointment Waiting Time', 'priority' => 'low', 'status' => 'pending',
            ],
            [
                'name' => 'David Wilson', 'email' => 'd.wilson@email.com', 'phone' => '+1234567894',
                'date' => '2026-01-11', 'subject' => 'Patient Support Follow-up', 'priority' => 'medium', 'status' => 'resolved',
            ],
            [
                'name' => 'Lisa Anderson', 'email' => 'lisa.a@email.com', 'phone' => '+1234567895',
                'date' => '2026-01-10', 'subject' => 'Portal Access Issue', 'priority' => 'high', 'status' => 'in_progress',
            ],
        ];

        foreach ($complaints as $complaint) {
            StaffComplaint::updateOrCreate(
                ['email' => $complaint['email'], 'subject' => $complaint['subject']],
                $complaint
            );
        }
    }
}
