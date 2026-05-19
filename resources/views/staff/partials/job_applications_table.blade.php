<div class="card-surface table-shell mb-4">
    <div class="table-responsive">
        <table class="table table-modern align-middle mb-0">
            <thead>
            <tr>
                <th>Applicant Name</th>
                <th>Job Title</th>
                <th>Job Type</th>
                <th>Status</th>
                <th>Submission Date</th>
                <th>CV Actions</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($rows as $application)
                @php
                    $jobTypeRaw = $application->job->type ?? 'medical';
                    $jobType = in_array($jobTypeRaw, ['administrative', 'admin'], true) ? 'Administrative' : 'Medical';
                    $status = $application->status ?? 'pending';
                @endphp
                <tr>
                    <td class="fw-semibold">{{ $application->full_name ?? $application->name }}</td>
                    <td>{{ $application->job->title ?? '-' }}</td>
                    <td>{{ $jobType }}</td>
                    <td>
                        <span class="status-badge {{ $status === 'approved' ? 'status-approved' : ($status === 'rejected' ? 'status-rejected' : 'status-pending') }}">
                            {{ ucfirst($status) }}
                        </span>
                    </td>
                    <td>{{ $application->created_at?->format('Y-m-d H:i') }}</td>
                    <td>
                        @if($application->cv_exists)
                            <div class="action-cluster">
                                <a href="{{ route('staff.job.cv.view', $application->id) }}" target="_blank" class="btn-soft">View CV</a>
                                <a href="{{ route('staff.job.cv.download', $application->id) }}" class="btn-soft">Download CV</a>
                            </div>
                        @else
                            <span class="table-note text-danger">CV Not Available</span>
                        @endif
                    </td>
                    <td>
                        @if($status === 'pending')
                            <div class="action-cluster">
                                <form method="POST" action="{{ route('staff.job.approve', $application->id) }}">
                                    @csrf
                                    <button class="btn-success-soft" type="submit">Approve</button>
                                </form>
                                <form method="POST" action="{{ route('staff.job.reject', $application->id) }}">
                                    @csrf
                                    <button class="btn-danger-soft" type="submit">Reject</button>
                                </form>
                            </div>
                        @else
                            <span class="table-note">Reviewed</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="empty-state">No applications found in this category.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>
@if(($showPagination ?? false) && method_exists($rows, 'links'))
    <div class="table-filter-pagination p-4 pt-0">
        {{ $rows->links() }}
    </div>
@endif
