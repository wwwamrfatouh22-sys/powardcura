<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Patient Profile</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script>
        function printPage() {
            window.print();
        }
    </script>
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(to right, #3a7bd5, #00d2ff);
            min-height: 100vh;
            padding-top: 60px;
        }

        .profile-card {
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .avatar-box {
            width: 100px;
            height: 100px;
            background: #f1f1f1;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 40px;
        }

        .section-title {
            font-weight: bold;
            margin-bottom: 20px;
        }

        .print-btn {
            background-color: #1f4e8c;
            border: none;
            width: 100%;
            max-width: 500px;
            padding: 14px;
            font-size: 18px;
            font-weight: 600;
            border-radius: 12px;
            transition: 0.3s;
        }

        .print-btn:hover {
            background-color: #163d6b;
        }
        @media print {
            .print-btn {
                display: none !important;
            }

            body {
                background: white !important;
            }

            .profile-card {
                box-shadow: none !important;
            }
        }
    </style>
</head>
<body>

<div class="container">

    <h3 class="text-white mb-4">Basic Information</h3>

    <div class="card profile-card p-4">

        <div class="row align-items-center">

            <!-- Avatar -->
            <div class="col-md-2 text-center">
                <div class="avatar-box">
                    👤
                </div>
            </div>

            <!-- Patient Info -->
            <div class="col-md-10">
                <h4>{{ $patient->full_name }}</h4>
                <p>File number: {{ $patient->file_number }}</p>

                <p>
                    Blood type: {{ $patient->blood_type }} |
                    {{ $patient->gender }} |
                    {{ $patient->age }} year
                </p>

                <p>Phone: {{ $patient->phone }}</p>
                <p>Address: {{ $patient->address }}</p>
            </div>
        </div>
    </div>
    <!-- ================= Vital Signs ================= -->
    <h4 class="text-white mt-5 mb-3">Vital Signs</h4>

    <div class="card profile-card p-4 mb-4">

        <div class="row mb-3">
            <div class="col-md-6">
                <strong>Blood Pressure:</strong>
                {{ $patient->blood_pressure ?? '-' }} mmHg
            </div>

            <div class="col-md-6">
                <strong>Pulse Rate:</strong>
                {{ $patient->pulse_rate ?? '-' }} beats/min
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <strong>Temperature:</strong>
                {{ $patient->temperature ?? '-' }} °C
            </div>

            <div class="col-md-6">
                <strong>Weight:</strong>
                {{ $patient->weight ?? '-' }} kg
            </div>
        </div>

    </div>


    <!-- ================= Medications ================= -->
    <h4 class="text-white mt-4 mb-3">Medications</h4>

    <div class="card profile-card p-4 mb-5">

        @forelse($patient->medications as $medication)

            <div class="alert alert-light border border-primary mb-3">
                <strong>
                    {{ $medication->name }}
                    {{ $medication->dose }}
                </strong>
                <br>
                {{ $medication->instructions }}
            </div>

        @empty
            <p class="text-muted mb-0">No medications found.</p>
        @endforelse
    </div>
    <div class="text-center mt-4">
        <button onclick="printPage()" class="btn btn-primary print-btn">
            🖨 Print
        </button>
    </div>
</div>

</body>
</html>
