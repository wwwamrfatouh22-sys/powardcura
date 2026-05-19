<?php
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\AdminAppointmentController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDepartmentController;
use App\Http\Controllers\AdminDoctorController;
use App\Http\Controllers\AdminPatientController;
use App\Http\Controllers\AdminSettingsController;
use App\Http\Controllers\AdminRoomController;
use App\Http\Controllers\AdminScheduleController;
use App\Http\Controllers\AdminStaffController;
use App\Http\Controllers\AdminMedicalResultController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ElectronicSignatureController;
use App\Http\Controllers\FawryPaymentController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RatingController;
use App\Http\Controllers\RadiologyLabResultsController;
use App\Http\Controllers\ResultsController;
use App\Http\Controllers\StaffAuthController;
use App\Http\Controllers\StaffComplaintController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\StaffJobController;
use App\Http\Controllers\StaffJobApplicationController;
use App\Http\Controllers\StaffLeaveController;
use App\Http\Controllers\StaffMedicalPositionController;
use App\Http\Controllers\StaffRadiologyLabController;
use App\Http\Controllers\StaffTrainingProgramController;
use App\Http\Controllers\TrainingProgramController;
use App\Http\Controllers\WebsiteRatingController;
use App\Models\Department;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/locale/{locale}', function (string $locale) {
    abort_unless(in_array($locale, ['en', 'ar'], true), 404);

    session(['locale' => $locale]);

    return redirect()->back();
})->name('locale.switch');

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/staff-module/jobs', [JobController::class, 'index'])->name('staff.module.jobs');
Route::get('/staff-module/jobs/{job}', [JobController::class, 'show'])->name('jobs.show');
Route::get('/staff-module/training-programs', [TrainingProgramController::class, 'index'])->name('staff.module.training');
Route::get('/training-programs/{program}/register', [TrainingProgramController::class, 'register'])->name('training.register');
Route::post('/training-programs/apply', [TrainingProgramController::class, 'storeApplication'])->name('training.apply.store');
Route::get('/training-programs', [TrainingProgramController::class, 'index'])->name('training.index');
Route::get('/radiology-results', [RadiologyLabResultsController::class, 'radiologyIndex'])->name('radiology_results.index');
Route::post('/radiology-results', [RadiologyLabResultsController::class, 'radiologySearch'])->name('radiology_results.search');
Route::get('/radiology-results/{id}/preview', [RadiologyLabResultsController::class, 'radiologyPreview'])->name('radiology_results.preview');
Route::get('/radiology-results/{id}/download', [RadiologyLabResultsController::class, 'radiologyDownload'])->name('radiology_results.download');
Route::get('/laboratory-results', [RadiologyLabResultsController::class, 'laboratoryIndex'])->name('laboratory_results.index');
Route::post('/laboratory-results', [RadiologyLabResultsController::class, 'laboratorySearch'])->name('laboratory_results.search');
Route::get('/laboratory-results/{id}/preview', [RadiologyLabResultsController::class, 'laboratoryPreview'])->name('laboratory_results.preview');
Route::get('/laboratory-results/{id}/download', [RadiologyLabResultsController::class, 'laboratoryDownload'])->name('laboratory_results.download');
Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');

Route::match(['GET', 'POST'], '/payments/fawry/callback', [FawryPaymentController::class, 'callback'])->name('payments.fawry.callback');
Route::post('/payments/fawry/webhook', [FawryPaymentController::class, 'webhook'])->name('payments.fawry.webhook');

// ## Patient Login
Route::get('/patient/login', [AuthController::class, 'showLoginForm'])->name('patient.login');
Route::post('/patient/login', [AuthController::class, 'login'])->name('patient.login.submit');
Route::get('/login', [AuthController::class,'selectlogintype'])->name('login');
Route::get('/password/{role}/forgot', [PasswordResetController::class, 'requestForm'])->name('password.request');
Route::post('/password/{role}/email', [PasswordResetController::class, 'sendLink'])->name('password.email');
Route::get('/password/{role}/reset/{token}', [PasswordResetController::class, 'resetForm'])->name('password.reset');
Route::post('/password/{role}/reset', [PasswordResetController::class, 'reset'])->name('password.update');

