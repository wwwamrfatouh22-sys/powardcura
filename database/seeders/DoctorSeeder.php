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

class DoctorSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('doctors') || ! Schema::hasTable('departments')) {
            return;
        }

        $rooms = Schema::hasTable('rooms')
            ? Room::query()->orderBy('room_number')->get()->values()
            : collect();

        foreach ($this->localDoctors() as $doctorIndex => $template) {
            $department = Department::query()->where('name_en', $template['department'])->first();

            if (! $department) {
                continue;
            }

            $doctor = $this->upsertDoctor($department, $template, $doctorIndex);
            $room = $rooms->isNotEmpty() ? $rooms[$doctorIndex % $rooms->count()] : null;
            $this->ensureSchedule($doctor, $room?->id);
        }

        Department::query()->each(fn (Department $department) => $this->ensureDepartmentHead($department));
    }

    /**
     * @param  array{department:string,name:string,email:string,specialization:string,experience:int,rating:float,image?:string,has_private_clinic?:bool}  $template
     */
    private function upsertDoctor(Department $department, array $template, int $index): Doctor
    {
        $doctor = Doctor::withTrashed()->firstOrNew(['email' => $template['email']]);
        $doctor->fill([
            'department_id' => $department->id,
            'name' => $template['name'],
            'specialization' => $template['specialization'],
            'image' => $template['image'] ?? ($index % 2 === 0 ? 'doc1.jpg' : 'logo_Image.png'),
            'experience' => $template['experience'],
            'rating' => $template['rating'],
            'status' => 'Available',
            'has_private_clinic' => $template['has_private_clinic'] ?? false,
        ]);
        $doctor->deleted_at = null;

        if (! $doctor->password) {
            $doctor->password = Hash::make('password123');
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

    private function localDoctors(): array
    {
        $emails = [
            'Internal Medicine' => ['ahmed-hassan-d-ahmd-hsn.internal-medicine@nuh.example', 'mariam-adel-d-mrym-aaadl.internal-medicine@nuh.example'],
            'General Surgery' => ['karim-nabil-d-krym-nbyl.general-surgery@nuh.example', 'salma-youssef-d-slm-yosf.general-surgery@nuh.example'],
            'Orthopedics' => ['omar-el-sayed-d-aamr-alsyd.orthopedics@nuh.example', 'farida-samir-d-fryd-smyr.orthopedics@nuh.example'],
            'Obstetrics and Gynecology' => ['nourhan-fathy-d-norhan-fthy.obstetrics-and-gynecology@nuh.example', 'hana-mahmoud-d-hna-mhmod.obstetrics-and-gynecology@nuh.example'],
            'Cardiology & Catheterization' => ['youssef-mansour-d-yosf-mnsor.cardiology-catheterization@nuh.example', 'laila-mostafa-d-lyl-mstf.cardiology-catheterization@nuh.example'],
            'Intensive Care Unit (ICU)' => ['hisham-fouad-d-hsham-foad.intensive-care-unit-icu@nuh.example', 'dina-magdy-d-dyna-mgdy.intensive-care-unit-icu@nuh.example'],
            'Urology' => ['mahmoud-tarek-d-mhmod-tark.urology@nuh.example', 'rana-khaled-d-rna-khald.urology@nuh.example'],
            'Dialysis / Nephrology' => ['amr-shawky-d-aamro-shoky.dialysis-nephrology@nuh.example', 'menna-ibrahim-d-mn-abrahym.dialysis-nephrology@nuh.example'],
            'Pediatrics' => ['hany-galal-d-hany-glal.pediatrics@nuh.example', 'yasmin-sherif-d-yasmyn-shryf.pediatrics@nuh.example'],
            'Ophthalmology' => ['sherif-kamal-d-shryf-kmal.ophthalmology@nuh.example', 'aya-nader-d-ay-nadr.ophthalmology@nuh.example'],
            'ENT (Ear, Nose, and Throat)' => ['tamer-amin-d-tamr-amyn.ent-ear-nose-and-throat@nuh.example', 'reem-ashraf-d-rym-ashrf.ent-ear-nose-and-throat@nuh.example'],
            'Neurosurgery' => ['nader-wahba-d-nadr-ohb.neurosurgery@nuh.example', 'malak-saeed-d-mlk-saayd.neurosurgery@nuh.example'],
            'Neurology & Psychiatry' => ['sameh-lotfy-d-samh-ltfy.neurology-psychiatry@nuh.example', 'nada-fawzy-d-nd-fozy.neurology-psychiatry@nuh.example'],
            'Chest / Pulmonology' => ['basel-hamdy-d-basl-hmdy.chest-pulmonology@nuh.example', 'heba-mokhtar-d-hb-mkhtar.chest-pulmonology@nuh.example'],
            'Dermatology' => ['seif-el-din-d-syf-aldyn.dermatology@nuh.example', 'joudy-ehab-d-gody-ayhab.dermatology@nuh.example'],
            'Emergency Physicians' => ['mostafa-raouf-d-mstf-roof.emergency-physicians@nuh.example', 'nermine-adel-d-nrmyn-aaadl.emergency-physicians@nuh.example'],
            'Radiology' => ['khaled-zaki-d-khald-zky.radiology@nuh.example', 'mai-samir-d-my-smyr.radiology@nuh.example'],
        ];

        $doctors = [
            ['department' => 'Neurology & Psychiatry', 'name' => 'Dr. Sarah Johnson', 'email' => 'doctor1@nuh.com', 'specialization' => 'Cardiologist', 'image' => 'doctor1.jpg', 'experience' => 10, 'rating' => 4.3, 'has_private_clinic' => true],
            ['department' => 'Neurology & Psychiatry', 'name' => 'Dr. Michael Chen', 'email' => 'doctor2@nuh.com', 'specialization' => 'Cardiologist', 'image' => 'doctor2.jpg', 'experience' => 8, 'rating' => 4.3, 'has_private_clinic' => true],
            ['department' => 'Radiology', 'name' => 'Dr. Aisha Patel', 'email' => 'doctor3@nuh.com', 'specialization' => 'Neurologist', 'image' => 'doctor3.jpg', 'experience' => 9, 'rating' => 4.9, 'has_private_clinic' => true],
            ['department' => 'Orthopedics', 'name' => 'Dr. James Wilson', 'email' => 'doctor4@nuh.com', 'specialization' => 'Orthopedic Surgeon', 'image' => 'doctor4.jpg', 'experience' => 15, 'rating' => 4.6, 'has_private_clinic' => true],
            ['department' => 'Radiology', 'name' => 'Amr fathelbab', 'email' => 'amrfatouh58@gmail.com', 'specialization' => 'Cardiologist', 'image' => 'logo_Image.png', 'experience' => 5, 'rating' => 4.0, 'has_private_clinic' => true],
            ['department' => 'Chest / Pulmonology', 'name' => 'Amr fathelbab salem', 'email' => 'www.amrfatouh22@gmail.com', 'specialization' => 'chest', 'image' => 'logo_Image.png', 'experience' => 3, 'rating' => 4.5, 'has_private_clinic' => true],
            ['department' => 'Cardiology & Catheterization', 'name' => 'amrfatouh', 'email' => 'amr@gmail.com', 'specialization' => 'hgrgf', 'image' => 'logo_Image.png', 'experience' => 4, 'rating' => 4.0, 'has_private_clinic' => true],
        ];

        foreach ($this->departmentDoctors() as $department => $templates) {
            foreach ($templates as $index => $template) {
                $doctors[] = [
                    'department' => $department,
                    'email' => $emails[$department][$index],
                    ...$template,
                ];
            }
        }

        return $doctors;
    }

    private function fallbackDoctors(): array
    {
        return [
            ['name' => 'Dr. Ali Reda / د. علي رضا', 'specialization' => 'Hospital Consultant', 'experience' => 11, 'rating' => 4.6],
            ['name' => 'Dr. Mona Atef / د. منى عاطف', 'specialization' => 'Clinical Specialist', 'experience' => 8, 'rating' => 4.5],
        ];
    }
}
