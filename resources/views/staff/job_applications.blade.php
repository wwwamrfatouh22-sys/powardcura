@extends('staff.layout')

@php($activeNav = 'applications')

@section('title', 'Job Applications')

@section('hero')
    <section class="page-hero">
        <div>
            <span class="eyebrow"><i class="bi bi-people"></i> Recruitment Review</span>
            <h2>Review incoming job applications in a clearer and more consistent staff workflow.</h2>
            <p>Browse all submissions or filter between medical and administrative job applications without losing the visual consistency of the staff panel.</p>
        </div>
    </section>
@endsection

@section('content')
    <x-table-filters
        :action="route('staff.job.applications')"
        :type-options="['medical' => 'Medical', 'administrative' => 'Administrative']"
        :status-options="['pending' => 'Pending', 'approved' => 'Approved', 'rejected' => 'Rejected']"
        type-label="Application Type" />

    <div class="card-surface mb-4">
        <div class="card-body">
            <ul class="nav nav-pills gap-2" id="jobTypeTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="btn-soft active" data-bs-toggle="pill" data-bs-target="#all-applications" type="button">All Applications</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="btn-soft" data-bs-toggle="pill" data-bs-target="#medical-applications" type="button">Medical Applications</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="btn-soft" data-bs-toggle="pill" data-bs-target="#administrative-applications" type="button">Administrative Applications</button>
                </li>
            </ul>
        </div>
    </div>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="all-applications" role="tabpanel">
            @include('staff.partials.job_applications_table', ['rows' => $applications, 'showPagination' => true])
        </div>
        <div class="tab-pane fade" id="medical-applications" role="tabpanel">
            @include('staff.partials.job_applications_table', ['rows' => $medicalApplications])
        </div>
        <div class="tab-pane fade" id="administrative-applications" role="tabpanel">
            @include('staff.partials.job_applications_table', ['rows' => $administrativeApplications])
        </div>
    </div>
@endsection
