@extends('staff.layout')

@php($activeNav = 'jobs')

@section('title', 'Manage Jobs')

@section('hero')
    <section class="page-hero">
        <div>
            <span class="eyebrow"><i class="bi bi-briefcase"></i> Job Publishing</span>
            <h2>Create and manage job listings with the same visual system as the rest of the site.</h2>
            <p>Publish new medical or administrative roles, keep listing data tidy, and review current job inventory in a cleaner two-column workspace.</p>
        </div>
    </section>
@endsection

@section('content')
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card-surface">
                <div class="card-body">
                    <div class="section-title">
                        <h3>Create Job</h3>
                        <span>Publish a new role</span>
                    </div>

                    <form method="POST" action="{{ route('staff.jobs.store') }}" class="row g-3">
                        @csrf
                        <div class="col-12">
                            <label class="form-label">Job Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Requirements</label>
                            <textarea name="requirements" class="form-control" rows="4" required>{{ old('requirements') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Department / Category</label>
                            <input type="text" name="department" class="form-control" value="{{ old('department') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Location</label>
                            <input type="text" name="location" class="form-control" value="{{ old('location', 'NUH') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Salary</label>
                            <input type="text" name="salary" class="form-control" value="{{ old('salary') }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Job Type</label>
                            <select name="type" class="form-select" required>
                                <option value="medical" {{ old('type') === 'medical' ? 'selected' : '' }}>Medical</option>
                                <option value="administrative" {{ old('type') === 'administrative' ? 'selected' : '' }}>Administrative</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="active" {{ old('status', 'active') === 'active' ? 'selected' : '' }}>Active</option>
                                <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="closed" {{ old('status') === 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>

                        <div class="col-12 pt-2">
                            <button type="submit" class="btn-brand">Publish Job</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card-surface table-shell">
                <div class="card-body">
                    <div class="section-title">
                        <h3>Published Jobs</h3>
                        <span>{{ method_exists($jobs, 'total') ? $jobs->total() : $jobs->count() }} total</span>
                    </div>
                </div>

                <x-table-filters
                    :action="route('staff.jobs.index')"
                    :type-options="['medical' => 'Medical', 'administrative' => 'Administrative']"
                    :status-options="['active' => 'Active', 'draft' => 'Draft', 'closed' => 'Closed']"
                    type-label="Job Type" />

                <div class="table-responsive">
                    <table class="table table-modern align-middle">
                        <thead>
                            <tr>
                                <th>Title</th>
                                <th>Department</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($jobs as $job)
                                <tr>
                                    <td>
                                        <div class="fw-semibold">{{ $job->title }}</div>
                                        <div class="table-note">{{ \Illuminate\Support\Str::limit($job->description, 90) }}</div>
                                    </td>
                                    <td>{{ $job->department ?: '-' }}</td>
                                    <td>{{ ucfirst($job->type) }}</td>
                                    <td>
                                        <span class="status-badge {{ $job->status === 'active' ? 'status-approved' : ($job->status === 'closed' ? 'status-rejected' : 'status-progress') }}">
                                            {{ ucfirst($job->status) }}
                                        </span>
                                    </td>
                                    <td>{{ optional($job->created_at)->format('Y-m-d') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="empty-state">No jobs created yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="table-filter-pagination p-4 pt-3">
                    {{ $jobs->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection
