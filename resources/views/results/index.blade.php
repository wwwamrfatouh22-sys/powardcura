<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ __('ui.nav.radiology') }}</title>
    <link href="{{ app()->isLocale('ar') ? 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css' : 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root { --main-blue: #002147; }
        body { margin: 0; padding: 0; background: #f4f7fb; font-family: {{ app()->isLocale('ar') ? "'Cairo', Arial, sans-serif" : "'Inter', Arial, sans-serif" }}; text-align: {{ app()->isLocale('ar') ? 'right' : 'left' }}; }

        .navbar { background-color: var(--main-blue); padding: 10px 20px; }
        .navbar-brand { display: flex; align-items: center; color: white !important; font-weight: bold; font-size: 24px; }
        .navbar-brand img { height: 40px; margin-right: 10px; }
        .nav-link { color: rgba(255,255,255,0.9) !important; margin: 0 10px; font-size: 15px; }

        .card-soft{
            border: 0;
            border-radius: 18px;
            box-shadow: 0 12px 30px rgba(0,0,0,.12);
            background: white;
        }

        .page-wrap{
            min-height: calc(100vh - 70px);
            display:flex;
            align-items:flex-start;
            justify-content:center;
            padding: 60px 15px;
            background: radial-gradient(circle at left, #8abbe7 0%, #ffffff 55%);
        }

        /* ===== Tiles (Test Result Images) ===== */
        .result-tile{
            background:#dfe9f6;
            border-radius:14px;
            padding:18px;
            height:100%;
        }
        .result-tile .tile-title{
            font-weight:800;
            margin:0 0 6px 0;
            display:flex;
            align-items:center;
            gap:10px;
            color:#0f1f33;
        }
        .result-tile .tile-sub{
            margin:0 0 14px 0;
            color:#1f2d3d;
            min-height: 24px;
        }
        .btn-download{
            border:2px solid #2c5f96;
            color:#2c5f96;
            font-weight:700;
            border-radius:10px;
            background:transparent;
            padding:10px 14px;
            display:flex;
            align-items:center;
            justify-content:center;
            gap:10px;
            width:100%;
            text-decoration:none;
        }
        .btn-download:hover{
            background:rgba(44,95,150,.08);
            color:#2c5f96;
        }
        .dl-icon{ width:18px; height:18px; display:inline-block; }
        .img-ic{ width:22px; height:22px; display:inline-block; }
    </style>
</head>
<body>

{{-- نفس Navbar بتاع الهوم --}}
<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('images/logoN.png') }}" alt="Logo">
            NUH
        </a>

        <div class="collapse navbar-collapse justify-content-center">
            <ul class="navbar-nav">
                <li class="nav-item"><a class="nav-link" href="#">About NUH</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Medical Departments</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Outpatient Clinics</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Jobs and Training</a></li>
                <li class="nav-item"><a class="nav-link" href="#">Contact us</a></li>

            </ul>
        </div>

        <div class="d-flex align-items-center">
            <a href="#" class="nav-link small">العربية</a>

            @auth('patient')
                <form action="{{ route('logout') }}" method="POST" class="ms-3">
                    @csrf
                    <button type="submit" style="background:none;border:none;color:white;font-size:15px;">
                        Logout
                    </button>
                </form>
            @endauth
        </div>
    </div>
</nav>

<div class="page-wrap">
    <div class="container" style="max-width: 1100px;">

        {{-- كارت البحث --}}
        <div class="card-soft p-5 mb-4">
            <h2 class="fw-bold mb-2">Search Radiology & Lab Tests Results</h2>
            <p class="text-muted mb-4">Please enter your National ID number to view your radiology & lab tests results.</p>

            <form method="POST" action="{{ route('results.search') }}" class="d-flex gap-3">
                @csrf

                <input
                    type="text"
                    name="national_id"
                    value="{{ old('national_id') }}"
                    class="form-control form-control-lg @error('national_id') is-invalid @enderror"
                    placeholder="Enter National ID Number"
                    maxlength="14"
                >

                <button class="btn btn-primary btn-lg px-5" type="submit">Search</button>
            </form>

            @error('national_id')
            <div class="text-danger mt-2">{{ $message }}</div>
            @enderror
        </div>

        {{-- بعد البحث: عرض النتائج --}}
        @if(isset($patient) && $patient)
            @php($downloadRoute = auth('admin')->check() ? 'admin.results.download' : 'results.download')

            {{-- Patient Information --}}
            <div class="card-soft p-5 mb-4">
                <h3 class="fw-bold mb-4">Patient Information</h3>

                <div class="row g-4">

                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <div class="fw-bold">Patient Name</div>
                            <div>{{ $patient->full_name }}</div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <div class="fw-bold">National ID</div>
                            <div>{{ $patient->national_id }}</div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <div class="fw-bold">Test Date</div>
                            <div>
                                {{ $patient->test_date ? \Carbon\Carbon::parse($patient->test_date)->format('d F Y') : '-' }}
                            </div>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="p-3 bg-light rounded">
                            <div class="fw-bold">Department</div>
                            <div>{{ $patient->department }}</div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- Laboratory + Radiology Lists --}}
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="card-soft p-4 h-100">
                        <h4 class="fw-bold mb-3">Laboratory Results</h4>

                        @forelse($labTests as $t)
                            <div class="p-3 bg-light rounded mb-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">{{ $t->title }}</div>
                                    <div class="text-muted small">{{ $t->description }}</div>
                                </div>

                                <a class="btn btn-outline-primary"
                                   href="{{ route($downloadRoute, ['type' => 'lab', 'id' => $t->id]) }}">
                                    Download
                                </a>
                            </div>
                        @empty
                            <div class="text-muted">No lab tests found.</div>
                        @endforelse
                    </div>
                </div>

                <div class="col-lg-6">
                    <div class="card-soft p-4 h-100">
                        <h4 class="fw-bold mb-3">Radiology Results</h4>

                        @forelse($radiologyResults as $r)
                            <div class="p-3 bg-light rounded mb-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">{{ $r->title }}</div>
                                    <div class="text-muted small">{{ $r->description }}</div>
                                </div>

                                <a class="btn btn-outline-primary"
                                   href="{{ route($downloadRoute, ['type' => 'radio', 'id' => $r->id]) }}">
                                    Download
                                </a>
                            </div>
                        @empty
                            <div class="text-muted">No radiology results found.</div>
                        @endforelse
                    </div>
                </div>
            </div>

                </div>
            </div>
        @endif
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
