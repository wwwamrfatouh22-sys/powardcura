<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Doctor;
use App\Models\DoctorAvailability;
use App\Models\DoctorSchedule;
use App\Models\Room;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('doctors') || ! Schema::hasTable('departments')) {
            return;
        }

        $departmentDoctors = $this->departmentDoctors();
        $fallbackDoctors = $this->fallbackDoctors();
        $fallbackIndex = 0;
        $rooms = Schema::hasTable('rooms')
            ? Room::query()->orderBy('room_number')->get()->values()
            : collect();

        Department::query()
            ->orderBy('name_en')
            ->get()
            ->each(function (Department $department, int $departmentIndex) use ($departmentDoctors, $fallbackDoctors, $rooms, &$fallbackIndex): void {
                $templates = $departmentDoctors[$department->name_en] ?? [];

                foreach (range(0, 1) as $doctorIndex) {
                    $template = $templates[$doctorIndex] ?? $fallbackDoctors[$fallbackIndex % count($fallbackDoctors)];
                    $fallbackIndex++;

                    $doctor = $this->upsertDoctor($department, $template, $doctorIndex);
                    $room = $rooms->isNotEmpty()
                        ? $rooms[($departmentIndex + $doctorIndex) % $rooms->count()]
                        : null;
                    $this->ensureSchedule($doctor, $room?->id);
                }

                $this->ensureDepartmentHead($department);
            });
    }

    /**
     * @param  array{name:string,specialization:string,experience:int,rating:float,image?:string}  $template
     */
    private function upsertDoctor(Department $department, array $template, int $index): Doctor
    {
        $email = $this->emailFor($department, $template['name']);

        $doctor = Doctor::withTrashed()->firstOrNew(['email' => $email]);
        $doctor->fill([
            'department_id' => $department->id,
            'name' => $template['name'],
            'specialization' => $template['specialization'],
            'image' => $template['image'] ?? ($index % 2 === 0 ? 'doc1.jpg' : 'logo_Image.png'),
            'experience' => $template['experience'],
            'rating' => $template['rating'],
            'status' => 'Available',
            'has_private_clinic' => false,
        ]);
        $doctor->deleted_at = null;

        if (! $doctor->password) {
            $doctor->password = Hash::make(env('SEED_DOCTOR_PASSWORD', 'Doctor@12345'));
        }

        $doctor->save();

        return $doctor;
    }

    private function ensureSchedule(Doctor $doctor, ?int $roomId): void
    {
        if (! Schema::hasTable('doctor_availabilities') || ! Schema::hasTable('doctor_schedules')) {
            return;
        }

        $availability = DoctorAvailability::updateOrCreate(
            [
                'doctor_id' => $doctor->id,
                'schedule_type' => 'hospital',
            ],
            [
                'appointment_duration_minutes' => 30,
                'break_between_appointments_minutes' => 0,
                'booking_window_days' => 30,
                'min_notice_minutes' => 0,
                'timezone' => 'Africa/Cairo',
                'is_active' => true,
            ]
        );

        foreach (range(0, 6) as $day) {
            foreach ([['09:00:00', '13:00:00'], ['17:00:00', '21:00:00']] as [$start, $end]) {
                DoctorSchedule::updateOrCreate(
                    [
                        'doctor_availability_id' => $availability->id,
                        'day_of_week' => $day,
                        'start_time' => $start,
                        'end_time' => $end,
                        'location_type' => 'hospital',
                    ],
                    [
                        'room_id' => $roomId,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    private function ensureDepartmentHead(Department $department): void
    {
        $head = $department->doctors()->orderByDesc('experience')->first();

        if (! $head) {
            return;
        }

        $department->update([
            'doctor_id' => $head->id,
            'head_name' => $head->name,
            'status' => 'active',
        ]);
    }

    private function emailFor(Department $department, string $name): string
    {
        $departmentSlug = Str::slug($department->name_en) ?: 'department';
        $nameSlug = Str::slug(str_replace('Dr.', '', $name)) ?: 'doctor';

        return "{$nameSlug}.{$departmentSlug}@nuh.example";
    }

    private function departmentDoctors(): array
    {
        return [
            'Internal Medicine' => [
                ['name' => 'Dr. Ahmed Hassan / د. أحمد حسن', 'specialization' => 'Consultant of Internal Medicine', 'experience' => 14, 'rating' => 4.8],
                ['name' => 'Dr. Mariam Adel / د. مريم عادل', 'specialization' => 'Diabetes and Endocrine Specialist', 'experience' => 9, 'rating' => 4.7],
            ],
            'General Surgery' => [
                ['name' => 'Dr. Karim Nabil / د. كريم نبيل', 'specialization' => 'General Surgery Consultant', 'experience' => 16, 'rating' => 4.8],
                ['name' => 'Dr. Salma Youssef / د. سلمى يوسف', 'specialization' => 'Laparoscopic Surgery Specialist', 'experience' => 10, 'rating' => 4.7],
            ],
            'Orthopedics' => [
                ['name' => 'Dr. Omar El-Sayed / د. عمر السيد', 'specialization' => 'Orthopedic Surgery Consultant', 'experience' => 15, 'rating' => 4.9],
                ['name' => 'Dr. Farida Samir / د. فريدة سمير', 'specialization' => 'Sports Injury Specialist', 'experience' => 8, 'rating' => 4.6],
            ],
            'Obstetrics and Gynecology' => [
                ['name' => 'Dr. Nourhan Fathy / د. نورهان فتحي', 'specialization' => 'Obstetrics and Gynecology Consultant', 'experience' => 13, 'rating' => 4.9],
                ['name' => 'Dr. Hana Mahmoud / د. هنا محمود', 'specialization' => 'Maternal Fetal Medicine Specialist', 'experience' => 9, 'rating' => 4.7],
            ],
            'Cardiology & Catheterization' => [
                ['name' => 'Dr. Youssef Mansour / د. يوسف منصور', 'specialization' => 'Interventional Cardiologist', 'experience' => 17, 'rating' => 4.9],
                ['name' => 'Dr. Laila Mostafa / د. ليلى مصطفى', 'specialization' => 'Cardiology Specialist', 'experience' => 11, 'rating' => 4.8],
            ],
            'Intensive Care Unit (ICU)' => [
                ['name' => 'Dr. Hisham Fouad / د. هشام فؤاد', 'specialization' => 'Critical Care Consultant', 'experience' => 18, 'rating' => 4.8],
                ['name' => 'Dr. Dina Magdy / د. دينا مجدي', 'specialization' => 'Intensive Care Specialist', 'experience' => 10, 'rating' => 4.7],
            ],
            'Urology' => [
                ['name' => 'Dr. Mahmoud Tarek / د. محمود طارق', 'specialization' => 'Urology Consultant', 'experience' => 15, 'rating' => 4.8],
                ['name' => 'Dr. Rana Khaled / د. رنا خالد', 'specialization' => 'Endourology Specialist', 'experience' => 8, 'rating' => 4.6],
            ],
            'Dialysis / Nephrology' => [
                ['name' => 'Dr. Amr Shawky / د. عمرو شوقي', 'specialization' => 'Nephrology Consultant', 'experience' => 16, 'rating' => 4.8],
                ['name' => 'Dr. Menna Ibrahim / د. منة إبراهيم', 'specialization' => 'Dialysis Specialist', 'experience' => 7, 'rating' => 4.6],
            ],
            'Pediatrics' => [
                ['name' => 'Dr. Hany Galal / د. هاني جلال', 'specialization' => 'Pediatrics Consultant', 'experience' => 14, 'rating' => 4.8],
                ['name' => 'Dr. Yasmin Sherif / د. ياسمين شريف', 'specialization' => 'Neonatology Specialist', 'experience' => 9, 'rating' => 4.7],
            ],
            'Ophthalmology' => [
                ['name' => 'Dr. Sherif Kamal / د. شريف كمال', 'specialization' => 'Ophthalmology Consultant', 'experience' => 13, 'rating' => 4.7],
                ['name' => 'Dr. Aya Nader / د. آية نادر', 'specialization' => 'Retina Specialist', 'experience' => 8, 'rating' => 4.6],
            ],
            'ENT (Ear, Nose, and Throat)' => [
                ['name' => 'Dr. Tamer Amin / د. تامر أمين', 'specialization' => 'ENT Consultant', 'experience' => 12, 'rating' => 4.7],
                ['name' => 'Dr. Reem Ashraf / د. ريم أشرف', 'specialization' => 'Audiology and ENT Specialist', 'experience' => 7, 'rating' => 4.6],
            ],
            'Neurosurgery' => [
                ['name' => 'Dr. Nader Wahba / د. نادر وهبة', 'specialization' => 'Neurosurgery Consultant', 'experience' => 19, 'rating' => 4.9],
                ['name' => 'Dr. Malak Saeed / د. ملك سعيد', 'specialization' => 'Spine Surgery Specialist', 'experience' => 10, 'rating' => 4.7],
            ],
            'Neurology & Psychiatry' => [
                ['name' => 'Dr. Sameh Lotfy / د. سامح لطفي', 'specialization' => 'Neurology Consultant', 'experience' => 15, 'rating' => 4.8],
                ['name' => 'Dr. Nada Fawzy / د. ندى فوزي', 'specialization' => 'Psychiatry Specialist', 'experience' => 9, 'rating' => 4.7],
            ],
            'Chest / Pulmonology' => [
                ['name' => 'Dr. Basel Hamdy / د. باسل حمدي', 'specialization' => 'Pulmonology Consultant', 'experience' => 14, 'rating' => 4.8],
                ['name' => 'Dr. Heba Mokhtar / د. هبة مختار', 'specialization' => 'Respiratory Medicine Specialist', 'experience' => 8, 'rating' => 4.6],
            ],
            'Dermatology' => [
                ['name' => 'Dr. Seif El-Din / د. سيف الدين', 'specialization' => 'Dermatology Consultant', 'experience' => 12, 'rating' => 4.7],
                ['name' => 'Dr. Joudy Ehab / د. جودي إيهاب', 'specialization' => 'Cosmetic Dermatology Specialist', 'experience' => 7, 'rating' => 4.6],
            ],
            'Emergency Physicians' => [
                ['name' => 'Dr. Mostafa Raouf / د. مصطفى رؤوف', 'specialization' => 'Emergency Medicine Consultant', 'experience' => 13, 'rating' => 4.8],
                ['name' => 'Dr. Nermine Adel / د. نرمين عادل', 'specialization' => 'Emergency Physician', 'experience' => 8, 'rating' => 4.7],
            ],
            'Radiology' => [
                ['name' => 'Dr. Khaled Zaki / د. خالد زكي', 'specialization' => 'Diagnostic Radiology Consultant', 'experience' => 15, 'rating' => 4.8],
                ['name' => 'Dr. Mai Samir / د. مي سمير', 'specialization' => 'Interventional Radiology Specialist', 'experience' => 9, 'rating' => 4.7],
            ],
        ];
    }

    private function fallbackDoctors(): array
    {
        return [
            ['name' => 'Dr. Ali Reda / د. علي رضا', 'specialization' => 'Hospital Consultant', 'experience' => 11, 'rating' => 4.6],
            ['name' => 'Dr. Mona Atef / د. منى عاطف', 'specialization' => 'Clinical Specialist', 'experience' => 8, 'rating' => 4.5],
        ];
    }
}
