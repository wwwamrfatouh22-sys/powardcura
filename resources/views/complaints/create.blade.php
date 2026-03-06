<!DOCTYPE html>
<html>
<head>
    <title>Complaints</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root { --main-blue: #002147; }

        body {
            margin: 0;
            min-height: 100vh;
            background: radial-gradient(circle at left, #3c8fd8 0%, #ffffff 70%);
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .page-title {
            margin-top: 60px;
            font-weight: 700;
        }

        .form-card {
            background: white;
            width: 85%;
            max-width: 1100px;
            margin-top: 40px;
            padding: 40px;
            border-radius: 25px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.15);
        }

        .form-card h4 {
            font-weight: 700;
            margin-bottom: 30px;
        }

        .form-control {
            border-radius: 12px;
            border: 2px solid #3c6fa8;
            padding: 12px;
        }

        .form-control:focus {
            box-shadow: none;
            border-color: #1f4e8c;
        }

        textarea.form-control {
            height: 130px;
        }

        .btn-send {
            background: #113c7b;
            color: white;
            padding: 10px 35px;
            border-radius: 12px;
            border: none;
        }

        .btn-send:hover {
            background: #0b2f61;
        }

        .logo {
            position: absolute;
            top: 25px;
            left: 40px;
        }

        .logo img {
            height: 50px;
        }
    </style>
</head>
<body>

<div class="logo">
    <img src="{{ asset('images/log.png') }}">
</div>

<h3 class="page-title">Complaints and suggestions</h3>
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="form-card">
    <h4>Complaints</h4>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <form method="POST" action="{{ route('complaints.store') }}">
        @csrf

        <div class="row mb-4">
            <div class="col-md-4">
                <label>Name</label>
                <input type="text" name="name" class="form-control">
            </div>

            <div class="col-md-4">
                <label>Email</label>
                <input type="email" name="email" class="form-control">
            </div>

            <div class="col-md-4">
                <label>Phone Number</label>
                <input type="text" name="phone" class="form-control">
            </div>
        </div>

        <div class="row mb-4">
            <div class="col-md-6">
                <label>Type of complaint</label>
                <input type="text" name="type" class="form-control">
            </div>

            <div class="col-md-6">
                <label>The relevant department or entity</label>
                <input type="text" name="department" class="form-control">
            </div>
        </div>

        <div class="mb-4">
            <label>Details of the complaint</label>
            <textarea name="details" class="form-control"></textarea>
        </div>

        <button class="btn-send">send</button>
    </form>
</div>

</body>
</html>
