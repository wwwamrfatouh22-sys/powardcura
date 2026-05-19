<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $department->name_en }} Doctors</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #0f4ba5;
            --primary-dark: #0b3f8b;
            --text: #243247;
            --muted: #5f6f84;
            --surface: rgba(248, 248, 248, 0.96);
            --shadow: 0 16px 34px rgba(33, 61, 102, 0.16);
            --radius: 24px;
        }

        body {
            margin: 0;
            font-family: 'Inter', 'Segoe UI', Arial, sans-serif;
            color: var(--text);
            background: radial-gradient(circle at 12% 92%, rgba(30, 140, 255, 0.78), rgba(30, 140, 255, 0) 44%),
                        linear-gradient(90deg, #9ecdf3 0%, #edf6ff 40%, #f4f4f6 100%);
            min-height: 100vh;
        }

        .page-shell { max-width: 1200px; margin: 0 auto; padding: 30px 16px 48px; }

        .hero {
            background: var(--surface);
            border-radius: 30px;
            box-shadow: var(--shadow);
            padding: 22px 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 14px;
            margin-bottom: 24px;
        }

        .hero h1 { margin: 0 0 6px; font-size: clamp(24px, 3vw, 34px); font-weight: 700; }
        .hero p { margin: 0; color: var(--muted); font-size: 15px; }

        .back-btn {
            border: 1.5px solid #b9cdea;
            color: var(--primary-dark);
            background: #fff;
            border-radius: 999px;
            padding: 10px 16px;
            text-decoration: none;
            font-weight: 600;
            white-space: nowrap;
        }

        .doctor-card {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            height: 100%;
            padding: 20px;
            transition: transform .25s ease, box-shadow .25s ease;
            display: flex;
            flex-direction: column;
        }

        .doctor-card:hover { transform: translateY(-5px); box-shadow: 0 22px 40px rgba(33, 61, 102, 0.22); }

        .doctor-head { display: flex; align-items: center; gap: 14px; margin-bottom: 14px; }

        .doctor-avatar {
            width: 74px;
            height: 74px;
            border-radius: 50%;
            object-fit: cover;
            background: #d8e7fb;
            border: 3px solid #fff;
            box-shadow: 0 8px 16px rgba(11, 63, 139, 0.18);
            flex-shrink: 0;
        }

        .doctor-title { margin: 0; font-size: 18px; font-weight: 700; }
        .doctor-sub { margin: 4px 0 0; color: var(--muted); font-size: 14px; }

        .doctor-meta {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin: 12px 0 18px;
        }

        .meta-chip {
            background: #eef4ff;
            border-radius: 12px;
            padding: 10px 11px;
            font-size: 13px;
            color: #29456b;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .meta-chip i { color: var(--primary); }

        .doctor-footer { margin-top: auto; }

        .profile-btn {
            width: 100%;
            border: none;
            border-radius: 12px;
            background: var(--primary);
            color: #fff;
            font-weight: 700;
            padding: 12px 14px;
            text-decoration: none;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            transition: .25s ease;
        }

        .profile-btn:hover { background: var(--primary-dark); color: #fff; }

        .empty-card {
            background: var(--surface);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 28px;
            text-align: center;
            color: var(--muted);
        }

        @media (max-width: 768px) {
            .hero { flex-direction: column; align-items: flex-start; }
        }
    </style>
</head>
<body>
<div class="page-shell">
    <section class="hero">
        <div>
            <h1>{{ $department->name_en }} Doctors</h1>
            <p>Choose a specialist and continue to secure online appointment booking.</p>
        </div>
        <a href="{{ route('home') }}#departments-section" class="back-btn"><i class="bi bi-arrow-left"></i> Back To Specialties</a>
    </section>

    <div class="row g-4">
        @forelse($doctors as $doctor)
            <div class="col-md-6 col-lg-4">
                <article class="doctor-card">
                    <div class="doctor-head">
                        <img
                            src="{{ $doctor->image ? asset('images/' . $doctor->image) : asset('images/logo_Image.png') }}"
                            class="doctor-avatar"
                            alt="{{ $doctor->name }}"
                            onerror="this.onerror=null;this.src='{{ asset('images/logo_Image.png') }}';"
                        >
                        <div>
                            <h2 class="doctor-title">{{ $doctor->name }}</h2>
                            <p class="doctor-sub">{{ $doctor->specialization ?: 'General Specialist' }}</p>
                        </div>
                    </div>

                    <div class="doctor-meta">
                        <div class="meta-chip"><i class="bi bi-star-fill"></i> {{ number_format((float) ($doctor->rating ?? 0), 1) }}/5</div>
                        <div class="meta-chip"><i class="bi bi-briefcase-fill"></i> {{ $doctor->experience ?? 0 }} yrs</div>
                        <div class="meta-chip" style="grid-column: span 2;"><i class="bi bi-hospital"></i> {{ $department->name_en }}</div>
                    </div>

                    <div class="doctor-footer">
                        <a href="{{ route('doctors.show', $doctor->id) }}" class="profile-btn">
                            View Profile & Book <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-card">No doctors are currently available in this department.</div>
            </div>
        @endforelse
    </div>
</div>
</body>
</html>
