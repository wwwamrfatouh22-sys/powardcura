@extends('layouts.public_site')

@php($publicSection = 'jobs')

@section('title', 'Job Opportunities')

@section('head')
    <style>
        .listing-grid {
            margin-top: 8px;
        }

        .listing-card {
            height: 100%;
            border-radius: var(--radius-lg);
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .listing-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-hover);
        }

        .card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 14px;
        }

        .card-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 800;
        }

        .card-subtitle {
            margin: 8px 0 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .type-badge,
        .meta-pill {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }

        .type-badge {
            padding: 8px 12px;
            background: var(--primary-soft);
            color: var(--primary-dark);
            white-space: nowrap;
        }

        .card-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .meta-pill {
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.78);
            border: 1px solid var(--border);
            color: #45617e;
        }

        .empty-state {
            border-radius: var(--radius-lg);
            padding: 30px 26px;
            color: var(--muted);
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <section class="page-hero surface-panel">
        <div>
            <span class="eyebrow"><i class="bi bi-briefcase-fill"></i> NUH Careers</span>
            <h1>Job Opportunities</h1>
            <p>Explore open roles designed with the same clean experience as the rest of the hospital website, then move straight into the existing application flow without any backend changes.</p>
        </div>

        <div class="hero-actions">
            <a href="{{ route('staff.module.training') }}" class="btn-ghost">View Training Programs</a>
            <a href="{{ route('home') }}#departments-section" class="btn-main">Back To Main Site</a>
        </div>
    </section>

    <div class="row g-4 listing-grid">
        @forelse($medicalJobs->concat($administrativeJobs) as $job)
            @php($isAdministrative = in_array($job->type, ['administrative', 'admin'], true))
            <div class="col-md-6 col-xl-4">
                <article class="listing-card surface-panel">
                    <div class="card-top">
                        <div>
                            <h2 class="card-title">{{ $job->title }}</h2>
                            <p class="card-subtitle">{{ \Illuminate\Support\Str::limit($job->description ?: 'Join the NUH team through a published opening.', 140) }}</p>
                        </div>
                        <span class="type-badge">{{ $isAdministrative ? 'Administrative' : 'Medical' }}</span>
                    </div>

                    <div class="card-meta">
                        @if($job->department)
                            <span class="meta-pill">{{ $job->department }}</span>
                        @endif
                        @if($job->location)
                            <span class="meta-pill">{{ $job->location }}</span>
                        @endif
                    </div>

                    <div class="mt-auto">
                        <a href="{{ route('jobs.apply', $job) }}" class="btn-main">Apply</a>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state surface-panel">No job opportunities are currently available.</div>
            </div>
        @endforelse
    </div>
@endsection
