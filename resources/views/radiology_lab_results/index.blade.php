@extends('layouts.public_site')

@php($publicSection = $resultType . '_results')

@section('title', $title)

@section('head')
<style>
    .results-grid {
        display: grid;
        grid-template-columns: 0.82fr 1.18fr;
        gap: 22px;
    }

    .result-list {
        display: grid;
        gap: 14px;
    }

    .result-card {
        border-radius: 18px;
        padding: 18px;
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid var(--border);
        box-shadow: 0 12px 28px rgba(27, 58, 100, 0.10);
    }

    .result-card h3 {
        margin: 0 0 8px;
        font-size: 17px;
        font-weight: 800;
    }

    .result-meta {
        color: var(--muted);
        font-size: 13px;
        margin-bottom: 12px;
    }

    .pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 7px 11px;
        border-radius: 999px;
        background: var(--primary-soft);
        color: var(--primary);
        font-size: 12px;
        font-weight: 800;
        margin-bottom: 12px;
    }

    .result-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
    }

    @media (max-width: 991.98px) {
        .results-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
    <section class="page-hero surface-panel">
        <div>
            <span class="eyebrow">
                <i class="bi {{ $resultType === 'laboratory' ? 'bi-prescription2' : 'bi-image' }}"></i>
                {{ $title }}
            </span>
            <h1>{{ $title }}</h1>
            <p>Search using the phone number registered during appointment booking.</p>
        </div>
    </section>

    <div class="results-grid">
        <div class="surface-panel result-card">
            <h3>Search by phone</h3>
            <form method="POST" action="{{ route($searchRoute) }}" class="d-grid gap-3">
                @csrf
                <input type="search" name="phone" class="form-control form-control-lg @error('phone') is-invalid @enderror" value="{{ old('phone', $phone) }}" placeholder="Enter booking phone number" required>
                @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                <button class="btn-main" type="submit"><i class="bi bi-search"></i> Search Results</button>
            </form>
        </div>

        <div class="surface-panel result-card">
            <span class="pill">
                <i class="bi {{ $resultType === 'laboratory' ? 'bi-prescription2' : 'bi-image' }}"></i>
                {{ $title }}
            </span>

            <div class="result-list">
                @forelse($results as $result)
                    <div class="result-card">
                        <h3>{{ $result->title }}</h3>
                        <div class="result-meta">
                            {{ $result->patient?->full_name ?? 'Patient' }}
                            @if($result->appointment_id)
                                | Appointment #{{ $result->appointment_id }}
                            @endif
                            | {{ $result->created_at?->format('Y-m-d') }}
                        </div>
                        <p class="mb-3">{{ $result->notes ?: $result->description }}</p>
                        <div class="result-actions">
                            <a class="btn-ghost" target="_blank" rel="noopener" href="{{ route($previewRoute, ['id' => $result->id]) }}">Preview</a>
                            <a class="btn-main" href="{{ route($downloadRoute, ['id' => $result->id]) }}">Download</a>
                        </div>
                    </div>
                @empty
                    <div class="text-muted">{{ $phone === '' ? 'Search to view results.' : 'No results found for this phone.' }}</div>
                @endforelse
            </div>
        </div>
    </div>
@endsection
