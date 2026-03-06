<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an account - NUH</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            /* الخلفية الزرقاء المتدرجة اللي في الصورة */
            background: linear-gradient(90deg, #94c1e9 0%, #ffffff 50%, #94c1e9 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            font-family: sans-serif;
            margin: 0;
        }

        .white-card {
            background-color: white;
            width: 100%;
            max-width: 850px; /* عرض الفورم البيضاء */
            min-height: 100vh;
            padding: 40px 80px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
        }

        .logo-box {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo-box img {
            height: 70px; /* حجم اللوجو */
        }

        h4 {
            text-align: center;
            font-weight: 500;
            margin-bottom: 50px;
            color: #333;
        }

        .form-label {
            font-weight: 600;
            color: #444;
            margin-bottom: 12px;
            font-size: 15px;
        }

        .form-control {
            border: 1.5px solid #a5c1d6; /* لون الحد الأزرق الفاتح */
            border-radius: 12px; /* الحواف الدائرية زي الصورة */
            padding: 12px 15px;
            margin-bottom: 5px;
            color: #666;
        }

        .form-control::placeholder {
            color: #adb5bd;
        }

        .hint {
            font-size: 11px;
            color: #888;
            margin-bottom: 25px;
            padding-left: 5px;
        }

        .btn-signup {
            background-color: #004494; /* اللون الأزرق اللي طلبته */
            color: white;
            border-radius: 12px;
            padding: 14px;
            width: 100%;
            margin-top: 20px;
            font-size: 18px;
            font-weight: bold;
            border: none;
            transition: 0.3s;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .btn-signup:hover {
            background-color: #003366 !important; /* هيقلب أزرق أغمق مش أبيض */
            color: white !important;             /* يفضل الخط أبيض */
            transform: translateY(-2px);        /* حركة بسيطة لفوق بتدي شكل احترافي */
            box-shadow: 0 6px 12px rgba(0,0,0,0.2); /* الظل يوضح أكتر */
        }
    </style>
</head>
<body>

<div class="white-card">
    <div class="logo-box">
        <img src="{{ asset('images/image.png') }}" alt="NUH Logo">
    </div>

    <h4>Create an account</h4>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('register.store') }}" method="POST">
        @csrf
        <label class="form-label">National ID number</label>
        <input type="text"
               name="national_id"
               value="{{ old('national_id') }}"
               class="form-control"
               placeholder="National ID number">
        <div class="hint">14 numbers</div>


        <label class="form-label">Four-part name</label>
        <input type="text"
               name="full_name"
               value="{{ old('full_name') }}"
               class="form-control"
               placeholder="four-part name">
        <div style="margin-bottom: 35px;"></div>


        <label class="form-label">Date of birth</label>
        <input type="date"
               name="dob"
               value="{{ old('dob') }}"
               class="form-control">
        <div style="margin-bottom: 35px;"></div>


        <label class="form-label">Phone number</label>
        <input type="text"
               name="phone"
               value="{{ old('phone') }}"
               class="form-control"
               placeholder="phone number">
        <div class="hint">11 numbers</div>


        <label class="form-label">Password</label>
        <input type="password"
               name="password"
               class="form-control"
               placeholder="password">

        <button type="submit" class="btn-signup">Sign Up</button>
        <div class="login-link-section">
            Do you have an account? <a href="{{ route('login') }}">Log In</a>
        </div>
    </form>
</div>

</body>
</html>
