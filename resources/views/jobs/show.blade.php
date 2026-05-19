<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ $job->title }} - Job Details</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { font-family: Arial, Helvetica, sans-serif; background: linear-gradient(90deg,#8ecbff,#eaf5ff 40%,#f4f4f6); }
        .card-wrap { background:#fff; border-radius:20px; padding:32px; box-shadow:0 12px 30px rgba(0,0,0,0.1); }
        .btn-main { background:#0b3a78; color:#fff; border:none; }
        .btn-main:hover { background:#082c5c; color:#fff; }
    </style>
</head>
<body>
<div class="container py-5">
    <a href="{{ route('staff.module.jobs') }}" class="btn btn-outline-secondary mb-4">Back To Job Opportunities</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card-wrap">
        <h1 class="h3 mb-3">{{ $job->title }}</h1>
        <p class="mb-2"><strong>Job Type:</strong> {{ in_array($job->type, ['administrative', 'admin'], true) ? 'Administrative' : 'Medical' }}</p>
        <p class="mb-2"><strong>Department:</strong> {{ $job->department ?: '-' }}</p>
        <p class="mb-2"><strong>Status:</strong> {{ ucfirst($job->status ?? 'active') }}</p>
        <p class="mb-3">{{ $job->description }}</p>
        <h2 class="h5">Requirements</h2>
        <p class="mb-3">{{ $job->requirements }}</p>
        <p class="mb-4"><strong>Location:</strong> {{ $job->location }} | <strong>Salary:</strong> {{ $job->salary ?? '-' }}</p>

        <a href="{{ route('jobs.apply', $job) }}" class="btn btn-main">Apply Now</a>
    </div>
</div>
</body>
</html>
