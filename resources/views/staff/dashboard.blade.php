@extends('staff.layout')

@php($activeNav = 'dashboard')

@section('title', 'Staff Dashboard')

@section('hero')
    <section class="page-hero">
        <div>
            <span class="eyebrow"><i class="bi bi-stars"></i> Staff Overview</span>
            <h2>Staff dashboard with a cleaner, unified workspace.</h2>
            <p>Review recruitment activity, training registrations, complaints, and live job publishing from one consistent interface.</p>
        </div>
    </section>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-md-6 col-xl-3">
            <div class="card-surface stat-card">
                <div class="label">Received Job Applications</div>
                <div class="value">{{ $jobApplicationsCount }}</div>
                <p class="table-note mb-4">Applications awaiting staff review and action.</p>
                <a href="{{ route('staff.job.applications') }}" class="btn-brand">Open Applications</a>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card-surface stat-card">
                <div class="label">Training Registrations</div>
                <div class="value">{{ $trainingRegistrationsCount }}</div>
                <p class="table-note mb-4">New program registrations submitted through the website.</p>
                <a href="{{ route('staff.training.programs') }}" class="btn-brand">Open Training</a>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card-surface stat-card">
                <div class="label">Published Jobs</div>
                <div class="value">{{ $publishedJobsCount }}</div>
                <p class="table-note mb-4">Manage current listings and publish new positions.</p>
                <a href="{{ route('staff.jobs.index') }}" class="btn-brand">Manage Jobs</a>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="card-surface stat-card">
                <div class="label">Complaints Inbox</div>
                <div class="value">{{ $complaintsCount }}</div>
                <p class="table-note mb-4">Track complaint status and review details quickly.</p>
                <a href="{{ route('staff.complaints') }}" class="btn-brand">Open Complaints</a>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-lg-7">
            <div class="card-surface">
                <div class="card-body">
                    <div class="section-title">
                        <h3>Workflow Snapshot</h3>
                        <span>At-a-glance guidance</span>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-tile">
                                <strong>Recruitment</strong>
                                Keep job postings fresh, review CVs quickly, and update applicant status from the same staff workspace.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-tile">
                                <strong>Training</strong>
                                Separate training registrations from job applications so each queue remains clear and easy to review.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-tile">
                                <strong>Complaints</strong>
                                View full complaint details without leaving the complaints table and keep escalation actions straightforward.
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-tile">
                                <strong>Publishing</strong>
                                Keep hiring updates and staff-facing actions visually aligned so the workspace stays simple to scan.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card-surface">
                <div class="card-body">
                    <div class="section-title">
                        <h3>Quick Links</h3>
                        <span>Fast actions</span>
                    </div>
                    <div class="d-grid gap-3">
                        <a href="{{ route('staff.jobs.index') }}" class="btn-soft"><i class="bi bi-plus-circle"></i> Create or Publish Jobs</a>
                        <a href="{{ route('staff.job.applications') }}" class="btn-soft"><i class="bi bi-people"></i> Review Job Applications</a>
                        <a href="{{ route('staff.training.programs') }}" class="btn-soft"><i class="bi bi-mortarboard"></i> Review Training Registrations</a>
                        <a href="{{ route('staff.complaints') }}" class="btn-soft"><i class="bi bi-chat-square-text"></i> Review Complaint Inbox</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
