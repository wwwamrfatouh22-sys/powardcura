<?php
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\AdminAppointmentController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminDepartmentController;
use App\Http\Controllers\AdminDoctorController;
use App\Http\Controllers\AdminPatientController;
use App\Http\Controllers\AdminRoomController;
use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ComplaintController;
use App\Http\Controllers\DepartmentController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\ElectronicSignatureController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\NurseAuthController;
use App\Http\Controllers\NurseDashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResultsController;
use App\Http\Controllers\StaffAuthController;
use App\Http\Controllers\StaffComplaintController;
use App\Http\Controllers\StaffDashboardController;
use App\Http\Controllers\StaffLeaveController;
use App\Http\Controllers\StaffMedicalPositionController;
use App\Http\Controllers\StaffTrainingProgramController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');

// ## Patient Login
Route::get('/patient/login', [AuthController::class, 'showLoginForm'])->name('patient.login');
Route::post('/patient/login', [AuthController::class, 'login'])->name('patient.login.submit');
Route::get('/login', [AuthController::class,'selectlogintype'])->name('login');

// ## Doctor Routes
Route::get('/doctor/login', [AuthController::class, 'showDoctorLogin'])->name('doctor.login');
Route::post('/doctor/login', [AuthController::class, 'doctorLogin'])->name('doctor.login.submit');
Route::get('/doctor/profile', [DoctorController::class, 'profile'])->middleware('auth:doctor')->name('doctor.profile');
Route::get('/doctor/appointments', [DoctorController::class, 'appointments'])->middleware('auth:doctor')->name('doctor.appointments');

Route::get('/electronic-signature', [ElectronicSignatureController::class, 'redirectToLatest'])->name('doctor.signature');
Route::get('/electronic-signature/{document}', [ElectronicSignatureController::class, 'show'])->name('signature.show');
Route::post('/save-signature', [ElectronicSignatureController::class, 'store'])->name('signature.store');

Route::middleware('auth:doctor')->group(function () {
    Route::get('/doctor/leave-request', [DoctorController::class, 'leaveForm'])->name('doctor.leave.form');
    Route::post('/doctor/leave-request', [DoctorController::class, 'storeLeaveRequest'])->name('doctor.leave.store');

});

// ## Nurse Routes
Route::prefix('nurse')->group(function () {
    Route::get('/login', [NurseAuthController::class, 'showLoginForm'])->name('nurse.login');
    Route::post('/login', [NurseAuthController::class, 'login'])->name('nurse.login.submit');
    Route::get('/patients', [PatientController::class, 'index'])->name('patients.index');
    Route::get('/appointments', [AppointmentController::class, 'index'])->name('nurse.appointments');
    Route::get('/reports', [ReportController::class, 'index'])->name('nurse.reports');
    Route::patch('/nurse/reports/{report}/toggle', [ReportController::class, 'toggleReviewed'])->name('reports.toggle');
    Route::middleware('auth:nurse')->group(function () {
        Route::get('/dashboard', [NurseDashboardController::class, 'index'])->name('nurse.dashboard');
        Route::post('/logout', function () {Auth::guard('nurse')->logout();return redirect('/');})->name('nurse.logout');
    });
});

// Staff Routes
Route::prefix('staff')->group(function () {

    Route::get('/login', [StaffAuthController::class, 'showLogin'])->name('staff.login');
    Route::post('/login', [StaffAuthController::class, 'login'])->name('staff.login.submit');
    Route::middleware('auth:staff')->group(function () {
        Route::get('/leave-requests', [StaffLeaveController::class, 'index'])->name('staff.leave.index');
        Route::post('/leave-requests/{id}/approve', [StaffLeaveController::class, 'approve'])->name('staff.leave.approve');
        Route::post('/leave-requests/{id}/reject', [StaffLeaveController::class, 'reject'])->name('staff.leave.reject');
        Route::get('/medical-positions', [StaffMedicalPositionController::class,'index'])->name('staff.medical.positions');
        Route::post('/medical-positions/{id}/approve',[StaffMedicalPositionController::class,'approve'])->name('staff.medical.approve');
        Route::post('/medical-positions/{id}/reject',[StaffMedicalPositionController::class,'reject'])->name('staff.medical.reject');
        Route::get('/administrative-positions',[StaffMedicalPositionController::class,'administrative'])->name('staff.administrative.positions');
        Route::get('/training-programs', [StaffTrainingProgramController::class,'index'])->name('staff.training.programs');
        Route::post('/training-programs/{id}/approve', [StaffTrainingProgramController::class,'approve'])->name('staff.training.approve');
        Route::post('/training-programs/{id}/reject', [StaffTrainingProgramController::class,'reject'])->name('staff.training.reject');
        Route::get('/complaints',[StaffComplaintController::class,'index'])->name('staff.complaints');
        Route::post('/complaints/{id}/resolve', [StaffComplaintController::class,'resolve'])->name('staff.complaint.resolve');
        Route::post('/complaints/{id}/escalate', [StaffComplaintController::class,'escalate'])->name('staff.complaint.escalate');
        Route::post('/logout', function () {Auth::guard('staff')->logout();return redirect('/');})->name('staff.logout');
        Route::get('/leave/create',[StaffLeaveController::class,'create'])->name('staff.leave.create');
        Route::post('/leave/store',[StaffLeaveController::class,'store'])->name('staff.leave.store');
    });
});