// ## Doctor Routes
Route::get('/doctor/login', [AuthController::class, 'showDoctorLogin'])->name('doctor.login');
Route::post('/doctor/login', [AuthController::class, 'doctorLogin'])->name('doctor.login.submit');
Route::get('/doctor/profile', [DoctorController::class, 'profile'])->middleware(['auth:doctor', 'role:doctor'])->name('doctor.profile');
Route::get('/doctor/appointments', [DoctorController::class, 'appointments'])->middleware(['auth:doctor', 'role:doctor'])->name('doctor.appointments');
Route::get('/doctor/api/appointments', [DoctorController::class, 'appointmentsJson'])->middleware(['auth:doctor', 'role:doctor'])->name('doctor.appointments.api');

Route::middleware(['auth:doctor', 'role:doctor'])->group(function () {
    Route::get('/electronic-signature', [ElectronicSignatureController::class, 'redirectToLatest'])->name('doctor.signature');
    Route::get('/electronic-signature/{document}', [ElectronicSignatureController::class, 'show'])->name('signature.show');
    Route::post('/save-signature', [ElectronicSignatureController::class, 'store'])->name('signature.store');
    Route::get('/doctor/leave-request', [DoctorController::class, 'leaveForm'])->name('doctor.leave.form');
    Route::post('/doctor/leave-request', [DoctorController::class, 'storeLeaveRequest'])->name('doctor.leave.store');
    Route::post('/doctor/appointments/{appointment}/complete', [DoctorController::class, 'completeAppointment'])->name('doctor.appointments.complete');
    Route::post('/doctor/appointments/{appointment}/diagnostics/{type}', [DoctorController::class, 'storeDiagnosticRequest'])->name('doctor.diagnostics.store');
    Route::get('/doctor/diagnostics/{type}/{id}/download', [DoctorController::class, 'downloadDiagnosticResult'])->name('doctor.diagnostics.download');
});

// Staff Routes
Route::prefix('staff')->group(function () {

    Route::get('/login', [StaffAuthController::class, 'showLogin'])->name('staff.login');
    Route::post('/login', [StaffAuthController::class, 'login'])->name('staff.login.submit');
    Route::middleware(['auth:staff', 'role:staff'])->group(function () {
        Route::get('/dashboard', [StaffDashboardController::class, 'index'])->name('staff.dashboard');
        Route::middleware('staff.role:radiology_lab,radiology,laboratory,lab')->group(function () {
            Route::get('/radiology-lab/{section?}', [StaffRadiologyLabController::class, 'index'])->name('staff.radiology_lab');
            Route::post('/radiology-lab/{type}/results', [StaffRadiologyLabController::class, 'store'])->name('staff.radiology_lab.results.store');
            Route::post('/diagnostics/{type}/{id}/processing', [StaffRadiologyLabController::class, 'process'])->name('staff.diagnostics.processing');
            Route::post('/diagnostics/{type}/{id}/complete', [StaffRadiologyLabController::class, 'complete'])->name('staff.diagnostics.complete');
        });
        Route::middleware('staff.not_radiology_lab')->group(function () {
        Route::get('/leave-requests', [StaffLeaveController::class, 'index'])->name('staff.leave.index');
        Route::post('/leave-requests/{id}/approve', [StaffLeaveController::class, 'approve'])->name('staff.leave.approve');
        Route::post('/leave-requests/{id}/reject', [StaffLeaveController::class, 'reject'])->name('staff.leave.reject');
        Route::get('/job-applications', [StaffJobApplicationController::class, 'index'])->name('staff.job.applications');
        Route::post('/job-applications/{id}/approve', [StaffJobApplicationController::class, 'approve'])->name('staff.job.approve');
        Route::post('/job-applications/{id}/reject', [StaffJobApplicationController::class, 'reject'])->name('staff.job.reject');
        Route::post('/job-applications/{id}/status', [StaffJobApplicationController::class, 'updateStatus'])->name('staff.job.status');
        Route::get('/job-applications/{id}/cv/view', [StaffJobApplicationController::class, 'viewCv'])->name('staff.job.cv.view');
        Route::get('/job-applications/{id}/cv', [StaffJobApplicationController::class, 'downloadCv'])->name('staff.job.cv.download');
        Route::get('/jobs', [StaffJobController::class, 'index'])->name('staff.jobs.index');
        Route::post('/jobs', [StaffJobController::class, 'store'])->name('staff.jobs.store');
        Route::get('/medical-positions', [StaffMedicalPositionController::class,'index'])->name('staff.medical.positions');
        Route::post('/medical-positions/{id}/approve',[StaffMedicalPositionController::class,'approve'])->name('staff.medical.approve');
        Route::post('/medical-positions/{id}/reject',[StaffMedicalPositionController::class,'reject'])->name('staff.medical.reject');
        Route::get('/administrative-positions',[StaffMedicalPositionController::class,'administrative'])->name('staff.administrative.positions');
        Route::get('/training-programs', [StaffTrainingProgramController::class,'index'])->name('staff.training.programs');
        Route::post('/training-programs/{id}/approve', [StaffTrainingProgramController::class,'approve'])->name('staff.training.approve');
        Route::post('/training-programs/{id}/reject', [StaffTrainingProgramController::class,'reject'])->name('staff.training.reject');
        Route::get('/training-programs/{id}/cv/view', [StaffTrainingProgramController::class, 'viewCv'])->name('staff.training.cv.view');
        Route::get('/training-programs/{id}/cv', [StaffTrainingProgramController::class, 'downloadCv'])->name('staff.training.cv.download');
        Route::get('/complaints',[StaffComplaintController::class,'index'])->name('staff.complaints');
        Route::post('/complaints/{id}/resolve', [StaffComplaintController::class,'resolve'])->name('staff.complaint.resolve');
        Route::post('/complaints/{id}/escalate', [StaffComplaintController::class,'escalate'])->name('staff.complaint.escalate');
        Route::get('/leave/create',[StaffLeaveController::class,'create'])->name('staff.leave.create');
        Route::post('/leave/store',[StaffLeaveController::class,'store'])->name('staff.leave.store');
        });
        Route::post('/logout', function () {
            \App\Support\AuditLogger::log('logout', null, ['role' => 'staff']);
            Auth::guard('staff')->logout();

            return redirect('/');
        })->name('staff.logout');
    });
});

