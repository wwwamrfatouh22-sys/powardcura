<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Apply</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
        }

        /* Header */
        .page-header {
            background: #f3f4f6;
            padding: 18px 0;
        }

        .page-header h2 {
            margin-bottom: 5px;
            font-weight: 700;
        }

        .page-header p {
            margin-bottom: 0;
            color: #6c757d;
        }

        /* Gradient Background */
        .apply-section {
            background: linear-gradient(90deg,#005baa,#5fa8dd);
            padding: 80px 0;
        }

        /* Card */
        .apply-card {
            background: #f8f9fa;
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .apply-btn {
            background-color: #0b3a78;
            color: white;
            padding: 10px 35px;
            border-radius: 8px;
            border: none;
        }

        .apply-btn:hover {
            background-color: #082c5c;
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="page-header d-flex align-items-center justify-content-between px-4">

    <div>
        <img src="{{ asset('images/logo_Image.png') }}" style="height:55px;">
    </div>

    <div class="text-center w-100">
        <h2>Medical Positions</h2>
        <p>Discover your next career opportunity in NUH</p>
    </div>

</div>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<!-- Gradient Section -->
<div class="apply-section">
    <div class="container">
        <div class="col-md-8 mx-auto">

            <div class="apply-card">

                <h4 class="mb-3">Submit Your CV</h4>
                <p class="text-muted mb-4">
                    Interested in joining our team? Fill out the form below and upload your CV.
                </p>

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form action="{{ route('jobs.apply.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <input type="hidden" name="job_id" value="{{ $job->id }}">

                    <div class="mb-3">
                        <label class="form-label">Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="Enter your full name">
                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Email *</label>
                        <input type="email" name="email" class="form-control" placeholder="your.email@example.com">
                        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Phone *</label>
                        <input type="text" name="phone" class="form-control" placeholder="(555) 123-4567">
                        @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label">National ID number *</label>
                        <input type="text" name="national_id" class="form-control">
                        @error('national_id') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <div class="mb-4">
                        <label class="form-label">Upload CV (PDF/DOCX) *</label>
                        <input type="file" name="cv" class="form-control">
                        @error('cv') <small class="text-danger">{{ $message }}</small> @enderror
                    </div>

                    <button type="submit" class="apply-btn">
                        Submit Application
                    </button>

                </form>

            </div>

        </div>
    </div>
</div>

</body>
</html>
