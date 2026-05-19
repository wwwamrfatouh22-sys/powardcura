@extends('layouts.public_site')

@php($publicSection = 'training')

@section('title', 'Training Programs')

@section('head')
    <style>
        .training-grid {
            margin-top: 8px;
        }

        .training-card {
            height: 100%;
            border-radius: var(--radius-lg);
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            transition: transform .25s ease, box-shadow .25s ease;
        }

        .training-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--shadow-hover);
        }

        .program-title {
            margin: 0;
            font-size: 1.25rem;
            font-weight: 800;
        }

        .program-description {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .program-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }

        .meta-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255, 255, 255, 0.78);
            border: 1px solid var(--border);
            color: #45617e;
            font-size: 12px;
            font-weight: 700;
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
            <span class="eyebrow"><i class="bi bi-mortarboard-fill"></i> NUH Training</span>
            <h1>Training Programs</h1>
            <p>Browse active programs in the same visual language as the careers section, with clear duration details, cleaner spacing, and the existing application flow preserved.</p>
        </div>

        <div class="hero-actions">
            <a href="{{ route('staff.module.jobs') }}" class="btn-ghost">View Jobs</a>
            <a href="{{ route('home') }}#departments-section" class="btn-main">Back To Main Site</a>
        </div>
    </section>

    @if($programs->isEmpty())
        <div class="empty-state surface-panel">No training programs are currently available.</div>
    @else
        <div class="row g-4 training-grid">
            @foreach($programs as $program)
                <div class="col-md-6 col-xl-4">
                    <article class="training-card surface-panel">
                        <div>
                            <h2 class="program-title">{{ $program->title }}</h2>
                            <p class="program-description">{{ $program->description ?: 'A professional training opportunity at NUH.' }}</p>
                        </div>

                        <div class="program-meta">
                            <span class="meta-pill"><i class="bi bi-clock-history"></i> {{ $program->duration_weeks ? $program->duration_weeks . ' weeks' : 'Duration TBD' }}</span>
                            <span class="meta-pill"><i class="bi bi-hospital"></i> {{ $program->department->name_en ?? 'General' }}</span>
                        </div>

                        <div class="mt-auto">
                            <a href="{{ route('training.register', $program) }}" class="btn-main">Apply</a>
                        </div>
                    </article>
                </div>
            @endforeach
        </div>
    @endif
@endsection
