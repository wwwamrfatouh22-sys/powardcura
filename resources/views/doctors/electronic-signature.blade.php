<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <title>Electronic Signature</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap"
        rel="stylesheet">

    <style>
    :root {
        --primary: #114a9f;
        --primary-dark: #0a3d86;
        --text: #2f3a45;
        --muted: #6b7280;
        --danger: #ff3b3f;
        --shadow: 0 18px 36px rgba(34, 52, 84, 0.18);
        --radius-xl: 28px;
        --radius-lg: 22px;
        --sidebar-width: 290px;
        --soft-bg: #f7f7f7;
        --card-bg: #f8f8f8;
        --border: #d8dde6;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        font-family: 'Inter', Arial, sans-serif;
        min-height: 100vh;
        overflow-x: hidden;
        color: var(--text);
        background: radial-gradient(circle at 72% 52%, rgba(49, 157, 255, 0.95) 0%, rgba(110, 189, 255, 0.72) 20%, rgba(196, 224, 248, 0.62) 42%, rgba(235, 240, 245, 0.95) 70%, #f2f2f2 100%);
    }

    a {
        text-decoration: none;
        color: inherit;
    }

    button,
    input,
    textarea {
        font-family: inherit;
    }

    @php $doctor=auth()->guard('doctor')->user();

    @endphp .page {
        min-height: 100vh;
        position: relative;
    }

    /* SIDEBAR */
    .sidebar {
        width: var(--sidebar-width);
        padding: 22px 18px;
        position: fixed;
        top: 0;
        left: 0;
        bottom: 0;
        z-index: 1200;
        transform: translateX(-112%);
        transition: transform .35s cubic-bezier(.4, 0, .2, 1);
    }

    .sidebar.active {
        transform: translateX(0);
    }

    .sidebar-panel {
        height: 100%;
        background: rgba(247, 247, 247, 0.97);
        border-radius: 34px;
        box-shadow: 0 16px 35px rgba(0, 0, 0, 0.14);
        display: flex;
        flex-direction: column;
        padding: 28px 18px 18px;
        backdrop-filter: blur(2px);
    }

    .sidebar-logo {
        display: flex;
        justify-content: center;
        align-items: center;
        min-height: 78px;
        margin-bottom: 26px;
    }

    .sidebar-logo img {
        max-width: 145px;
        height: auto;
        object-fit: contain;
    }

    .menu {
        list-style: none;
        display: flex;
        flex-direction: column;
        gap: 8px;
        margin: 0;
        padding: 0;
    }

    .menu li a {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 14px;
        padding: 16px 18px;
        border: none;
        background: transparent;
        border-radius: 18px;
        font-size: 16px;
        color: #2e3844;
        cursor: pointer;
        transition: .22s ease;
        position: relative;
        overflow: hidden;
    }

    .menu li a::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg,
                transparent 30%,
                rgba(255, 255, 255, 0.32) 50%,
                transparent 70%);
        transform: translateX(-100%);
        transition: transform .4s ease;
        pointer-events: none;
    }

    .menu li a:hover::before {
        transform: translateX(100%);
    }

    .menu li a:hover {
        background: rgba(255, 255, 255, 0.35);
        color: #2e3844;
        transform: translateX(4px);
    }

    .menu li.active a {
        background: var(--primary);
        color: #fff;
        box-shadow: 0 10px 20px rgba(17, 74, 159, 0.28);
    }

    .menu li.active a::before {
        display: none;
    }

    .menu i {
        font-size: 21px;
        width: 24px;
        text-align: center;
        transition: .22s ease;
    }

    .menu li a:hover i {
        transform: scale(1.15);
    }

    .logout-wrap {
        margin-top: auto;
        padding-top: 18px;
    }

    .logout-btn {
        width: 100%;
        border: none;
        border-radius: 14px;
        background: var(--danger);
        color: #fff;
        font-size: 18px;
        font-weight: 700;
        padding: 16px 18px;
        cursor: pointer;
        transition: .25s ease;
        position: relative;
        overflow: hidden;
    }

    .logout-btn::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.18), transparent);
        transform: translateX(-100%);
        transition: transform .4s ease;
    }

    .logout-btn:hover::before {
        transform: translateX(100%);
    }

    .logout-btn:hover {
        filter: brightness(.94);
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(255, 59, 63, .3);
    }

    /* OVERLAY */
    .overlay {
        position: fixed;
        inset: 0;
        background: rgba(0, 0, 0, .18);
        opacity: 0;
        pointer-events: none;
        transition: .25s ease;
        z-index: 1100;
    }

    .overlay.show {
        opacity: 1;
        pointer-events: auto;
    }

    /* MAIN */
    .main-content {
        min-height: 100vh;
        padding: 24px 28px 40px;
    }

    .topbar {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 20px;
        padding: 0 0 8px;
        margin-bottom: 28px;
        animation: slideDown .6s ease both;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .left-section {
        display: flex;
        align-items: flex-start;
        gap: 18px;
    }

    .menu-btn {
        font-size: 28px;
        cursor: pointer;
        width: 46px;
        height: 46px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #4b5563;
        transition: .25s ease;
        border: none;
        background: transparent;
    }

    .menu-btn:hover {
        background: rgba(255, 255, 255, 0.55);
        transform: scale(1.08) rotate(90deg);
    }

    .brand-wrapper h1 {
        margin: 0;
        font-size: 21px;
        font-weight: 600;
        letter-spacing: .2px;
    }

    .brand-wrapper p {
        margin: 6px 0 0;
        font-size: 14px;
        color: #4b5563;
    }

    .right {
        display: flex;
        align-items: flex-start;
        gap: 14px;
        flex-wrap: wrap;
        justify-content: flex-end;
    }

    .profile-link {
        display: flex;
        align-items: center;
        gap: 12px;
        color: #1f2937;
        font-size: 15px;
        font-weight: 500;
    }

    .profile-info {
        text-align: left;
    }

    .profile-info strong {
        display: block;
        font-size: 15px;
        font-weight: 600;
    }

    .profile-info span {
        display: block;
        margin-top: 4px;
        color: #6b7280;
        font-size: 13px;
    }

    .avatar {
        width: 44px;
        height: 44px;
        background: #124d9d;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-size: 18px;
        font-weight: 700;
        box-shadow: 0 10px 18px rgba(17, 74, 159, .25);
        transition: .25s ease;
        animation: avatarPulse 3s ease-in-out infinite;
    }

    .avatar:hover {
        transform: scale(1.1) rotate(5deg);
    }

    @keyframes avatarPulse {

        0%,
        100% {
            box-shadow: 0 10px 18px rgba(17, 74, 159, .25);
        }

        50% {
            box-shadow: 0 10px 28px rgba(17, 74, 159, .45);
        }
    }

    /* ALERTS */
    .alert-success,
    .alert-danger {
        max-width: 1080px;
        margin: 0 auto 20px;
        padding: 15px 18px;
        border-radius: 14px;
        font-size: 15px;
        box-shadow: 0 10px 20px rgba(0, 0, 0, .08);
    }

    .alert-success {
        background: #dff4e3;
        color: #166534;
    }

    .alert-danger {
        background: #fde2e4;
        color: #9f1239;
    }

    /* CONTENT */
    .content-wrap {
        max-width: 1080px;
        margin: 0 auto;
    }

    .intro-card {
        background: rgba(248, 248, 248, 0.96);
        border-radius: var(--radius-xl);
        box-shadow: 0 12px 28px rgba(34, 52, 84, 0.16);
        padding: 32px 34px;
        margin-bottom: 24px;
        border-left: 4px solid var(--primary);
        opacity: 0;
        transform: translateY(24px);
        animation: fadeUp .7s ease .2s forwards;
    }

    .intro-card h1 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 10px;
        color: #111827;
    }

    .intro-card p {
        font-size: 16px;
        line-height: 1.7;
        color: #5d6774;
    }

    .card {
        background: rgba(248, 248, 248, 0.97);
        border-radius: 26px;
        padding: 30px 32px;
        box-shadow: 0 12px 28px rgba(34, 52, 84, 0.16);
        margin-bottom: 24px;
        opacity: 0;
        transform: translateY(24px);
        transition: .3s ease;
    }

    .card.visible {
        animation: fadeUp .65s ease forwards;
    }

    .card:hover {
        box-shadow: 0 22px 44px rgba(34, 52, 84, 0.22);
        transform: translateY(-4px);
    }

    .card h2 {
        color: #111827;
        margin-bottom: 14px;
        font-size: 21px;
        font-weight: 700;
    }

    .card p {
        font-size: 15px;
        line-height: 1.7;
        color: #44505d;
    }

    .pdf-viewer {
        margin-top: 20px;
        background: linear-gradient(180deg, #eef4fb 0%, #d9e3ef 100%);
        border-radius: 18px;
        min-height: 340px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        overflow: hidden;
        border: 1px solid #d3dde8;
    }

    .pdf-placeholder {
        text-align: center;
        color: #111827;
    }

    .pdf-placeholder i {
        font-size: 86px;
        margin-bottom: 12px;
        display: block;
        color: #114a9f;
    }

    .pdf-placeholder span {
        font-size: 18px;
        font-weight: 700;
    }

    .signature-note {
        margin-bottom: 18px;
    }

    .signature-box {
        border: 1.8px solid #cfd8e3;
        background: #fff;
        overflow: hidden;
        border-radius: 18px;
        box-shadow: inset 0 0 0 1px rgba(17, 74, 159, .03);
    }

    canvas {
        width: 100%;
        height: 180px;
        display: block;
        cursor: crosshair;
        background: #fff;
    }

    .btn-group {
        margin-top: 22px;
        display: flex;
        gap: 18px;
        flex-wrap: wrap;
    }

    .btn {
        padding: 13px 20px;
        border-radius: 12px;
        border: none;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        transition: .25s ease;
    }

    .btn-clear {
        background: #fff;
        border: 1.8px solid #2d6cdf;
        color: #1450b8;
        min-width: 90px;
        box-shadow: 0 8px 18px rgba(45, 108, 223, .08);
    }

    .btn-clear:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 22px rgba(45, 108, 223, .14);
    }

    .btn-save {
        background: #114a9e;
        color: #fff;
        min-width: 165px;
        box-shadow: 0 10px 20px rgba(17, 74, 158, .22);
        position: relative;
        overflow: hidden;
    }

    .btn-save::before {
        content: "";
        position: absolute;
        inset: 0;
        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.18), transparent);
        transform: translateX(-100%);
        transition: transform .4s ease;
    }

    .btn-save:hover::before {
        transform: translateX(100%);
    }

    .btn-save:hover {
        background: #0d3f87;
        transform: translateY(-2px);
        box-shadow: 0 14px 28px rgba(17, 74, 159, .3);
    }

    .reveal {
        opacity: 0;
        transform: translateY(28px);
        transition: opacity .65s ease, transform .65s ease;
    }

    .reveal.visible {
        opacity: 1;
        transform: translateY(0);
    }

    @keyframes fadeUp {
        from {
            opacity: 0;
            transform: translateY(28px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* RESPONSIVE */
    @media (max-width:992px) {
        .main-content {
            padding: 22px 18px 40px;
        }

        .card,
        .intro-card {
            padding: 28px 24px;
        }

        .pdf-viewer {
            min-height: 280px;
        }

        .topbar {
            flex-direction: column;
            align-items: stretch;
        }

        .right {
            justify-content: space-between;
        }
    }

    @media (max-width:768px) {
        .intro-card h1 {
            font-size: 21px;
        }

        .card h2 {
            font-size: 19px;
        }

        .pdf-placeholder i {
            font-size: 72px;
        }

        .btn-group {
            gap: 12px;
        }
    }

    @media (max-width:620px) {
        .main-content {
            padding: 18px 16px 28px;
        }

        .right {
            flex-direction: column;
            align-items: stretch;
        }

        .profile-link {
            justify-content: space-between;
        }

        .intro-card,
        .card {
            border-radius: 22px;
        }
    }
    </style>
</head>

<body>

    <div class="page">

        <!-- SIDEBAR -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-panel">
                <div class="sidebar-logo">
                    <img src="{{ asset('images/logo_Image.png') }}" alt="NUH">
                </div>

                <ul class="menu">
                    <li>
                        <a href="{{ route('doctor.profile') }}">
                            <i class="bi bi-grid-1x2"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    <li>
                        <a href="{{ route('doctor.appointments') }}">
                            <i class="bi bi-calendar-event"></i>
                            <span>Appointments</span>
                        </a>
                    </li>

                    <li class="active">
                        <a href="{{ route('doctor.signature') }}">
                            <i class="bi bi-pen"></i>
                            <span>Electronic Signature</span>
                        </a>
                    </li>
                </ul>

                <div class="logout-wrap">
                    <a href="{{ url('/') }}" class="logout-btn"
                        style="display:flex;align-items:center;justify-content:center;">
                        Log out
                    </a>
                </div>
            </div>
        </aside>

        <!-- OVERLAY -->
        <div class="overlay" id="overlay"></div>

        <!-- MAIN -->
        <main class="main-content">
            <header class="topbar">
                <div class="left-section">
                    <button class="menu-btn" id="menuBtn" type="button">
                        <i class="bi bi-list"></i>
                    </button>

                    <div class="brand-wrapper">
                        <h1>NUH</h1>
                        <p>Doctor Dashboard</p>
                    </div>
                </div>

                <div class="right">
                    <a href="{{ route('doctor.profile') }}" class="profile-link">
                        <div class="profile-info">
                            <strong>{{ $doctor->name ?? 'Doctor Ahmed' }}</strong>
                            <span>{{ $doctor->department->name_en ?? 'Doctor' }}</span>
                        </div>
                        <div class="avatar">
                            {{ strtoupper(substr($doctor->name ?? 'A',0,1)) }}
                        </div>
                    </a>
                </div>
            </header>

            @if ($errors->any())
            <div class="alert-danger">
                <ul style="margin:0; padding-left:20px;">
                    @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @if(session('success'))
            <div class="alert-success">
                {{ session('success') }}
            </div>
            @endif

            <div class="content-wrap">

                <div class="intro-card">
                    <h1>Sign Medical Document</h1>
                    <p>Please review the medical document carefully and provide your electronic signature below.</p>
                </div>

                <div class="card reveal">
                    <h2>Medical Document (PDF)</h2>

                    <div class="pdf-viewer">
                        <div class="pdf-placeholder">
                            <i class="bi bi-file-earmark-pdf"></i>
                            <span>PDF Preview</span>
                        </div>
                    </div>
                </div>

                <div class="card reveal">
                    <h2>Doctor Signature</h2>
                    <p class="signature-note">Sign inside the box using your mouse.</p>

                    <form method="POST" action="{{ route('signature.store') }}"
                        onsubmit="return prepareSignature(event)">
                        @csrf

                        <input type="hidden" name="signature" id="signatureInput">
                        <input type="hidden" name="document_id" value="{{ $document->id }}">

                        <div class="signature-box">
                            <canvas id="signatureCanvas"></canvas>
                        </div>

                        <div class="btn-group">
                            <button type="button" class="btn btn-clear" onclick="clearCanvas()">Clear</button>
                            <button type="submit" class="btn btn-save">Save Signature</button>
                        </div>
                    </form>
                </div>

            </div>
        </main>
    </div>

    <script>
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('overlay');
    const menuBtn = document.getElementById('menuBtn');

    function toggleSidebar() {
        sidebar.classList.toggle('active');
        overlay.classList.toggle('show');
        menuBtn.querySelector('i').className = sidebar.classList.contains('active') ? 'bi bi-x-lg' : 'bi bi-list';
    }

    function closeSidebar() {
        sidebar.classList.remove('active');
        overlay.classList.remove('show');
        menuBtn.querySelector('i').className = 'bi bi-list';
    }

    menuBtn.addEventListener('click', toggleSidebar);
    overlay.addEventListener('click', closeSidebar);

    const revealObserver = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.15
    });

    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

    const canvas = document.getElementById('signatureCanvas');
    const ctx = canvas.getContext('2d');

    function resizeCanvas() {
        const ratio = Math.max(window.devicePixelRatio || 1, 1);
        const rect = canvas.getBoundingClientRect();
        canvas.width = rect.width * ratio;
        canvas.height = 180 * ratio;
        ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
        ctx.lineWidth = 2.2;
        ctx.lineCap = 'round';
        ctx.strokeStyle = '#41516c';
    }

    resizeCanvas();
    window.addEventListener('resize', resizeCanvas);

    let drawing = false;
    let hasSigned = false;

    function getMousePos(e) {
        const rect = canvas.getBoundingClientRect();
        return {
            x: e.clientX - rect.left,
            y: e.clientY - rect.top
        };
    }

    canvas.addEventListener('mousedown', (e) => {
        drawing = true;
        hasSigned = true;
        const pos = getMousePos(e);
        ctx.beginPath();
        ctx.moveTo(pos.x, pos.y);
    });

    canvas.addEventListener('mouseup', () => {
        drawing = false;
        ctx.beginPath();
    });

    canvas.addEventListener('mouseleave', () => {
        drawing = false;
        ctx.beginPath();
    });

    canvas.addEventListener('mousemove', draw);

    function draw(e) {
        if (!drawing) return;
        const pos = getMousePos(e);
        ctx.lineTo(pos.x, pos.y);
        ctx.stroke();
    }

    function clearCanvas() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);
        hasSigned = false;
        resizeCanvas();
    }

    function prepareSignature(event) {
        if (!hasSigned) {
            alert("Please sign before saving.");
            event.preventDefault();
            return false;
        }

        document.getElementById('signatureInput').value = canvas.toDataURL('image/png');
        return true;
    }

    canvas.addEventListener('touchstart', function(e) {
        e.preventDefault();
        const touch = e.touches[0];
        const rect = canvas.getBoundingClientRect();
        drawing = true;
        hasSigned = true;
        ctx.beginPath();
        ctx.moveTo(touch.clientX - rect.left, touch.clientY - rect.top);
    }, {
        passive: false
    });

    canvas.addEventListener('touchmove', function(e) {
        e.preventDefault();
        if (!drawing) return;
        const touch = e.touches[0];
        const rect = canvas.getBoundingClientRect();
        ctx.lineTo(touch.clientX - rect.left, touch.clientY - rect.top);
        ctx.stroke();
    }, {
        passive: false
    });

    canvas.addEventListener('touchend', function(e) {
        e.preventDefault();
        drawing = false;
        ctx.beginPath();
    }, {
        passive: false
    });
    </script>

</body>

</html>
