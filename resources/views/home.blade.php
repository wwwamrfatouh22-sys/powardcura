<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Al Nahda University Hospital</title>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root { --main-blue: #002147; }

        body { margin: 0; padding: 0; }

        .navbar {
            background-color: var(--main-blue);
            padding: 10px 20px;
        }

        .navbar-brand {
            display: flex;
            align-items: center;
            color: white !important;
            font-weight: bold;
            font-size: 24px;
        }

        .navbar-brand img {
            height: 40px;
            margin-right: 10px;
        }

        .nav-link {
            color: rgba(255,255,255,0.9) !important;
            margin: 0 10px;
            font-size: 15px;
        }

        .nav-link:hover {
            color: white !important;
        }

        .dropdown-menu {
            border-radius: 14px;
            padding: 8px;
            border: none;
        }

        .dropdown-item {
            padding: 10px 15px;
            border-radius: 8px;
        }

        .dropdown-item:hover {
            background-color: #f1f5fb;
        }

        .hero {
            background: linear-gradient(rgba(0,0,0,0.1), rgba(0,0,0,0.1)),
            url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?q=80&w=1470&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            height: 90vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .hero h1 {
            color: #003366;
            font-size: 4rem;
            font-weight: 800;
        }

        .hero p {
            color: #003366;
            font-size: 1.5rem;
        }

        .btn-book {
            background-color: #004494;
            color: white;
            padding: 12px 40px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 25px;
        }

        .btn-book:hover {
            background-color: #003b7a;
            color: white;
        }

        /* Jobs Dropdown Custom Style */
        .jobs-dropdown {
            background-color: var(--main-blue);
            padding: 8px 18px;
            border-radius: 8px;
        }

        .jobs-dropdown:hover {
            background-color: #003366;
        }

        /* Dropdown Menu */
        .jobs-menu {
            width: 240px;
            border-radius: 10px;
            padding: 0;
            overflow: hidden;
        }

        /* Dropdown Items */
        .jobs-menu .dropdown-item {
            padding: 14px 18px;
            background-color: #f2f2f2;
            border-bottom: 1px solid #ddd;
            font-weight: 500;
        }

        .jobs-menu .dropdown-item:last-child {
            border-bottom: none;
        }

        /* Hover effect */
        .jobs-menu .dropdown-item:hover {
            background-color: #e0e0e0;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg">
    <div class="container-fluid">

        <a class="navbar-brand" href="#">
            <img src="{{ asset('images/logoN.png') }}" alt="Logo">
            NUH
        </a>

        <!-- زرار الموبايل -->
        <button class="navbar-toggler bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-center" id="mainNavbar">
            <ul class="navbar-nav">

                <li class="nav-item">
                    <a class="nav-link" href="#">About NUH</a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('departments.index') }}" class="nav-link">
                        Medical Departments
                    </a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="#">Outpatient Clinics</a>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle jobs-dropdown"
                       href="#"
                       id="jobsDropdown"
                       role="button"
                       data-bs-toggle="dropdown"
                       aria-expanded="false">
                        Jobs and Training
                    </a>

                    <ul class="dropdown-menu jobs-menu shadow">
                        <li>
                            <a class="dropdown-item" href="{{ route('jobs.medical') }}">
                                Medical positions
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#">
                                Training
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle"
                       href="#"
                       id="contactDropdown"
                       role="button"
                       data-bs-toggle="dropdown">
                        Contact us
                    </a>

                    <ul class="dropdown-menu shadow">
                        <li>
                            <a class="dropdown-item" href="#">
                                Landline number
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('complaints.create') }}">
                                Complaints and suggestions
                            </a>
                        </li>
                    </ul>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="{{ route('results.index') }}">
                        Radiology & Lab Tests
                    </a>
                </li>

            </ul>
        </div>

        <!-- الجزء اليمين -->
        <div class="d-flex align-items-center">

            <a href="#" class="nav-link small">العربية</a>

            @guest
                <a href="{{ route('register') }}" class="nav-link">Sign up</a>
                <a href="{{ route('login') }}" class="nav-link">Login</a>
            @endguest

            @auth
                <div class="nav-item dropdown ms-2">
                    <a class="nav-link dropdown-toggle"
                       href="#"
                       data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle" style="font-size:20px;"></i>
                    </a>

                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.show') }}">
                                My Profile
                            </a>
                        </li>

                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button class="dropdown-item">
                                    Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            @endauth

        </div>

    </div>
</nav>

@if(session('success'))
    <div class="alert alert-success text-center m-3">
        {{ session('success') }}
    </div>
@endif

<div class="hero">
    <h1>Al Nahda University Hospital</h1>
    <p>Your care starts here</p>

    <a href="{{ route('book.start') }}" class="btn btn-book">
        Book an appointment!
    </a>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
