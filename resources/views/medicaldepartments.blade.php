<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hospital Departments</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(90deg, #94c1e9 0%, #ffffff 50%, #94c1e9 100%);
            font-family: sans-serif;
        }

        .section-title {
            text-align: center;
            font-size: 40px;
            font-weight: 600;
            margin: 60px 0;
        }

        .department-card {
            background: #fff;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            transition: 0.3s;
            height: 100%;
        }

        .department-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .department-card img {
            width: 100%;
            height: 220px;
            object-fit: cover;
        }

        .department-body {
            padding: 25px;
        }

        .department-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .department-ar {
            font-size: 14px;
            color: #777;
            margin-bottom: 20px;
        }

        .learn-more {
            color: #0d4c92;
            font-weight: 500;
            text-decoration: none;
        }

        .learn-more:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="section-title">Hospital Departments</h2>

    <div class="row g-4">  {{-- دي كانت ناقصة --}}

        @foreach($departments as $department)
            <div class="col-md-4">
                <div class="department-card">
                    <img src="{{ asset('images/' . $department->image) }}" alt="">
                    <div class="department-body">
                        <div class="department-title">
                            {{ $department->name_en }}
                        </div>
                        <div class="department-ar">
                            {{ $department->name_ar }}
                        </div>
                        <a href="{{ route('departments.show', $department->id) }}"
                           class="learn-more">
                            See More →
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>
</body>
</html>