// ## Auth Routes
Route::middleware(['auth:patient', 'role:patient'])->group(function () {
    Route::get('/book', [AppointmentController::class, 'start'])->name('book.start');
 Route::get('/appointment/{appointment}/invoice', [AppointmentController::class, 'invoice'])->name('appointments.invoice');
 Route::get('/appointment/{doctor}/{time}', [AppointmentController::class, 'create'])->name('appointments.create');
 Route::post('/appointments/review', [AppointmentController::class, 'review'])->name('appointments.review');
 Route::get('/appointments/payment', [AppointmentController::class, 'payment'])->name('appointments.payment');
 Route::post('/appointments/confirm', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
 Route::get('/appointments/{appointment}/rate', [AppointmentController::class, 'rate'])->name('appointments.rate');
 Route::post('/appointments/{appointment}/cancel', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
 Route::get('/appointments/{appointment}/reschedule-slots', [AppointmentController::class, 'rescheduleSlots'])->name('appointments.reschedule-slots');
 Route::post('/appointments/{appointment}/reschedule', [AppointmentController::class, 'reschedule'])->name('appointments.reschedule');
 Route::post('/site-rating/{appointment}', [WebsiteRatingController::class, 'store'])->name('site.ratings.store');

 Route::get('/results', [ResultsController::class, 'index'])->name('results.index');
 Route::post('/results/search', [ResultsController::class, 'search'])->name('results.search');
 Route::get('/results/download/{type}/{id}', [ResultsController::class, 'download'])->name('results.download');
 Route::get('/complaints', [ComplaintController::class, 'create'])->name('complaints.create');
 Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');

 Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
 Route::get('/profile/data', [ProfileController::class, 'data'])->name('profile.data');
 Route::get('/profile/prescriptions', [ProfileController::class, 'prescriptions'])->name('profile.prescriptions');
 Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
 Route::post('/doctor-rating/{appointment}', [RatingController::class, 'store'])->name('doctor.ratings.store');
});

// ## Admin Routes
Route::get('/admin/login', [AdminAuthController::class,'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class,'login'])->name('admin.login.submit');
Route::middleware(['auth:admin', 'role:admin'])->group(function(){
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/appointments', [AdminAppointmentController::class,'index'])->name('admin.appointments');
    Route::get('/admin/appointments/create', [AdminAppointmentController::class, 'create'])->name('admin.appointments.create');
    Route::post('/admin/appointments/store',[AdminAppointmentController::class,'store'])->name('admin.appointments.store');

    Route::get('/admin/appointments/edit/{id}', [AdminAppointmentController::class, 'edit'])->name('admin.appointments.edit');
    Route::put('/admin/appointments/update/{id}', [AdminAppointmentController::class, 'update'])->name('admin.appointments.update');
    Route::delete('/admin/appointments/delete/{id}', [AdminAppointmentController::class, 'delete'])->name('admin.appointments.delete');

    Route::get('/admin/patients', [AdminPatientController::class, 'index'])->name('admin.patients');
    Route::delete('/admin/patients/{id}', [AdminPatientController::class, 'destroy'])->name('admin.patients.delete');
    Route::get('/admin/patients/{id}/edit', [AdminPatientController::class, 'edit'])->name('admin.patients.edit');
    Route::get('/admin/patients/{patient}/results', [ResultsController::class, 'admin'])->name('admin.patients.results');
    Route::get('/admin/results/download/{type}/{id}', [ResultsController::class, 'download'])->name('admin.results.download');
    Route::post('/admin/patients/{id}/update', [AdminPatientController::class, 'update'])->name('admin.patients.update');
    Route::get('/admin/patients/create', [AdminPatientController::class, 'create'])->name('admin.patients.create');
    Route::post('/admin/patients/store', [AdminPatientController::class, 'store'])->name('admin.patients.store');
    Route::get('/admin/doctors', [AdminDoctorController::class, 'index'])->name('admin.doctors');
    Route::get('/admin/doctors/create', [AdminDoctorController::class, 'create'])->name('admin.doctors.create');
    Route::post('/admin/doctors/store', [AdminDoctorController::class, 'store'])->name('admin.doctors.store');
    Route::get('/admin/doctors/edit/{id}', [AdminDoctorController::class, 'edit'])->name('admin.doctors.edit');
    Route::post('/admin/doctors/update/{id}', [AdminDoctorController::class, 'update'])->name('admin.doctors.update');
    Route::delete('/admin/doctors/delete/{id}', [AdminDoctorController::class, 'destroy'])->name('admin.doctors.delete');
    Route::get('/admin/staff', [AdminStaffController::class, 'index'])->name('admin.staff');
    Route::get('/admin/staff/create', [AdminStaffController::class, 'create'])->name('admin.staff.create');
    Route::post('/admin/staff/store', [AdminStaffController::class, 'store'])->name('admin.staff.store');
    Route::get('/admin/staff/edit/{id}', [AdminStaffController::class, 'edit'])->name('admin.staff.edit');
    Route::post('/admin/staff/update/{id}', [AdminStaffController::class, 'update'])->name('admin.staff.update');
    Route::delete('/admin/staff/delete/{id}', [AdminStaffController::class, 'destroy'])->name('admin.staff.delete');
    Route::get('/admin/radiology', [AdminMedicalResultController::class, 'radiology'])->name('admin.radiology');
    Route::get('/admin/laboratory', [AdminMedicalResultController::class, 'laboratory'])->name('admin.laboratory');
    Route::get('/admin/doctors/{doctor}/schedule', [AdminScheduleController::class, 'edit'])->name('admin.doctors.schedule');
    Route::post('/admin/doctors/{doctor}/schedule/apply-template', [AdminScheduleController::class, 'applyTemplate'])->name('admin.doctors.schedule.apply_template');
    Route::post('/admin/doctors/{doctor}/schedule/availability', [AdminScheduleController::class, 'saveAvailability'])->name('admin.doctors.schedule.availability');
    Route::post('/admin/doctors/{doctor}/schedule/shifts', [AdminScheduleController::class, 'storeShift'])->name('admin.doctors.schedule.shifts.store');
    Route::put('/admin/doctors/{doctor}/schedule/shifts/{schedule}', [AdminScheduleController::class, 'updateShift'])->name('admin.doctors.schedule.shifts.update');
    Route::delete('/admin/doctors/{doctor}/schedule/shifts/{schedule}', [AdminScheduleController::class, 'deleteShift'])->name('admin.doctors.schedule.shifts.delete');
    Route::post('/admin/doctors/{doctor}/schedule/time-off', [AdminScheduleController::class, 'storeTimeOff'])->name('admin.doctors.schedule.time-off.store');
    Route::delete('/admin/doctors/{doctor}/schedule/time-off/{timeOff}', [AdminScheduleController::class, 'deleteTimeOff'])->name('admin.doctors.schedule.time-off.delete');
    Route::post('/admin/doctors/{doctor}/schedule/blocked-times', [AdminScheduleController::class, 'storeBlockedTime'])->name('admin.doctors.schedule.blocked-times.store');
    Route::put('/admin/doctors/{doctor}/schedule/blocked-times/{blockedTime}', [AdminScheduleController::class, 'updateBlockedTime'])->name('admin.doctors.schedule.blocked-times.update');
    Route::delete('/admin/doctors/{doctor}/schedule/blocked-times/{blockedTime}', [AdminScheduleController::class, 'deleteBlockedTime'])->name('admin.doctors.schedule.blocked-times.delete');
    Route::get('/admin/rooms', [AdminRoomController::class, 'index'])->name('admin.rooms');
    Route::get('/admin/rooms/create', [AdminRoomController::class, 'create'])->name('admin.rooms.create');
    Route::post('/rooms/store', [AdminRoomController::class, 'store'])->name('admin.rooms.store');
    Route::get('/admin/rooms/edit/{id}', [AdminRoomController::class, 'edit'])->name('admin.rooms.edit');
    Route::post('/rooms/{id}/update', [AdminRoomController::class, 'update'])->name('admin.rooms.update');
    Route::delete('/admin/rooms/delete/{id}', [AdminRoomController::class, 'destroy'])->name('admin.rooms.delete');

    Route::get('/admin/departments', [AdminDepartmentController::class, 'index'])->name('admin.departments');
    Route::get('/admin/departments/create', [AdminDepartmentController::class, 'create'])->name('admin.departments.create');
    Route::post('/admin/departments/store', [AdminDepartmentController::class, 'store'])->name('admin.departments.store');

    Route::get('/admin/departments/edit/{id}', [AdminDepartmentController::class, 'edit'])->name('admin.departments.edit');
    Route::post('/admin/departments/update/{id}', [AdminDepartmentController::class, 'update'])->name('admin.departments.update');
    Route::delete('/admin/departments/delete/{id}', [AdminDepartmentController::class, 'destroy'])->name('admin.departments.delete');
    Route::get('/admin/settings', [AdminSettingsController::class, 'index'])->name('admin.settings');
    Route::post('/admin/settings', [AdminSettingsController::class, 'update'])->name('admin.settings.update');
    Route::delete('/admin/settings/users/{role}/{id}', [AdminSettingsController::class, 'destroy'])->name('admin.settings.users.destroy');
    Route::patch('/admin/settings/users/{role}/{id}/restore', [AdminSettingsController::class, 'restore'])->name('admin.settings.users.restore');

});



Route::post('/logout', [AuthController::class, 'logout'])->middleware(['auth:patient', 'role:patient'])->name('logout');
Route::get('/medical-positions',[JobController::class,'medical'])->name('jobs.medical');
Route::get('/jobs/{job}/apply', [JobApplicationController::class, 'create'])->name('jobs.apply');
Route::post('/jobs/apply', [JobApplicationController::class, 'store'])->name('jobs.apply.store');
Route::redirect('/departments', '/#departments-section')->name('departments.index');
Route::get('/departments/{department}', function (Department $department) {
    return redirect()->route('specialties.doctors', $department);
})->name('departments.show');
Route::get('/specialties/{department}/doctors', [DepartmentController::class, 'showDoctors'])->name('specialties.doctors');
Route::get('/doctors/{doctor}/booked-slots', [DoctorController::class, 'bookedSlots'])->name('doctors.booked-slots');
Route::get('/doctors/{doctor}', [DoctorController::class, 'show'])->name('doctors.show');






Route::get('/chat', function () {
    return view('chat');
});








//Route::get('/departments', [DepartmentController::class,'index'])->name('departments');
//Route::get('/departments/{department}', [DepartmentController::class, 'show'])->name('departments.show');
//Route::get('/departments', [DepartmentController::class, 'index'])->middleware('auth')->name('departments.index');
