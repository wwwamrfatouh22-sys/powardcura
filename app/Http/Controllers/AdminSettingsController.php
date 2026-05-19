<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Staff;
use App\Models\SystemSetting;
use App\Support\DeletionGuard;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AdminSettingsController extends Controller
{
    private const DEFAULTS = [
        'system_name' => 'Nahada University Hospital',
        'app_name' => 'NUH Admin',
        'logo_path' => 'images/logo_Image.png',
        'default_language' => 'en',
        'theme' => 'light',
    ];

    public function index(): View
    {
        $filters = [
            'role' => request('user_role', 'all'),
            'date' => request('user_date', 'all'),
            'status' => request('user_status', 'active'),
        ];

        $settings = collect(self::DEFAULTS)->mapWithKeys(
            fn (mixed $default, string $key) => [$key => SystemSetting::valueFor($key, $default)]
        );

        $admin = auth()->guard('admin')->user();
        $adminName = SystemSetting::valueFor(
            'admin_name',
            $admin?->email ? str($admin->email)->before('@')->headline()->toString() : 'Administrator'
        );

        return view('admin.settings', [
            'settings' => $settings,
            'admin' => $admin,
            'adminName' => $adminName,
            'languageOptions' => [
                'en' => 'English',
                'ar' => 'Arabic',
            ],
            'themeOptions' => [
                'light' => 'Light',
                'dark' => 'Dark',
            ],
            'filters' => $filters,
            'users' => $this->paginateUsers($this->buildUnifiedUsers($filters), request()),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $admin = auth()->guard('admin')->user();

        $validated = $request->validate([
            'system_name' => ['required', 'string', 'max:255'],
            'app_name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'image', 'max:2048'],
            'default_language' => ['required', 'in:en,ar'],
            'theme' => ['nullable', 'in:light,dark'],
            'admin_name' => ['required', 'string', 'max:255'],
            'admin_email' => ['required', 'email', 'max:255', 'unique:admins,email,' . $admin?->id],
            'current_password' => ['nullable', 'required_with:password', 'current_password:admin'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $logoPath = SystemSetting::valueFor('logo_path', self::DEFAULTS['logo_path']);

        if ($request->hasFile('logo')) {
            $file = $request->file('logo');
            $fileName = 'admin-logo.' . $file->getClientOriginalExtension();
            $file->move(public_path('images'), $fileName);
            $logoPath = 'images/' . $fileName;
        }

        $payload = [
            'system_name' => trim($validated['system_name']),
            'app_name' => trim($validated['app_name']),
            'logo_path' => $logoPath,
            'default_language' => $validated['default_language'],
            'theme' => $validated['theme'] ?? 'light',
            'admin_name' => trim($validated['admin_name']),
        ];

        foreach ($payload as $key => $value) {
            SystemSetting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => (string) $value]
            );
        }

        foreach ([
            'hospital_name',
            'license_number',
            'language',
            'time_zone',
            'two_factor_authentication',
            'auto_logout',
            'auto_backup',
            'email_alerts',
            'push_notifications',
        ] as $legacyKey) {
            $setting = SystemSetting::query()->where('key', $legacyKey)->first();

            if ($setting) {
                DeletionGuard::deleteOne($setting, 'system_setting.deleted', [
                    'source' => 'admin_settings_cleanup',
                    'key' => $legacyKey,
                ]);
            }
        }

        if ($admin) {
            $admin->email = $validated['admin_email'];

            if (!empty($validated['password'])) {
                $admin->password = Hash::make($validated['password']);
            }

            $admin->save();
        }

        $request->session()->put('locale', $validated['default_language']);

        return redirect()
            ->route('admin.settings')
            ->with('success', 'Settings updated successfully.');
    }

    public function destroy(Request $request, string $role, int $id): RedirectResponse
    {
        $user = $this->resolveManagedUser($role, $id, true);

        if ($user->trashed()) {
        return redirect()
            ->route('admin.settings', $request->only(['user_role', 'user_date', 'user_status', 'page']))
            ->with('success', 'User is already deleted.');
        }

        DeletionGuard::deleteOne($user, $role . '.deleted', ['source' => 'admin_settings']);

        return redirect()
            ->route('admin.settings', $request->only(['user_role', 'user_date', 'user_status', 'page']))
            ->with('success', 'User deleted safely. You can restore them from the Deleted filter.');
    }

    public function restore(Request $request, string $role, int $id): RedirectResponse
    {
        $user = $this->resolveManagedUser($role, $id, true);

        if ($user->trashed()) {
            $user->restore();
        }

        return redirect()
            ->route('admin.settings', $request->only(['user_role', 'user_date', 'user_status', 'page']))
            ->with('success', 'User restored successfully.');
    }

    private function buildUnifiedUsers(array $filters): Collection
    {
        return collect()
            ->merge($this->doctorRows($filters))
            ->merge($this->patientRows($filters))
            ->merge($this->staffRows($filters))
            ->when(
                $filters['role'] !== 'all',
                fn (Collection $users) => $users->where('role_key', $filters['role'])->values()
            )
            ->when(
                $filters['date'] !== 'all',
                fn (Collection $users) => $this->filterUsersByDate($users, $filters['date'])
            )
            ->sortByDesc('created_at_sort')
            ->values();
    }

    private function doctorRows(array $filters): Collection
    {
        if (!Schema::hasTable('doctors')) {
            return collect();
        }

        return $this->applyStatusFilter(Doctor::query(), $filters['status'])
            ->latest()
            ->get()
            ->map(fn (Doctor $doctor) => $this->formatUserRow(
                id: $doctor->id,
                name: $doctor->name,
                email: $doctor->email,
                phone: null,
                role: 'Doctor',
                roleKey: 'doctor',
                createdAt: $doctor->created_at,
                deletedAt: $doctor->deleted_at,
            ));
    }

    private function patientRows(array $filters): Collection
    {
        if (!Schema::hasTable('patients')) {
            return collect();
        }

        return $this->applyStatusFilter(Patient::query()->with('user:id,email'), $filters['status'])
            ->latest()
            ->get()
            ->map(fn (Patient $patient) => $this->formatUserRow(
                id: $patient->id,
                name: $patient->full_name,
                email: $patient->user?->email,
                phone: $patient->phone,
                role: 'Patient',
                roleKey: 'patient',
                createdAt: $patient->created_at,
                deletedAt: $patient->deleted_at,
            ));
    }

    private function staffRows(array $filters): Collection
    {
        if (!Schema::hasTable('staff')) {
            return collect();
        }

        return $this->applyStatusFilter(Staff::query(), $filters['status'])
            ->latest()
            ->get()
            ->map(fn (Staff $staff) => $this->formatUserRow(
                id: $staff->id,
                name: $staff->name,
                email: $staff->email,
                phone: null,
                role: 'Staff',
                roleKey: 'staff',
                createdAt: $staff->created_at,
                deletedAt: $staff->deleted_at,
            ));
    }

    private function applyStatusFilter($query, string $status)
    {
        return match ($status) {
            'all' => $query->withTrashed(),
            'deleted' => $query->onlyTrashed(),
            default => $query,
        };
    }

    private function filterUsersByDate(Collection $users, string $dateFilter): Collection
    {
        $now = now();

        return $users->filter(function (array $user) use ($dateFilter, $now): bool {
            $createdAt = $user['created_at_sort'];

            if (!$createdAt instanceof Carbon) {
                return false;
            }

            return match ($dateFilter) {
                'today' => $createdAt->isSameDay($now),
                'week' => $createdAt->greaterThanOrEqualTo($now->copy()->startOfWeek()),
                'month' => $createdAt->greaterThanOrEqualTo($now->copy()->startOfMonth()),
                default => true,
            };
        })->values();
    }

    private function formatUserRow(
        int $id,
        ?string $name,
        ?string $email,
        ?string $phone,
        string $role,
        string $roleKey,
        mixed $createdAt,
        mixed $deletedAt,
    ): array {
        $createdAt = $createdAt ? Carbon::parse($createdAt) : null;
        $deletedAt = $deletedAt ? Carbon::parse($deletedAt) : null;

        return [
            'id' => $id,
            'name' => $name ?: '-',
            'email' => $email ?: '-',
            'phone' => $phone ?: '-',
            'role' => $role,
            'role_key' => $roleKey,
            'status' => $deletedAt ? 'Deleted' : 'Active',
            'created_at' => $createdAt?->format('M d, Y') ?? '-',
            'created_at_sort' => $createdAt,
            'deleted_at' => $deletedAt,
        ];
    }

    private function paginateUsers(Collection $users, Request $request): LengthAwarePaginator
    {
        $perPage = 10;
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator(
            $users->forPage($page, $perPage)->values(),
            $users->count(),
            $perPage,
            $page,
            [
                'path' => $request->url(),
                'query' => $request->query(),
            ]
        );
    }

    private function resolveManagedUser(string $role, int $id, bool $withTrashed = false): Model
    {
        $modelClass = match ($role) {
            'doctor' => Doctor::class,
            'patient' => Patient::class,
            'staff' => Staff::class,
            default => abort(404),
        };

        $query = $modelClass::query();

        if ($withTrashed) {
            $query->withTrashed();
        }

        return $query->findOrFail($id);
    }
}
