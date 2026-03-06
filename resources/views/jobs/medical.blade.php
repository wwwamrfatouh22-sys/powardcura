<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Medical Positions</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        .page-header {
            background: #f3f4f6;
            padding: 15px 0;
            text-align: center;
        }

        .page-header h2 {
            font-weight: 700;
            margin-bottom: 10px;
        }

        .page-header p {
            color: #6c757d;
        }

        .jobs-section {
            background: linear-gradient(90deg,#005baa,#5fa8dd);
            padding: 30px 0;
        }

        .job-card {
            background: #f8f9fa;
            border-radius: 18px;
            padding: 35px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.15);
            margin-bottom: 40px;
        }

        .job-card h4 {
            font-weight: 700;
            margin-bottom: 15px;
        }

        .job-card p {
            color: #333;
        }

        .apply-btn {
            background-color: #0b3a78;
            color: white;
            padding: 12px 45px;
            border-radius: 8px;
            font-weight: 600;
            border: none;
        }

        .apply-btn:hover {
            background-color: #082c5c;
        }

        @media (max-width: 768px) {
            .job-card {
                text-align: center;
            }
        }
        .page-header {
            padding: 25px 0;
        }
        .page-header img {
            margin: 0;
        }
    </style>
</head>
<body>

<!-- Page Header -->
<div class="page-header">
    <div class="page-header d-flex align-items-center justify-content-between px-4">

        <!-- Logo -->
        <div>
            <img src="{{ asset('images/logo_Image.png') }}"
                 alt="NUH Logo"
                 style="height:60px;">
        </div>

        <!-- Title -->
        <div class="text-center w-100">
            <h2>Medical Positions</h2>
            <p>Discover your next career opportunity in NUH</p>
        </div>

    </div>
</div>

<!-- Jobs Section -->
<div class="jobs-section">
    <div class="container">

        @forelse($jobs as $job)
            <div class="job-card d-md-flex justify-content-between align-items-center">
                <div>
                    <h4>{{ $job->title }}</h4>

                    <p>{{ $job->description }}</p>

                    <strong>Requirements:</strong>
                    <p>{{ $job->requirements }}</p>

                    <p><i class="bi bi-geo-alt"></i> {{ $job->location }}</p>
                    <p><i class="bi bi-currency-dollar"></i> {{ $job->salary ?? '-' }}</p>
                </div>

                <a href="{{ route('jobs.apply', $job->id) }}" class="apply-btn">
                    Apply Now
                </a>
            </div>
        @empty
            <div class="job-card text-center">
                <h4>No medical positions available right now.</h4>
            </div>
        @endforelse

    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
