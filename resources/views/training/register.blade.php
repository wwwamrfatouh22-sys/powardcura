<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Training Registration</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-4 p-md-5">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h1 class="h4 mb-1">Training Program Registration</h1>
                            <p class="text-muted mb-0">Program: <strong>{{ $program->title }}</strong></p>
                        </div>
                        <a href="{{ route('training.index') }}" class="btn btn-outline-secondary btn-sm">Back</a>
                    </div>

                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('training.apply.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="training_id" value="{{ $program->id }}">

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Full Name</label>
                                <input name="full_name" class="form-control" required value="{{ old('full_name') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <input name="email" type="email" class="form-control" required value="{{ old('email') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Phone</label>
                                <input name="phone" class="form-control" required value="{{ old('phone') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">National ID</label>
                                <input name="national_id" class="form-control" required value="{{ old('national_id') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Age</label>
                                <input name="age" type="number" class="form-control" value="{{ old('age') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Gender</label>
                                <select name="gender" class="form-select">
                                    <option value="">Select</option>
                                    <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Department</label>
                                <select name="department_id" class="form-select">
                                    <option value="">Select Department</option>
                                    @foreach($departments as $department)
                                        <option value="{{ $department->id }}" {{ old('department_id') == $department->id ? 'selected' : '' }}>
                                            {{ $department->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-8">
                                <label class="form-label">University</label>
                                <input name="university" class="form-control" value="{{ old('university') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">GPA</label>
                                <input name="gpa" type="number" step="0.01" min="0" max="4" class="form-control" value="{{ old('gpa') }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">CV (PDF/DOC/DOCX)</label>
                                <input name="cv" type="file" class="form-control" required>
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-4">
                            <a href="{{ route('training.index') }}" class="btn btn-outline-secondary">Cancel</a>
                            <button class="btn btn-primary" type="submit">Register Now</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
