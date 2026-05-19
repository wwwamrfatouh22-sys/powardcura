<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>Job Application</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background: linear-gradient(90deg,#005baa,#5fa8dd); }
        .apply-card { background:#f8f9fa; border-radius:20px; padding:40px; box-shadow:0 10px 30px rgba(0,0,0,0.15); }
        .btn-main { background:#0b3a78; color:#fff; border:none; }
        .btn-main:hover { background:#082c5c; color:#fff; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="col-lg-8 mx-auto">
        <div class="apply-card">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h1 class="h4 mb-0">Job Application</h1>
                <a href="{{ route('jobs.show', $job) }}" class="btn btn-outline-secondary btn-sm">Back To Job Details</a>
            </div>
            <p class="text-muted">You are applying for: <strong>{{ $job->title }}</strong></p>
            <p class="text-muted">Job Type: <strong>{{ in_array($job->type, ['administrative', 'admin'], true) ? 'Administrative' : 'Medical' }}</strong></p>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form action="{{ route('jobs.apply.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="job_id" value="{{ $job->id }}">

                <div class="mb-3">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
                </div>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone *</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                    </div>
                </div>

                <div class="mt-3">
                    <label class="form-label">National ID *</label>
                    <input type="text" name="national_id" class="form-control" value="{{ old('national_id') }}" required>
                </div>

                <div class="mt-3 mb-4">
                    <label class="form-label">Upload CV (PDF/DOC/DOCX) *</label>
                    <input type="file" name="cv" class="form-control" required>
                </div>

                <button type="submit" class="btn btn-main">Apply Now</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>
