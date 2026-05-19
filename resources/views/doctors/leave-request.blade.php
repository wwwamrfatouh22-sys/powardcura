<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <title>Leave Request</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: linear-gradient(135deg, #5dade2, #1e69de);
            padding: 40px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            background: #f3f4f6;
            border-radius: 25px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.25);
        }

        h2 { margin-top: 0; }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }

        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border-radius: 10px;
            border: 1px solid #ccc;
        }

        textarea { height: 100px; }

        .btn {
            margin-top: 20px;
            background: #123a6f;
            color: white;
            padding: 12px 25px;
            border-radius: 12px;
            border: none;
            cursor: pointer;
        }

        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .approved { background: #d4edda; color: #155724; }
        .pending { background: #fff3cd; color: #856404; }
        .rejected { background: #f8d7da; color: #721c24; }

        .error { color: red; margin-top: 10px; }
        .success { color: green; margin-bottom: 15px; }

        @media (max-width: 900px) {
            .grid { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

@php
    use Carbon\Carbon;
@endphp

<div class="container">
    {{-- Success Message --}}
    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif
    <h2>Leave Request Form</h2>
    <p>Submit a new leave request or view your current and past leave requests.</p>

    <div class="grid">

        <!-- LEFT SIDE -->
        <div>

            {{-- Current Leave --}}
            @php
                $currentLeave = $leaveRequests->where('status','pending')->first();
            @endphp

            @if($currentLeave)
                <div class="card">
                    <h4>Current Leave Request Status</h4>
                    <p>
                        {{ Carbon::parse($currentLeave->start_date)->format('M d, Y') }}
                        -
                        {{ Carbon::parse($currentLeave->end_date)->format('M d, Y') }}
                    </p>

                    <p>
                        Duration:
                        {{ Carbon::parse($currentLeave->start_date)
                            ->diffInDays(Carbon::parse($currentLeave->end_date)) + 1 }}
                        days
                    </p>

                    <span class="status {{ $currentLeave->status }}">
                        {{ ucfirst($currentLeave->status) }}
                    </span>
                </div>
            @endif




            {{-- Errors --}}
            @if ($errors->any())
                <div class="error">
                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach
                </div>
            @endif


            <!-- FORM -->
            <form method="POST" action="{{ route('doctor.leave.store') }}">
                @csrf

                <label>Doctor Name</label>
                <input type="text" value="{{ $doctor->name }}" disabled>

                <label>Department</label>
                <input type="text" value="{{ $doctor->department->name ?? 'N/A' }}" disabled>

                <label>Start Date</label>
                <input type="date" name="start_date" value="{{ old('start_date') }}">

                <label>End Date</label>
                <input type="date" name="end_date" value="{{ old('end_date') }}">

                <label>Reason for Leave</label>
                <textarea name="reason">{{ old('reason') }}</textarea>

                <button class="btn">Submit Request</button>
            </form>

        </div>

    </div>
</div>

</body>
</html>
