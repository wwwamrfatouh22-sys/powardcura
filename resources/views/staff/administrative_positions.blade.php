@extends('staff.layout')

@php($activeNav = 'administrative')

@section('title', 'Administrative Positions')

@section('hero')
    <section class="page-hero">
        <div>
            <span class="eyebrow"><i class="bi bi-person-vcard"></i> Administrative Positions</span>
            <h2>Review administrative position applicants in the same modern staff table layout.</h2>
            <p>Keep candidate review clean and consistent with readable rows, better hover states, and clear approval controls that stay responsive across screen sizes.</p>
        </div>
    </section>
@endsection

@section('content')
    <div class="card-surface table-shell">
        <div class="card-body">
            <div class="section-title">
                <h3>Administrative Position Applicants</h3>
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
                        <th>University</th>
                        <th>Department</th>
                        <th>CV</th>
                        <th>GPA</th>
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
                            <td>{{ $position->university ?? '-' }}</td>
                            <td>{{ $position->department->name_en ?? '-' }}</td>
                            <td>
                                <a href="{{ asset('storage/'.$position->cv) }}" class="btn-soft">View CV</a>
                            </td>
                            <td>{{ $position->gpa ?? '-' }}</td>
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
                            <td colspan="10" class="empty-state">No administrative positions found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
