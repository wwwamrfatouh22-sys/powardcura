@extends('staff.layout')

@php($activeNav = 'leave')

@section('title', 'Leave Requests')

@section('hero')
    <section class="page-hero">
        <div>
            <span class="eyebrow"><i class="bi bi-calendar2-week"></i> Leave Requests</span>
            <h2>Review and manage leave requests in a layout aligned with the rest of the website.</h2>
            <p>Approve or reject requests from a cleaner table with stronger spacing, clearer status styling, and the same visual language used across the staff workspace.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('staff.leave.create') }}" class="btn-brand"><i class="bi bi-plus-circle"></i> Add New Leave Request</a>
        </div>
    </section>
@endsection

@section('content')
    <div class="card-surface table-shell">
        <div class="card-body">
            <div class="section-title">
                <h3>Leave Requests Management</h3>
                <span>{{ $leaveRequests->count() }} total</span>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-modern align-middle mb-0">
                <thead>
                    <tr>
                        <th>Doctor Name</th>
                        <th>Department</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Reason</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaveRequests as $request)
                        @php($status = $request->status ?? 'pending')
                        <tr>
                            <td class="fw-semibold">{{ $request->doctor->name ?? '-' }}</td>
                            <td>{{ $request->doctor->department->name_en ?? '-' }}</td>
                            <td>{{ $request->start_date }}</td>
                            <td>{{ $request->end_date }}</td>
                            <td>{{ $request->reason }}</td>
                            <td>
                                <span class="status-badge {{ $status === 'approved' ? 'status-approved' : ($status === 'rejected' ? 'status-rejected' : 'status-pending') }}">
                                    {{ ucfirst($status) }}
                                </span>
                            </td>
                            <td>
                                @if($status === 'pending')
                                    <div class="action-cluster">
                                        <form method="POST" action="{{ route('staff.leave.approve',$request->id) }}">
                                            @csrf
                                            <button class="btn-success-soft" type="submit">Approve</button>
                                        </form>
                                        <form method="POST" action="{{ route('staff.leave.reject',$request->id) }}">
                                            @csrf
                                            <button class="btn-danger-soft" type="submit">Reject</button>
                                        </form>
                                    </div>
                                @else
                                    <span class="table-note">Decision recorded</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty-state">No leave requests found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
