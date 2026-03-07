<!DOCTYPE html>
<html>
<head>
    <title>Leave Request</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <style>

        body{
            margin:0;
            font-family:Arial;
            background:linear-gradient(135deg,#5dade2,#1e69de);
            padding:40px;
        }

        .container{
            max-width:1100px;
            margin:auto;
            background:#f3f4f6;
            border-radius:25px;
            padding:40px;
            box-shadow:0 20px 40px rgba(0,0,0,0.25);
        }

        h2{margin-top:0;}

        .grid{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:40px;
        }

        .card{
            background:white;
            padding:25px;
            border-radius:15px;
            box-shadow:0 10px 25px rgba(0,0,0,0.1);
            margin-bottom:20px;
        }

        label{
            display:block;
            margin-top:15px;
            font-weight:bold;
        }

        input,select,textarea{
            width:100%;
            padding:10px;
            margin-top:5px;
            border-radius:10px;
            border:1px solid #ccc;
        }

        textarea{height:100px;}

        .btn{
            margin-top:20px;
            background:#123a6f;
            color:white;
            padding:12px 25px;
            border-radius:12px;
            border:none;
            cursor:pointer;
        }

        .status{
            padding:5px 10px;
            border-radius:20px;
            font-size:12px;
            font-weight:bold;
        }

        .approved{background:#d4edda;color:#155724;}
        .pending{background:#fff3cd;color:#856404;}
        .rejected{background:#f8d7da;color:#721c24;}

        .error{color:red;margin-top:10px;}
        .success{color:green;margin-bottom:15px;}

        @media(max-width:900px){
            .grid{grid-template-columns:1fr;}
        }

    </style>
</head>

<body>

@php
    use Carbon\Carbon;
@endphp

<div class="container">

    @if(session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <h2>Leave Request Form</h2>
    <p>Submit a new leave request or view current and past leave requests.</p>

    <div class="grid">

        <!-- LEFT SIDE -->
        <div>

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


            @if ($errors->any())

                <div class="error">

                    @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                    @endforeach

                </div>

            @endif


            <form method="POST" action="{{ route('staff.leave.store') }}">

                @csrf

                <label>Request For</label>

                <select id="typeSelect" name="type">

                    <option value="">Select</option>
                    <option value="doctor">Doctor</option>
                    <option value="nurse">Nurse</option>

                </select>


                <label>Select Name</label>

                <select id="staffSelect" name="staff_id">

                    <option value="">Select staff</option>

                    @foreach($doctors as $doctor)

                        <option value="{{ $doctor->id }}" data-type="doctor" style="display:none;">
                            {{ $doctor->name }}
                        </option>

                    @endforeach


                    @foreach($nurses as $nurse)

                        <option value="{{ $nurse->id }}" data-type="nurse" style="display:none;">
                            {{ $nurse->name }}
                        </option>

                    @endforeach

                </select>


                <label>Start Date</label>

                <input type="date" name="start_date" value="{{ old('start_date') }}">


                <label>End Date</label>

                <input type="date" name="end_date" value="{{ old('end_date') }}">


                <label>Reason for Leave</label>

                <textarea name="reason">{{ old('reason') }}</textarea>


                <button class="btn">Submit Request</button>

            </form>

        </div>


        <!-- RIGHT SIDE -->

        <div>

            <h3>Leave Request History</h3>

            @foreach($leaveRequests as $leave)

                <div class="card">

                    <p>

                        {{ Carbon::parse($leave->start_date)->format('M d, Y') }}

                        -

                        {{ Carbon::parse($leave->end_date)->format('M d, Y') }}

                    </p>

                    <p>
                        Duration:
                        {{ Carbon::parse($leave->start_date)
                        ->diffInDays(Carbon::parse($leave->end_date)) + 1 }}
                        days
                    </p>

                    <p>
                        Reason: {{ $leave->reason }}
                    </p>

                    <span class="status {{ $leave->status }}">
{{ ucfirst($leave->status) }}
</span>

                </div>

            @endforeach

        </div>

    </div>

</div>


<script>

    const typeSelect = document.getElementById('typeSelect');
    const staffSelect = document.getElementById('staffSelect');
    const options = staffSelect.querySelectorAll('option[data-type]');

    typeSelect.addEventListener('change', function(){

        const type = this.value;

        options.forEach(option => {

            option.style.display = option.dataset.type === type ? 'block' : 'none';

        });

        staffSelect.value='';

    });

</script>

</body>
</html>
