@extends('staff.layout')

@php
    $activeNav = 'complaints';
@endphp

@section('title', 'Complaints Management')

@section('hero')
    <section class="page-hero">
        <div>
            <span class="eyebrow"><i class="bi bi-chat-left-text"></i> Complaints Inbox</span>
            <h2>Track complaints with a clearer table, better actions, and full detail viewing.</h2>
            <p>Review submitted complaints with consistent spacing, readable status styling, and a dedicated complaint detail modal without changing complaint backend behavior.</p>
        </div>
    </section>
@endsection

@section('content')
    <div class="card-surface table-shell">
        <div class="card-body">
            <div class="section-title">
                <h3>Complaint Queue</h3>
                <span>{{ method_exists($complaints, 'total') ? $complaints->total() : $complaints->count() }} total</span>
            </div>
        </div>

        <x-table-filters
            :action="route('staff.complaints')"
            :type-options="[]"
            :status-options="['pending' => 'Pending', 'in_progress' => 'In Progress', 'resolved' => 'Resolved']"
            :show-type="false" />

        <div class="table-responsive">
            <table class="table table-modern align-middle mb-0">
                <thead>
                <tr>
                    <th>Patient / User</th>
                    <th>Subject</th>
                    <th>Type</th>
                    <th>Department</th>
                    <th>Preview</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse(($complaints ?? collect()) as $complaint)
                    @php
                        $status = $complaint->status ?? 'pending';
                        $priority = $complaint->priority ?? 'medium';
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $complaint->name }}</div>
                            <div class="table-note">{{ $complaint->email }}</div>
                            <div class="table-note">{{ $complaint->phone }}</div>
                        </td>
                        <td>{{ $complaint->subject ?: $complaint->type }}</td>
                        <td>{{ $complaint->type }}</td>
                        <td>{{ $complaint->department }}</td>
                        <td>{{ \Illuminate\Support\Str::limit($complaint->details, 70) }}</td>
                        <td>{{ optional($complaint->created_at)->format('Y-m-d H:i') }}</td>
                        <td>
                            <span class="status-badge {{ $status === 'resolved' ? 'status-resolved' : ($status === 'in_progress' ? 'status-progress' : 'status-pending') }}">
                                {{ $status === 'in_progress' ? 'In Progress' : ucfirst($status) }}
                            </span>
                        </td>
                        <td>
                            <span class="priority-badge {{ $priority === 'high' ? 'priority-high' : 'priority-medium' }}">
                                {{ ucfirst($priority) }}
                            </span>
                        </td>
                        <td>
                            <div class="action-cluster">
                                <button
                                    type="button"
                                    class="btn-soft complaint-view-btn"
                                    data-bs-toggle="modal"
                                    data-bs-target="#complaintViewModal"
                                    data-name="{{ e($complaint->name) }}"
                                    data-email="{{ e($complaint->email) }}"
                                    data-phone="{{ e($complaint->phone) }}"
                                    data-title="{{ e($complaint->subject ?: $complaint->type) }}"
                                    data-type="{{ e($complaint->type) }}"
                                    data-department="{{ e($complaint->department) }}"
                                    data-message="{{ e($complaint->details) }}"
                                    data-date="{{ e(optional($complaint->created_at)->format('Y-m-d H:i')) }}"
                                    data-status="{{ e($status === 'in_progress' ? 'In Progress' : ucfirst($status)) }}"
                                    data-priority="{{ e(ucfirst($priority)) }}">
                                    View Complaint
                                </button>

                                <form method="POST" action="{{ route('staff.complaint.escalate', $complaint->id) }}">
                                    @csrf
                                    <button type="submit" class="btn-warning-soft">Escalate</button>
                                </form>
                                <form method="POST" action="{{ route('staff.complaint.resolve', $complaint->id) }}">
                                    @csrf
                                    <button type="submit" class="btn-success-soft">Resolve</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty-state">No complaints have been submitted yet.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="table-filter-pagination p-4 pt-3">
            {{ $complaints->links() }}
        </div>
    </div>
@endsection

@section('modals')
    <div class="modal fade" id="complaintViewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title mb-1">Complaint Details</h5>
                        <div class="table-note">Full complaint information</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-6"><div class="info-tile"><strong>Patient Name</strong><span id="complaintModalName"></span></div></div>
                        <div class="col-md-6"><div class="info-tile"><strong>Complaint Title</strong><span id="complaintModalTitle"></span></div></div>
                        <div class="col-md-6"><div class="info-tile"><strong>Email</strong><span id="complaintModalEmail"></span></div></div>
                        <div class="col-md-6"><div class="info-tile"><strong>Phone</strong><span id="complaintModalPhone"></span></div></div>
                        <div class="col-md-6"><div class="info-tile"><strong>Type</strong><span id="complaintModalType"></span></div></div>
                        <div class="col-md-6"><div class="info-tile"><strong>Department</strong><span id="complaintModalDepartment"></span></div></div>
                        <div class="col-md-6"><div class="info-tile"><strong>Date Submitted</strong><span id="complaintModalDate"></span></div></div>
                        <div class="col-md-3"><div class="info-tile"><strong>Status</strong><span id="complaintModalStatus"></span></div></div>
                        <div class="col-md-3"><div class="info-tile"><strong>Priority</strong><span id="complaintModalPriority"></span></div></div>
                    </div>
                    <div class="info-tile">
                        <strong>Full Complaint Message</strong>
                        <div id="complaintModalMessage" style="white-space: pre-wrap;"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-soft" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const complaintModal = document.getElementById('complaintViewModal');

    if (complaintModal) {
        complaintModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;
            if (!button) return;

            document.getElementById('complaintModalName').textContent = button.getAttribute('data-name') || '-';
            document.getElementById('complaintModalTitle').textContent = button.getAttribute('data-title') || '-';
            document.getElementById('complaintModalEmail').textContent = button.getAttribute('data-email') || '-';
            document.getElementById('complaintModalPhone').textContent = button.getAttribute('data-phone') || '-';
            document.getElementById('complaintModalType').textContent = button.getAttribute('data-type') || '-';
            document.getElementById('complaintModalDepartment').textContent = button.getAttribute('data-department') || '-';
            document.getElementById('complaintModalDate').textContent = button.getAttribute('data-date') || '-';
            document.getElementById('complaintModalStatus').textContent = button.getAttribute('data-status') || '-';
            document.getElementById('complaintModalPriority').textContent = button.getAttribute('data-priority') || '-';
            document.getElementById('complaintModalMessage').textContent = button.getAttribute('data-message') || '-';
        });
    }
</script>
@endsection
