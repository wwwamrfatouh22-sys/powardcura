<!DOCTYPE html>
<html>
<head>
    <title>{{ $doctor->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body style="background: linear-gradient(90deg,#94c1e9,#ffffff,#94c1e9);">

<div class="container py-5">

    <div class="card p-5 rounded-4 shadow-lg">

        <div class="row align-items-center">

            <div class="col-md-3 text-center">
                <img src="{{ asset('images/' . $doctor->image) }}"
                     class="rounded-circle"
                     width="150"
                     height="150"
                     style="object-fit:cover;">
            </div>

            <div class="col-md-9">
                <h2>{{ $doctor->name }}</h2>
                <p class="text-muted">{{ $doctor->specialization }}</p>

                <p>⭐ {{ $doctor->rating }}</p>
                <p>{{ $doctor->experience }} years experience</p>

                <p class="mt-3">
                    Lorem ipsum description from database later...
                </p>
            </div>

        </div>

        <hr class="my-5">

        <h5>Select Date</h5>

        <div class="d-flex gap-3 mb-4">
            <button class="btn btn-primary">Tue 29</button>
            <button class="btn btn-secondary">Wed 30</button>
        </div>

        <h5>Select Time</h5>

        <div class="d-flex gap-3">

            <a href="{{ route('appointments.create', [$doctor->id, '09:00']) }}"
               class="btn btn-primary">
                9:00
            </a>

            <a href="{{ route('appointments.create', [$doctor->id, '10:00']) }}"
               class="btn btn-primary">
                10:00
            </a>

            <a href="{{ route('appointments.create', [$doctor->id, '11:00']) }}"
               class="btn btn-primary">
                11:00
            </a>

        </div>

    </div>

</div>

</body>
</html>
