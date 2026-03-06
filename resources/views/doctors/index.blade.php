<!DOCTYPE html>
<html>
<head>
    <title>{{ $department->name_en }} Doctors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body style="background: linear-gradient(90deg,#94c1e9,#ffffff,#94c1e9);">

<div class="container py-5">
    <h2 class="text-center mb-5">
        {{ $department->name_en }} Doctors
    </h2>

    <div class="row g-4">

        @foreach($doctors as $doctor)
            <div class="col-md-4">
                <div class="card shadow-lg rounded-4 p-3">

                    <div class="text-center">
                        <img src="{{ asset('images/' . $doctor->image) }}"
                             class="rounded-circle mb-3"
                             width="100" height="100"
                             style="object-fit:cover;">
                    </div>

                    <h5>{{ $doctor->name }}</h5>
                    <p>{{ $doctor->specialization }}</p>
                    <p>⭐ {{ $doctor->rating }}</p>
                    <p>{{ $doctor->experience }} years experience</p>

                    <a href="{{ route('doctors.show', $doctor->id) }}"
                       class="btn btn-primary w-100">
                        View Profile
                    </a>

                </div>
            </div>
        @endforeach

    </div>
</div>

</body>
</html>
