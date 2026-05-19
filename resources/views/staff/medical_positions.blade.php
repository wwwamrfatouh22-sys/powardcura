@extends('staff.layout')

@php($activeNav = 'applications')

@section('title', 'Medical Positions')

@section('hero')
    <section class="page-hero">
        <div>
            <span class="eyebrow"><i class="bi bi-heart-pulse"></i> Medical Positions</span>
            <h2>Medical position review now follows the same consistent staff interface.</h2>
            <p>This view uses the shared staff layout, card treatment, and responsive table styling so it visually matches the rest of the staff workspace.</p>
        </div>
    </section>
@endsection

@section('content')
    <div class="card-surface table-shell">
        <div class="card-body">
            <div class="section-title">
                <h3>Medical Position Applicants</h3>
                <span>{{ $positions->count() }} total</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-modern align-middle mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Age</th>
                        <th>Gender</th>
                        <th>Phone</th>
                        <th>Department</th>
                        <th>CV</th>
                        <th>Status / Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($positions as $position)
                        <tr>
                            <td>{{ $position->id }}</td>
                            <td class="fw-semibold">{{ $position->name }}</td>
                            <td>{{ $position->age }}</td>
                            <td>{{ ucfirst($position->gender) }}</td>
                            <td>{{ $position->phone }}</td>
                            <td>{{ $position->department->name_en ?? '-' }}</td>
                            <td><a href="{{ asset('storage/'.$position->cv) }}" class="btn-soft">View CV</a></td>
                            <td>
                                <div class="action-cluster">
                                    @if($position->status == 'pending')
                                        <form method="POST" action="{{ route('staff.medical.approve',$position->id) }}">
                                            @csrf
                                            <button type="submit" class="btn-success-soft">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('staff.medical.reject',$position->id) }}">
                                            @csrf
                                            <button type="submit" class="btn-danger-soft">Reject</button>
                                        </form>
                                    @elseif($position->status == 'approved')
                                        <span class="status-badge status-approved">Approved</span>
                                    @elseif($position->status == 'rejected')
                                        <span class="status-badge status-rejected">Rejected</span>
                                    @else
                                        <span class="status-badge status-progress">{{ ucfirst($position->status) }}</span>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">No medical positions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