// ## Auth Routes
Route::middleware('auth')->group(function () {
    Route::get('/book', [AppointmentController::class, 'start'])->name('book.start');
 Route::get('/appointment/{doctor}/{time}', [AppointmentController::class, 'create'])->name('appointments.create');
 Route::post('/appointments/store', [AppointmentController::class, 'store'])->name('appointments.store');
 Route::get('/appointments/{appointment}/success', [AppointmentController::class, 'success'])->name('appointments.success');

 Route::get('/results', [ResultsController::class, 'index'])->name('results.index');
 Route::post('/results/search', [ResultsController::class, 'search'])->name('results.search');
 Route::get('/results/download/{type}/{id}', [ResultsController::class, 'download'])->name('results.download');
 Route::get('/complaints', [ComplaintController::class, 'create'])->name('complaints.create');
 Route::post('/complaints', [ComplaintController::class, 'store'])->name('complaints.store');

 Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
});

// ## Admin Routes
Route::get('/admin/login', [AdminAuthController::class,'showLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class,'login'])->name('admin.login.submit');
Route::middleware('auth:admin')->group(function(){
    Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/admin/appointments', [AdminAppointmentController::class,'index'])->name('admin.appointments');
    Route::post('/admin/appointments/store',[AdminAppointmentController::class,'store'])->name('admin.appointments.store');

    Route::get('/admin/appointments/edit/{id}', [AdminAppointmentController::class, 'edit'])->name('admin.appointments.edit');
    Route::put('/admin/appointments/update/{id}', [AdminAppointmentController::class, 'update'])->name('admin.appointments.update');
    Route::delete('/admin/appointments/delete/{id}', [AdminAppointmentController::class, 'delete'])->name('admin.appointments.delete');

    Route::get('/admin/patients', [AdminPatientController::class, 'index'])->name('admin.patients');
    Route::delete('/admin/patients/{id}', [AdminPatientController::class, 'destroy'])->name('admin.patients.delete');
    Route::get('/admin/patients/{id}/edit', [AdminPatientController::class, 'edit'])->name('admin.patients.edit');
    Route::post('/admin/patients/{id}/update', [AdminPatientController::class, 'update'])->name('admin.patients.update');
    Route::get('/admin/patients/create', [AdminPatientController::class, 'create'])->name('admin.patients.create');
    Route::post('/admin/patients/store', [AdminPatientController::class, 'store'])->name('admin.patients.store');
    Route::get('/admin/doctors', [AdminDoctorController::class, 'index'])->name('admin.doctors');
    Route::get('/admin/doctors/create', [AdminDoctorController::class, 'create'])->name('admin.doctors.create');
    Route::post('/admin/doctors/store', [AdminDoctorController::class, 'store'])->name('admin.doctors.store');
    Route::get('/admin/doctors/edit/{id}', [AdminDoctorController::class, 'edit'])->name('admin.doctors.edit');
    Route::post('/admin/doctors/update/{id}', [AdminDoctorController::class, 'update'])->name('admin.doctors.update');
    Route::delete('/admin/doctors/delete/{id}', [AdminDoctorController::class, 'destroy'])->name('admin.doctors.delete');
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

});



Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/logout', [NurseAuthController::class, 'logout'])->name('nurse.logout');
Route::get('/medical-positions',[JobController::class,'medical'])->name('jobs.medical');
Route::get('/jobs/{id}/apply', [JobApplicationController::class, 'create'])->name('jobs.apply');
Route::post('/jobs/apply', [JobApplicationController::class, 'store'])->name('jobs.apply.store');
Route::get('/departments', [DepartmentController::class, 'index'])->name('departments.index');
Route::get('/departments/{department}', [DepartmentController::class, 'show'])->name('departments.show');
Route::get('/doctors/{doctor}', [DoctorController::class, 'show'])->name('doctors.show');













//Route::get('/departments', [DepartmentController::class,'index'])->name('departments');
//Route::get('/departments/{department}', [DepartmentController::class, 'show'])->name('departments.show');
//Route::get('/departments', [DepartmentController::class, 'index'])->middleware('auth')->name('departments.index');

//Route::get('/appointment/{doctor}/{time}', [AppointmentController::class, 'create'])->name('appointments.create');
//
//Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
//
//Route::post('/appointments', [AppointmentController::class, 'store'])->name('appointments.store');
