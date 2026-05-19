@extends('staff.layout')

@php
    use Carbon\Carbon;
    $activeNav = 'leave';
    $currentLeave = $leaveRequests->where('status', 'pending')->first();
@endphp

@section('title', 'Create Leave Request')

@section('hero')
    <section class="page-hero">
        <div>
            <span class="eyebrow"><i class="bi bi-calendar-plus"></i> New Leave Request</span>
            <h2>Submit leave requests in a cleaner, more readable staff form.</h2>
            <p>This page keeps the existing leave submission flow intact while improving spacing, card structure, and responsiveness for staff on desktop and mobile.</p>
        </div>

        <div class="page-actions">
            <a href="{{ route('staff.leave.index') }}" class="btn-soft"><i class="bi bi-arrow-left"></i> Back to Requests</a>
        </div>
    </section>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card-surface">
                <div class="card-body">
                    <div class="section-title">
                        <h3>Leave Request Form</h3>
                        <span>Submit a new request</span>
                    </div>

                    <form method="POST" action="{{ route('staff.leave.store') }}" class="row g-3">
                        @csrf
                        <div class="col-md-6">
                            <label class="form-label">Select Doctor</label>
                            <select name="doctor_id" class="form-select">
                                <option value="">Select doctor</option>
                                @foreach($doctors as $doctor)
                                    <option value="{{ $doctor->id }}" {{ old('doctor_id') == $doctor->id ? 'selected' : '' }}>
                                        {{ $doctor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label">Reason for Leave</label>
                            <textarea name="reason" class="form-control" rows="5">{{ old('reason') }}</textarea>
                        </div>

                        <div class="col-12 pt-2">
                            <button class="btn-brand" type="submit">Submit Request</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="info-stack">
                @if($currentLeave)
                    <div class="card-surface">
                        <div class="card-body">
                            <div class="section-title">
                                <h4>Current Leave Request</h4>
                                <span class="status-badge status-pending">Pending</span>
                            </div>
                            <div class="info-tile mb-3">
                                <strong>Leave Window</strong>
                                {{ Carbon::parse($currentLeave->start_date)->format('M d, Y') }} - {{ Carbon::parse($currentLeave->end_date)->format('M d, Y') }}
                            </div>
                            <div class="info-tile mb-3">
                                <strong>Duration</strong>
                                {{ Carbon::parse($currentLeave->start_date)->diffInDays(Carbon::parse($currentLeave->end_date)) + 1 }} days
                            </div>
                            <div class="info-tile">
                                <strong>Reason</strong>
                                {{ $currentLeave->reason }}
                            </div>
                        </div>
                    </div>
                @endif

                <div class="card-surface">
                    <div class="card-body">
                        <div class="section-title">
                            <h4>Leave Request History</h4>
                            <span>{{ $leaveRequests->count() }} records</span>
                        </div>

                        <div class="info-stack">
                            @forelse($leaveRequests as $leave)
                                <div class="info-tile">
                                    <strong>{{ Carbon::parse($leave->start_date)->format('M d, Y') }} - {{ Carbon::parse($leave->end_date)->format('M d, Y') }}</strong>
                                    <div class="mb-2">Duration: {{ Carbon::parse($leave->start_date)->diffInDays(Carbon::parse($leave->end_date)) + 1 }} days</div>
                                    <div class="mb-2">Reason: {{ $leave->reason }}</div>
                                    <span class="status-badge {{ $leave->status === 'approved' ? 'status-approved' : ($leave->status === 'rejected' ? 'status-rejected' : 'status-pending') }}">{{ ucfirst($leave->status) }}</span>
                                </div>
                            @empty
                                <div class="info-tile">No leave history available yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
