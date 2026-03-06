<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointment Confirmed</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(90deg, #94c1e9 0%, #ffffff 50%, #94c1e9 100%);
            font-family: sans-serif;
        }

        .card-box {
            background: #fff;
            border-radius: 20px;
            padding: 50px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            margin-top: 60px;
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: #d4f4dd;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px auto;
            font-size: 40px;
            color: #28a745;
        }

        .doctor-img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            object-fit: cover;
        }

        .divider {
            border-top: 1px solid #ddd;
            margin: 30px 0;
        }

        .payment-box {
            text-align: center;
        }

        .payment-box input {
            transform: scale(1.3);
            margin-left: 10px;
        }

        .payment-details {
            font-size: 14px;
            color: #6c757d;
            margin-top: 10px;
        }

        .btn-card {
            border: 2px solid #0d4c92;
            color: #0d4c92;
            border-radius: 10px;
            padding: 10px 25px;
            font-weight: 600;
        }

        .btn-card:hover {
            background: #0d4c92;
            color: #fff;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card-box">

        <!-- Success Icon -->
        <div class="text-center">
            <div class="success-icon">
                ✓
            </div>

            <h2 class="fw-bold">Appointment Confirmed!</h2>
            <p class="text-muted">
                Your appointment has been successfully scheduled.
            </p>
        </div>

        <div class="divider"></div>

        <!-- Appointment Details -->
        <h5 class="mb-4">Appointment Details</h5>

        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <img src="{{ asset('images/' . $appointment->doctor->image) }}" class="doctor-img">
                <div>
                    <strong>{{ $appointment->doctor->name }}</strong><br>
                    {{ $appointment->doctor->specialization }}
                </div>
            </div>

            <div>
                📅 {{ \Carbon\Carbon::parse($appointment->date)->format('l, F j, Y') }}<br>
                ⏰ {{ $appointment->time }}
            </div>
        </div>

        <div class="divider"></div>

        <!-- Patient Info -->
        <h5>Patient Information</h5>

        <p><strong>Name:</strong> {{ $appointment->first_name }} {{ $appointment->last_name }}</p>
        <p><strong>Email:</strong> {{ $appointment->email }}</p>
        <p><strong>Phone:</strong> {{ $appointment->phone }}</p>

        <div class="divider"></div>

        <!-- Payment Section -->

        <h5>Payment Method</h5>

        <p>
            <strong>Method:</strong>
            {{ ucfirst(str_replace('_',' ', $appointment->payment_method)) }}
        </p>

        <div class="text-center mt-4">
            <button type="button"
                    onclick="window.print()"
                    style="
            background:#163c6b;
            color:white;
            padding:12px 50px;
            border:none;
            border-radius:12px;
            font-weight:600;
            font-size:18px;
        ">
                Print
            </button>
        </div>
    </div>

</div>

</body>
</html>
