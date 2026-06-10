<?php

namespace Database\Seeders;

use App\Models\Medication;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        if (! Schema::hasTable('users') || ! Schema::hasTable('patients')) {
            return;
        }

        foreach ($this->patients() as $index => $data) {
            $user = User::firstOrNew(['email' => $data['email']]);
            $user->fill([
                'name' => $data['full_name'],
                'national_id' => $data['national_id'],
            ]);

            if (! $user->password) {
                $user->password = Hash::make(env('SEED_PATIENT_PASSWORD', 'Patient@12345'));
            }

            $user->save();

            $dob = Carbon::parse($data['dob']);
            $patient = Patient::withTrashed()->firstOrNew(['national_id' => $data['national_id']]);
            $patient->fill([
                'user_id' => $user->id,
                'full_name' => $data['full_name'],
                'file_number' => 'PAT'.str_pad((string) ($index + 1), 6, '0', STR_PAD_LEFT),
                'blood_type' => $data['blood_type'],
                'gender' => $data['gender'],
                'age' => $dob->age,
                'dob' => $dob->toDateString(),
                'phone' => $data['phone'],
                'address' => $data['address'],
                'blood_pressure' => $data['blood_pressure'],
                'pulse_rate' => $data['pulse_rate'],
                'temperature' => $data['temperature'],
                'weight' => $data['weight'],
                'medical_condition' => $data['medical_condition'],
                'last_visit' => now()->subDays($index % 14)->setTime(10 + ($index % 7), 0),
            ]);
            $patient->deleted_at = null;

            if (! $patient->password) {
                $patient->password = $user->password;
            }

            $patient->save();

            foreach (Schema::hasTable('medications') ? $data['medications'] : [] as $medication) {
                Medication::updateOrCreate(
                    [
                        'patient_id' => $patient->id,
                        'name' => $medication['name'],
                    ],
                    [
                        'dose' => $medication['dose'],
                        'instructions' => $medication['instructions'],
                    ]
                );
            }
        }
    }

    private function patients(): array
    {
        return [
            ['full_name' => 'Tamer Ahmed Ibrahim / تامر أحمد إبراهيم', 'email' => 'tamer.ibrahim@nuh.example', 'national_id' => '30001011234567', 'dob' => '1984-01-12', 'gender' => 'male', 'phone' => '01012003456', 'address' => 'Nasr City, Cairo', 'blood_type' => 'A+', 'blood_pressure' => '120/80', 'pulse_rate' => 72, 'temperature' => 37.0, 'weight' => 82, 'medical_condition' => 'Type 2 diabetes follow-up', 'medications' => [['name' => 'Metformin', 'dose' => '500 mg', 'instructions' => 'Twice daily with food']]],
            ['full_name' => 'Nadine Samy Khalil / نادين سامي خليل', 'email' => 'nadine.khalil@nuh.example', 'national_id' => '29903021234568', 'dob' => '1992-03-02', 'gender' => 'female', 'phone' => '01014567890', 'address' => 'Heliopolis, Cairo', 'blood_type' => 'O+', 'blood_pressure' => '118/76', 'pulse_rate' => 78, 'temperature' => 36.8, 'weight' => 64, 'medical_condition' => 'Routine cardiology check', 'medications' => []],
            ['full_name' => 'Mohamed Fathy Salem / محمد فتحي سالم', 'email' => 'mohamed.salem@nuh.example', 'national_id' => '28807151234569', 'dob' => '1988-07-15', 'gender' => 'male', 'phone' => '01123456780', 'address' => 'Maadi, Cairo', 'blood_type' => 'B+', 'blood_pressure' => '135/85', 'pulse_rate' => 80, 'temperature' => 37.1, 'weight' => 91, 'medical_condition' => 'Hypertension monitoring', 'medications' => [['name' => 'Amlodipine', 'dose' => '5 mg', 'instructions' => 'Once daily in the morning']]],
            ['full_name' => 'Heba Essam Farouk / هبة عصام فاروق', 'email' => 'heba.farouk@nuh.example', 'national_id' => '29511261234570', 'dob' => '1995-11-26', 'gender' => 'female', 'phone' => '01234567891', 'address' => 'Dokki, Giza', 'blood_type' => 'AB+', 'blood_pressure' => '112/72', 'pulse_rate' => 74, 'temperature' => 36.9, 'weight' => 59, 'medical_condition' => 'Pregnancy follow-up', 'medications' => [['name' => 'Folic Acid', 'dose' => '400 mcg', 'instructions' => 'Once daily']]],
            ['full_name' => 'Omar Hossam Ali / عمر حسام علي', 'email' => 'omar.ali@nuh.example', 'national_id' => '31004201234571', 'dob' => '2010-04-20', 'gender' => 'male', 'phone' => '01098765432', 'address' => 'Shubra, Cairo', 'blood_type' => 'O-', 'blood_pressure' => '105/68', 'pulse_rate' => 86, 'temperature' => 37.0, 'weight' => 45, 'medical_condition' => 'Pediatric asthma follow-up', 'medications' => [['name' => 'Salbutamol Inhaler', 'dose' => '100 mcg', 'instructions' => 'As needed']]],
            ['full_name' => 'Salma Mostafa Gabr / سلمى مصطفى جبر', 'email' => 'salma.gabr@nuh.example', 'national_id' => '30109081234572', 'dob' => '2001-09-08', 'gender' => 'female', 'phone' => '01187654321', 'address' => 'Mansoura', 'blood_type' => 'A-', 'blood_pressure' => '110/70', 'pulse_rate' => 76, 'temperature' => 36.7, 'weight' => 58, 'medical_condition' => 'Dermatology consultation', 'medications' => []],
            ['full_name' => 'Youssef Mahmoud Amin / يوسف محمود أمين', 'email' => 'youssef.amin@nuh.example', 'national_id' => '27606181234573', 'dob' => '1976-06-18', 'gender' => 'male', 'phone' => '01022223333', 'address' => '6th of October, Giza', 'blood_type' => 'B-', 'blood_pressure' => '128/82', 'pulse_rate' => 70, 'temperature' => 36.8, 'weight' => 88, 'medical_condition' => 'Knee pain assessment', 'medications' => [['name' => 'Paracetamol', 'dose' => '500 mg', 'instructions' => 'Every 8 hours if needed']]],
            ['full_name' => 'Mariam Ashraf Nader / مريم أشرف نادر', 'email' => 'mariam.nader@nuh.example', 'national_id' => '30302191234574', 'dob' => '2003-02-19', 'gender' => 'female', 'phone' => '01211112222', 'address' => 'Alexandria', 'blood_type' => 'O+', 'blood_pressure' => '116/74', 'pulse_rate' => 73, 'temperature' => 36.9, 'weight' => 61, 'medical_condition' => 'Migraine evaluation', 'medications' => []],
            ['full_name' => 'Khaled Abdelrahman / خالد عبد الرحمن', 'email' => 'khaled.abdelrahman@nuh.example', 'national_id' => '27010051234575', 'dob' => '1970-10-05', 'gender' => 'male', 'phone' => '01033334444', 'address' => 'Tanta', 'blood_type' => 'AB-', 'blood_pressure' => '142/88', 'pulse_rate' => 82, 'temperature' => 37.2, 'weight' => 94, 'medical_condition' => 'Nephrology follow-up', 'medications' => [['name' => 'Losartan', 'dose' => '50 mg', 'instructions' => 'Once daily']]],
            ['full_name' => 'Farida Wael Hassan / فريدة وائل حسن', 'email' => 'farida.hassan@nuh.example', 'national_id' => '29812211234576', 'dob' => '1998-12-21', 'gender' => 'female', 'phone' => '01144445555', 'address' => 'Zamalek, Cairo', 'blood_type' => 'A+', 'blood_pressure' => '108/70', 'pulse_rate' => 75, 'temperature' => 36.6, 'weight' => 55, 'medical_condition' => 'ENT consultation', 'medications' => []],
            ['full_name' => 'Adel Refaat Osman / عادل رفعت عثمان', 'email' => 'adel.osman@nuh.example', 'national_id' => '26505021234577', 'dob' => '1965-05-02', 'gender' => 'male', 'phone' => '01255556666', 'address' => 'Faisal, Giza', 'blood_type' => 'O+', 'blood_pressure' => '130/84', 'pulse_rate' => 79, 'temperature' => 36.8, 'weight' => 86, 'medical_condition' => 'Urology follow-up', 'medications' => [['name' => 'Tamsulosin', 'dose' => '0.4 mg', 'instructions' => 'Once daily after dinner']]],
            ['full_name' => 'Dina Magdy Lotfy / دينا مجدي لطفي', 'email' => 'dina.lotfy@nuh.example', 'national_id' => '29308111234578', 'dob' => '1993-08-11', 'gender' => 'female', 'phone' => '01066667777', 'address' => 'New Cairo', 'blood_type' => 'B+', 'blood_pressure' => '114/75', 'pulse_rate' => 77, 'temperature' => 36.9, 'weight' => 63, 'medical_condition' => 'Ophthalmology check', 'medications' => []],
            ['full_name' => 'Bassel Kamal Mourad / باسل كمال مراد', 'email' => 'bassel.mourad@nuh.example', 'national_id' => '28604141234579', 'dob' => '1986-04-14', 'gender' => 'male', 'phone' => '01177778888', 'address' => 'Sheikh Zayed, Giza', 'blood_type' => 'A-', 'blood_pressure' => '126/80', 'pulse_rate' => 71, 'temperature' => 36.7, 'weight' => 79, 'medical_condition' => 'Chest tightness evaluation', 'medications' => []],
            ['full_name' => 'Rana Ehab Sobhy / رنا إيهاب صبحي', 'email' => 'rana.sobhy@nuh.example', 'national_id' => '30206061234580', 'dob' => '2002-06-06', 'gender' => 'female', 'phone' => '01288889999', 'address' => 'Port Said', 'blood_type' => 'O-', 'blood_pressure' => '109/69', 'pulse_rate' => 72, 'temperature' => 36.6, 'weight' => 57, 'medical_condition' => 'General surgery follow-up', 'medications' => []],
            ['full_name' => 'Sherif Gamal Younis / شريف جمال يونس', 'email' => 'sherif.younis@nuh.example', 'national_id' => '28009171234581', 'dob' => '1980-09-17', 'gender' => 'male', 'phone' => '01099990000', 'address' => 'Ismailia', 'blood_type' => 'B+', 'blood_pressure' => '122/78', 'pulse_rate' => 68, 'temperature' => 36.8, 'weight' => 83, 'medical_condition' => 'Post-operative review', 'medications' => []],
            ['full_name' => 'Laila Tarek Amin / ليلى طارق أمين', 'email' => 'laila.amin@nuh.example', 'national_id' => '29704031234582', 'dob' => '1997-04-03', 'gender' => 'female', 'phone' => '01100001111', 'address' => 'Helwan, Cairo', 'blood_type' => 'AB+', 'blood_pressure' => '111/72', 'pulse_rate' => 74, 'temperature' => 36.9, 'weight' => 60, 'medical_condition' => 'Neurology consultation', 'medications' => []],
            ['full_name' => 'Amira Saad ElDin / أميرة سعد الدين', 'email' => 'amira.saaddin@nuh.example', 'national_id' => '29012011234583', 'dob' => '1990-12-01', 'gender' => 'female', 'phone' => '01210101010', 'address' => 'Assiut', 'blood_type' => 'A+', 'blood_pressure' => '117/73', 'pulse_rate' => 76, 'temperature' => 36.7, 'weight' => 62, 'medical_condition' => 'Psychiatry follow-up', 'medications' => []],
            ['full_name' => 'Hussein Waheed Amer / حسين وحيد عامر', 'email' => 'hussein.amer@nuh.example', 'national_id' => '27303091234584', 'dob' => '1973-03-09', 'gender' => 'male', 'phone' => '01012121212', 'address' => 'Beni Suef', 'blood_type' => 'O+', 'blood_pressure' => '138/86', 'pulse_rate' => 81, 'temperature' => 37.0, 'weight' => 90, 'medical_condition' => 'Emergency follow-up', 'medications' => []],
            ['full_name' => 'Malak Nasser Fouad / ملك ناصر فؤاد', 'email' => 'malak.fouad@nuh.example', 'national_id' => '30507121234585', 'dob' => '2005-07-12', 'gender' => 'female', 'phone' => '01123232323', 'address' => 'Minya', 'blood_type' => 'B-', 'blood_pressure' => '107/68', 'pulse_rate' => 79, 'temperature' => 36.8, 'weight' => 52, 'medical_condition' => 'Pediatric follow-up', 'medications' => []],
            ['full_name' => 'Sameh Lotfy Rizk / سامح لطفي رزق', 'email' => 'sameh.rizk@nuh.example', 'national_id' => '26811041234586', 'dob' => '1968-11-04', 'gender' => 'male', 'phone' => '01234343434', 'address' => 'Suez', 'blood_type' => 'A+', 'blood_pressure' => '145/90', 'pulse_rate' => 84, 'temperature' => 37.1, 'weight' => 96, 'medical_condition' => 'Cardiology follow-up', 'medications' => [['name' => 'Atorvastatin', 'dose' => '20 mg', 'instructions' => 'Once daily in the evening']]],
            ['full_name' => 'Jana Karim Soliman / جنى كريم سليمان', 'email' => 'jana.soliman@nuh.example', 'national_id' => '30602241234587', 'dob' => '2006-02-24', 'gender' => 'female', 'phone' => '01045454545', 'address' => 'Cairo', 'blood_type' => 'O+', 'blood_pressure' => '106/70', 'pulse_rate' => 82, 'temperature' => 36.8, 'weight' => 50, 'medical_condition' => 'Dermatology follow-up', 'medications' => []],
            ['full_name' => 'Nour El-Din Samir / نور الدين سمير', 'email' => 'nour.samir@nuh.example', 'national_id' => '29101081234588', 'dob' => '1991-01-08', 'gender' => 'male', 'phone' => '01156565656', 'address' => 'Giza', 'blood_type' => 'AB-', 'blood_pressure' => '124/79', 'pulse_rate' => 73, 'temperature' => 36.7, 'weight' => 77, 'medical_condition' => 'Radiology referral', 'medications' => []],
            ['full_name' => 'Yara Hany Maged / يارا هاني ماجد', 'email' => 'yara.maged@nuh.example', 'national_id' => '29610191234589', 'dob' => '1996-10-19', 'gender' => 'female', 'phone' => '01267676767', 'address' => 'Cairo', 'blood_type' => 'B+', 'blood_pressure' => '115/74', 'pulse_rate' => 75, 'temperature' => 36.8, 'weight' => 59, 'medical_condition' => 'Gynecology follow-up', 'medications' => []],
            ['full_name' => 'Ibrahim Sami Badr / إبراهيم سامي بدر', 'email' => 'ibrahim.badr@nuh.example', 'national_id' => '28205221234590', 'dob' => '1982-05-22', 'gender' => 'male', 'phone' => '01078787878', 'address' => 'Qalyubia', 'blood_type' => 'O-', 'blood_pressure' => '132/82', 'pulse_rate' => 76, 'temperature' => 36.9, 'weight' => 85, 'medical_condition' => 'Internal medicine review', 'medications' => []],
        ];
    }
}
