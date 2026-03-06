<?php

namespace Database\Seeders;

use App\Models\StaffComplaint;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ComplaintSeeder extends Seeder
{
    public function run(): void
    {

        StaffComplaint::insert([

            [
                'name'=>'John Smith',
                'email'=>'john.smith@email.com',
                'phone'=>'+1234567890',
                'date'=>'2026-01-15',
                'subject'=>'Service Quality Issue',
                'priority'=>'high',
                'status'=>'pending',
                'created_at'=>now(),
                'updated_at'=>now()
            ],

            [
                'name'=>'Sarah Johnson',
                'email'=>'sarah.j@email.com',
                'phone'=>'+1234567891',
                'date'=>'2026-01-14',
                'subject'=>'Billing Dispute',
                'priority'=>'medium',
                'status'=>'in_progress',
                'created_at'=>now(),
                'updated_at'=>now()
            ],

            [
                'name'=>'Michael Brown',
                'email'=>'m.brown@email.com',
                'phone'=>'+1234567892',
                'date'=>'2026-01-13',
                'subject'=>'Product Defect',
                'priority'=>'high',
                'status'=>'pending',
                'created_at'=>now(),
                'updated_at'=>now()
            ],

            [
                'name'=>'Emily Davis',
                'email'=>'emily.d@email.com',
                'phone'=>'+1234567893',
                'date'=>'2026-01-12',
                'subject'=>'Late Delivery',
                'priority'=>'low',
                'status'=>'pending',
                'created_at'=>now(),
                'updated_at'=>now()
            ],

            [
                'name'=>'David Wilson',
                'email'=>'d.wilson@email.com',
                'phone'=>'+1234567894',
                'date'=>'2026-01-11',
                'subject'=>'Customer Support',
                'priority'=>'medium',
                'status'=>'resolved',
                'created_at'=>now(),
                'updated_at'=>now()
            ],

            [
                'name'=>'Lisa Anderson',
                'email'=>'lisa.a@email.com',
                'phone'=>'+1234567895',
                'date'=>'2026-01-10',
                'subject'=>'Technical Issue',
                'priority'=>'high',
                'status'=>'in_progress',
                'created_at'=>now(),
                'updated_at'=>now()
            ]

        ]);

    }
}
