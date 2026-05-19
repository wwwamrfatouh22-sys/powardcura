@extends('staff.layout')

@php($activeNav = 'training')

@section('title', 'Training Registrations')

@section('hero')
    <section class="page-hero">
        <div>
            <span class="eyebrow"><i class="bi bi-mortarboard"></i> Training Programs</span>
            <h2>Review training registrations in the same polished staff interface.</h2>
            <p>Keep program submissions easy to scan, approve, reject, and review with the same table spacing, button language, and responsive behavior used across the staff area.</p>
        </div>
    </section>
@endsection

@section('content')
    <div class="card-surface table-shell">
        <div class="card-body">
            <div class="section-title">
                <h3>Training Registrations</h3>
                <span>{{ method_exists($registrations, 'total') ? $registrations->total() : $registrations->count() }} total</span>
            </div>
        </div>

        <x-table-filters
            :action="route('staff.training.programs')"
            :type-options="[]"
            :status-options="['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']"
            :show-type="false" />

        <div class="table-responsive">
            <table class="table table-modern align-middle mb-0">
                <thead>
                <tr>
                    <th>Full Name</th>
                    <th>Program</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>National ID</th>
                    <th>Age</th>
                    <th>Gender</th>
                    <th>Department</th>
                    <th>University</th>
                    <th>GPA</th>
                    <th>Status</th>
                    <th>Submitted</th>
                    <th>CV</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($registrations as $registration)
                    <tr>
                        <td class="fw-semibold">{{ $registration->full_name }}</td>
                        <td>{{ $registration->training->title ?? '-' }}</td>
                        <td>{{ $registration->email }}</td>
                        <td>{{ $registration->phone }}</td>
                        <td>{{ $registration->national_id }}</td>
                        <td>{{ $registration->age ?? '-' }}</td>
                        <td>{{ $registration->gender ? ucfirst($registration->gender) : '-' }}</td>
                        <td>{{ $registration->department->name_en ?? '-' }}</td>
                        <td>{{ $registration->university ?? '-' }}</td>
                        <td>{{ $registration->gpa ?? '-' }}</td>
                        <td>
                            @if($registration->status === 'approved')
                                <span class="status-badge status-approved">Approved</span>
                            @elseif($registration->status === 'rejected')
                                <span class="status-badge status-rejected">Rejected</span>
                            @else
                                <span class="status-badge status-pending">Pending</span>
                            @endif
                        </td>
                        <td>{{ $registration->created_at?->format('Y-m-d H:i') }}</td>
                        <td>
                            @if($registration->cv_exists)
                                <div class="action-cluster">
                                    <a href="{{ route('staff.training.cv.view', $registration->id) }}" target="_blank" class="btn-soft">View CV</a>
                                    <a href="{{ route('staff.training.cv.download', $registration->id) }}" class="btn-soft">Download CV</a>
                                </div>
                            @else
                                <span class="table-note text-danger">CV Not Available</span>
                            @endif
                        </td>
                        <td>
                            @if($registration->status === 'pending')
                                <div class="action-cluster">
                                    <form method="POST" action="{{ route('staff.training.approve', $registration->id) }}">
                                        @csrf
                                        <button class="btn-success-soft" type="submit">Approve</button>
                                    </form>
                                    <form method="POST" action="{{ route('staff.training.reject', $registration->id) }}">
                                        @csrf
                                        <button class="btn-danger-soft" type="submit">Reject</button>
                                    </form>
                                </div>
                            @else
                                <span class="table-note">Processed</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="14" class="empty-state">No training registrations received yet.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-filter-pagination p-4 pt-3">
            {{ $registrations->links() }}
        </div>
    </div>
@endsection
