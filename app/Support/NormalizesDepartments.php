<?php

namespace App\Support;

use App\Models\Department;
use Illuminate\Support\Facades\DB;

class NormalizesDepartments
{
    public static function sync(): array
    {
        $summary = [
            'created' => 0,
            'updated' => 0,
            'merged' => 0,
            'deleted' => 0,
            'delete_skipped' => 0,
        ];

        $foreignKeyTables = [
            'doctors',
            'appointments',
            'training_programs',
            'medical_positions',
            'reports',
        ];

        DB::transaction(function () use (&$summary, $foreignKeyTables) {
            $departments = Department::query()->orderBy('id')->get();
            $groups = [];

            foreach ($departments as $department) {
                $normalized = DepartmentCatalog::normalize($department->name_en, $department->name_ar);

                if ($normalized === null) {
                    $normalized = DepartmentCatalog::cleanEnglish($department->name_en ?: $department->name_ar);
                }

                $groups[$normalized][] = $department;
            }

            foreach (DepartmentCatalog::requiredPairs() as $requiredDepartment) {
                $requiredName = $requiredDepartment['name_en'];

                if (! array_key_exists($requiredName, $groups)) {
                    $department = Department::create([
                        'name_en' => $requiredName,
                        'name_ar' => $requiredDepartment['name_ar'],
                        'status' => 'active',
                    ]);

                    $groups[$requiredName] = [$department];
                    $summary['created']++;
                }
            }

            foreach ($groups as $normalizedName => $items) {
                $canonical = collect($items)
                    ->sortByDesc(function (Department $department) use ($foreignKeyTables) {
                        return self::referenceCount($department->id, $foreignKeyTables) * 1000
                            + ($department->status === 'active' ? 100 : 0)
                            + ($department->doctor_id ? 10 : 0)
                            - $department->id;
                    })
                    ->first();

                if (! $canonical) {
                    continue;
                }

                $targetName = $normalizedName !== '' ? $normalizedName : $canonical->name_en;
                $targetPair = DepartmentCatalog::pairFromInput($targetName, $canonical->name_ar);
                $targetEnglish = $targetPair['name_en'] ?? $targetName;
                $targetArabic = $targetPair['name_ar'] ?? $canonical->name_ar;
                $changed = false;

                if ($canonical->name_en !== $targetEnglish) {
                    $canonical->name_en = $targetEnglish;
                    $changed = true;
                }

                if ($canonical->name_ar !== $targetArabic) {
                    $canonical->name_ar = $targetArabic;
                    $changed = true;
                }

                if ($canonical->status === null || $canonical->status === '') {
                    $canonical->status = 'active';
                    $changed = true;
                }

                if ($changed) {
                    $canonical->save();
                    $summary['updated']++;
                }

                $duplicates = collect($items)
                    ->pluck('id')
                    ->filter(static fn (int $id) => $id !== $canonical->id)
                    ->values();

                if ($duplicates->isNotEmpty()) {
                    foreach ($foreignKeyTables as $table) {
                        DB::table($table)
                            ->whereIn('department_id', $duplicates)
                            ->update(['department_id' => $canonical->id]);
                    }

                    $summary['merged'] += $duplicates->count();
                    $summary['delete_skipped'] += $duplicates->count();

                    AuditLogger::log('department.normalization_delete_skipped', $canonical, [
                        'source' => 'department_normalizer',
                        'canonical_id' => $canonical->id,
                        'duplicate_ids' => $duplicates->all(),
                    ]);
                }
            }

            foreach (Department::query()->orderBy('id')->get() as $department) {
                $normalized = DepartmentCatalog::normalize($department->name_en, $department->name_ar)
                    ?? DepartmentCatalog::cleanEnglish($department->name_en ?: $department->name_ar);

                $references = self::referenceCount($department->id, $foreignKeyTables);

                if (DepartmentCatalog::isSupported($normalized)) {
                    $pair = DepartmentCatalog::pairFromInput($normalized, $department->name_ar);

                    if (
                        $pair !== null
                        && ($department->name_en !== $pair['name_en'] || $department->name_ar !== $pair['name_ar'])
                    ) {
                        $department->update([
                            'name_en' => $pair['name_en'],
                            'name_ar' => $pair['name_ar'],
                        ]);
                        $summary['updated']++;
                    }

                    continue;
                }

                $updates = [];

                if ($department->name_en !== $normalized || $department->name_ar !== $normalized) {
                    $updates['name_en'] = $normalized;
                    $updates['name_ar'] = $normalized;
                }

                if ($references === 0 && $department->status !== 'inactive') {
                    $updates['status'] = 'inactive';
                }

                if ($updates !== []) {
                    $department->update($updates);
                    $summary['updated']++;
                }

                if ($references === 0) {
                    $summary['delete_skipped']++;
                    AuditLogger::log('department.normalization_delete_skipped', $department, [
                        'source' => 'department_normalizer',
                        'record_id' => $department->id,
                        'reason' => 'unsupported_without_references',
                    ]);
                }
            }

            foreach (DepartmentCatalog::aliases() as $alias => $canonicalName) {
                DB::table('patients')
                    ->whereRaw('LOWER(TRIM(COALESCE(department, ""))) = ?', [trim($alias)])
                    ->update(['department' => $canonicalName]);
            }

            $existingNames = Department::query()->pluck('name_en')->all();

            foreach (DepartmentCatalog::requiredPairs() as $requiredDepartment) {
                $requiredName = $requiredDepartment['name_en'];

                if (! in_array($requiredName, $existingNames, true)) {
                    Department::create([
                        'name_en' => $requiredName,
                        'name_ar' => $requiredDepartment['name_ar'],
                        'status' => 'active',
                    ]);

                    $summary['created']++;
                }
            }
        });

        return $summary;
    }

    private static function referenceCount(int $departmentId, array $tables): int
    {
        $count = 0;

        foreach ($tables as $table) {
            $count += DB::table($table)->where('department_id', $departmentId)->count();
        }

        return $count;
    }
}
