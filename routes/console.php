<?php

use Illuminate\Foundation\Inspiring;
use App\Models\Department;
use App\Models\Appointment;
use App\Support\NormalizesDepartments;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('appointments:repair-patient-links', function () {
    $nullPatientCount = Appointment::whereNull('patient_id')->count();

    $invalidPatientAppointments = Appointment::whereNotNull('patient_id')
        ->whereDoesntHave('patient')
        ->get(['id', 'patient_id']);

    $invalidIds = $invalidPatientAppointments->pluck('id');

    if ($invalidIds->isNotEmpty()) {
        Appointment::whereIn('id', $invalidIds)->update(['patient_id' => null]);
    }

    $missingIdentityCount = Appointment::whereNull('patient_id')
        ->where(function ($query) {
            $query->whereNull('first_name')
                ->orWhere('first_name', '')
                ->where(function ($inner) {
                    $inner->whereNull('last_name')
                        ->orWhere('last_name', '');
                });
        })
        ->count();

    if ($missingIdentityCount > 0) {
        Appointment::whereNull('patient_id')
            ->where(function ($query) {
                $query->whereNull('first_name')
                    ->orWhere('first_name', '')
                    ->where(function ($inner) {
                        $inner->whereNull('last_name')
                            ->orWhere('last_name', '');
                    });
            })
            ->update([
                'first_name' => 'Unknown',
                'last_name' => 'Patient',
            ]);
    }

    $this->info("Appointments with NULL patient_id: {$nullPatientCount}");
    $this->info("Invalid patient links fixed: {$invalidIds->count()}");
    $this->info("Missing anonymous identity fixed: {$missingIdentityCount}");
})->purpose('Repair appointment records with missing or invalid patient links');

Artisan::command('doctors:normalize-identities {--dry-run : Preview the merge without writing changes}', function () {
    $foreignKeyTables = [
        'admin_appointments',
        'appointments',
        'departments',
        'document_signatures',
        'leave_requests',
        'reports',
        'staff_leave_requests',
    ];

    $duplicateGroups = DB::table('doctors')
        ->selectRaw('LOWER(TRIM(name)) as normalized_name, COUNT(*) as duplicate_count')
        ->groupBy('normalized_name')
        ->havingRaw('COUNT(*) > 1')
        ->orderBy('normalized_name')
        ->get();

    if ($duplicateGroups->isEmpty()) {
        $this->info('No duplicate doctors found.');
        return self::SUCCESS;
    }

    $summary = [];
    $sqlPreview = [];

    DB::beginTransaction();

    try {
        foreach ($duplicateGroups as $group) {
            $doctors = DB::table('doctors')
                ->leftJoin('appointments', 'appointments.doctor_id', '=', 'doctors.id')
                ->whereRaw('LOWER(TRIM(doctors.name)) = ?', [$group->normalized_name])
                ->groupBy(
                    'doctors.id',
                    'doctors.name',
                    'doctors.email',
                    'doctors.password',
                    'doctors.department_id',
                    'doctors.status',
                    'doctors.specialization',
                    'doctors.experience',
                    'doctors.rating',
                    'doctors.image',
                    'doctors.created_at',
                    'doctors.updated_at'
                )
                ->selectRaw('
                    doctors.*,
                    CASE WHEN doctors.password IS NULL OR doctors.password = "" THEN 0 ELSE 1 END as has_password,
                    COUNT(appointments.id) as appointments_count
                ')
                ->orderByDesc('has_password')
                ->orderByDesc('appointments_count')
                ->orderBy('id')
                ->get();

            $canonical = $doctors->first();
            $duplicates = $doctors->skip(1)->values();

            if (!$canonical || $duplicates->isEmpty()) {
                continue;
            }

            $duplicateIds = $duplicates->pluck('id')->all();
            $duplicateIdList = implode(',', $duplicateIds);
            $groupSummary = [
                'name' => $canonical->name,
                'canonical_id' => $canonical->id,
                'canonical_email' => $canonical->email,
                'merged_ids' => $duplicateIds,
                'table_updates' => [],
            ];

            foreach ($foreignKeyTables as $table) {
                $affected = DB::table($table)
                    ->whereIn('doctor_id', $duplicateIds)
                    ->count();

                if ($affected === 0) {
                    continue;
                }

                DB::table($table)
                    ->whereIn('doctor_id', $duplicateIds)
                    ->update(['doctor_id' => $canonical->id]);

                $groupSummary['table_updates'][$table] = $affected;
                $sqlPreview[] = sprintf(
                    'UPDATE %s SET doctor_id = %d WHERE doctor_id IN (%s);',
                    $table,
                    $canonical->id,
                    $duplicateIdList
                );
            }

            $sqlPreview[] = sprintf(
                '-- DELETE SKIPPED: duplicate doctors left intact after repointing relationships. Candidate IDs: %s;',
                $duplicateIdList
            );

            $summary[] = $groupSummary;
        }
        if ($this->option('dry-run')) {
            DB::rollBack();
        } else {
            DB::commit();
        }
    } catch (\Throwable $e) {
        DB::rollBack();
        throw $e;
    }

    $this->info($this->option('dry-run') ? 'Dry run complete. No changes were written.' : 'Doctor normalization complete.');

    foreach ($summary as $group) {
        $this->line(sprintf(
            '%s => keep #%d (%s), merge [%s]',
            $group['name'],
            $group['canonical_id'],
            $group['canonical_email'] ?? 'no-email',
            implode(', ', $group['merged_ids'])
        ));

        foreach ($group['table_updates'] as $table => $count) {
            $this->line("  {$table}: {$count} row(s) repointed");
        }
    }

    $this->newLine();
    $this->line('SQL preview:');
    foreach ($sqlPreview as $statement) {
        $this->line($statement);
    }

    return self::SUCCESS;
})->purpose('Normalize duplicate doctor identities and repoint related doctor_id foreign keys');

Artisan::command('departments:normalize', function () {
    $beforeCount = Department::count();
    $summary = NormalizesDepartments::sync();
    $afterCount = Department::count();

    $this->info('Department normalization complete.');
    $this->line("Departments before: {$beforeCount}");
    $this->line("Departments after: {$afterCount}");
    $this->line("Created: {$summary['created']}");
    $this->line("Updated: {$summary['updated']}");
    $this->line("Merged duplicates removed: {$summary['merged']}");
    $this->line("Deleted invalid rows: {$summary['deleted']}");
    $this->line("Deletes skipped by safeguard: " . ($summary['delete_skipped'] ?? 0));

    $this->newLine();
    $this->line('Final department list:');

    foreach (Department::orderBy('name_en')->pluck('name_en') as $name) {
        $this->line(" - {$name}");
    }

    return self::SUCCESS;
})->purpose('Normalize department names to English, merge duplicates, and clean invalid rows');
