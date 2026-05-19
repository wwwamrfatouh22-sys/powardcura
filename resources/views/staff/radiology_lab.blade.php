@extends('staff.layout')

@php
    $activeNav = 'radiology_lab';
    $isLab = $selectedType === 'laboratory';
    $sectionTitle = $isLab ? 'Laboratory Requests' : 'Radiology Requests';
    $sectionIcon = $isLab ? 'bi-prescription2' : 'bi-image';
@endphp

@section('title', $sectionTitle)

@section('hero')
    <section class="page-hero">
        <div>
            <span class="eyebrow"><i class="bi {{ $sectionIcon }}"></i> {{ $sectionTitle }}</span>
            <h2>{{ $sectionTitle }}</h2>
            <p>Doctor-created diagnostic requests flow here for processing, secure upload, and completion.</p>
        </div>
        @if(count($allowedTypes) > 1)
            <div class="page-actions">
                <a href="{{ route('staff.radiology_lab', ['section' => 'laboratory']) }}" class="btn-soft {{ $isLab ? 'active' : '' }}">
                    <i class="bi bi-prescription2"></i> Lab
                </a>
                <a href="{{ route('staff.radiology_lab', ['section' => 'radiology']) }}" class="btn-soft {{ ! $isLab ? 'active' : '' }}">
                    <i class="bi bi-image"></i> Radiology
                </a>
            </div>
        @endif
    </section>
@endsection

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card-surface stat-card">
                <div class="label">Pending</div>
                <div class="value">{{ $pendingCount }}</div>
                <span class="table-note">Awaiting staff action</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-surface stat-card">
                <div class="label">Processing</div>
                <div class="value">{{ $processingCount }}</div>
                <span class="table-note">Currently in progress</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-surface stat-card">
                <div class="label">Completed</div>
                <div class="value">{{ $completedCount }}</div>
                <span class="table-note">Uploaded protected results</span>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-5">
            <div class="card-surface" id="search-patient">
                <div class="card-body">
                    <div class="section-title">
                        <h3>Select Patient</h3>
                        <span>Appointment booking phone</span>
                    </div>

                    <label class="form-label" for="appointmentPicker">Patient phone</label>
                    <select id="appointmentPicker" class="form-select" size="10">
                        @forelse($appointments as $appointment)
                            @php($patient = $appointment->patient)
                            <option value="{{ $appointment->id }}"
                                data-patient-name="{{ e($patient?->full_name ?? 'Patient') }}"
                                data-patient-phone="{{ e($appointment->phone ?: $patient?->phone) }}"
                                data-title-prefix="{{ $isLab ? 'Lab Result' : 'Radiology Result' }}"
                                data-appointment-label="#{{ $appointment->id }} - {{ $appointment->date ? \Carbon\Carbon::parse($appointment->date)->format('Y-m-d') : 'No date' }}">
                                {{ $patient?->full_name ?? 'Patient' }} - {{ $appointment->phone ?: $patient?->phone ?: '-' }} - Appointment #{{ $appointment->id }}
                            </option>
                        @empty
                            <option disabled>No appointment-linked patients found.</option>
                        @endforelse
                    </select>
                    <div class="table-note mt-2">Patients are selected from booked appointments only.</div>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card-surface" id="upload-result">
                <div class="card-body">
                    <div class="section-title">
                        <h3>Upload {{ $isLab ? 'Lab' : 'Radiology' }} Result</h3>
                        <span id="selectedPatientLabel">Select an appointment first</span>
                    </div>

                    <div class="info-tile mb-3">
                        <strong>Selected appointment</strong>
                        <div id="selectedAppointmentLabel">No appointment selected</div>
                    </div>

                    <form method="POST" action="{{ route('staff.radiology_lab.results.store', ['type' => $selectedType]) }}" enctype="multipart/form-data" class="row g-3">
                        @csrf
                        <input type="hidden" name="appointment_id" id="appointmentId" value="{{ old('appointment_id') }}">

                        <div class="col-md-6">
                            <label class="form-label">Result Title</label>
                            <input type="text" name="title" id="directResultTitle" class="form-control" value="{{ old('title') }}" placeholder="{{ $isLab ? 'CBC, glucose, culture...' : 'Chest X-Ray, MRI, CT...' }}" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">File</label>
                            <input type="file" name="result_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp,.tif,.tiff,.dcm,.doc,.docx" required>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Notes</label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="Optional result notes">{{ old('notes') }}</textarea>
                        </div>

                        <div class="col-12">
                            <button type="submit" class="btn-brand"><i class="bi bi-cloud-arrow-up"></i> Upload Result</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card-surface table-shell mb-4">
        <div class="card-body">
            <div class="section-title">
                <h3>Request Queue</h3>
                <span>{{ $requests->total() }} total</span>
            </div>

            <div class="table-responsive">
                <table class="table table-modern align-middle">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Doctor</th>
                            <th>Phone</th>
                            <th>Request Type</th>
                            <th>Urgency</th>
                            <th>Notes</th>
                            <th>Status</th>
                            <th>Upload</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $requestRecord)
                            <tr>
                                <td>
                                    {{ $requestRecord->patient?->full_name ?? '-' }}
                                    <div class="table-note">Appointment #{{ $requestRecord->appointment_id }}</div>
                                </td>
                                <td>{{ $requestRecord->doctor?->name ?? '-' }}</td>
                                <td>{{ $requestRecord->phone ?: $requestRecord->patient?->phone ?: '-' }}</td>
                                <td>{{ $requestRecord->request_type }}</td>
                                <td>
                                    <span class="priority-badge {{ $requestRecord->priority === 'urgent' ? 'priority-high' : 'status-approved' }}">
                                        {{ ucfirst($requestRecord->priority) }}
                                    </span>
                                </td>
                                <td style="min-width:220px;">{{ $requestRecord->notes ?: '-' }}</td>
                                <td>
                                    @php($statusClass = $requestRecord->status === 'completed' ? 'status-approved' : ($requestRecord->status === 'processing' ? 'status-progress' : 'status-pending'))
                                    <span class="status-badge {{ $statusClass }}">{{ ucfirst($requestRecord->status) }}</span>
                                </td>
                                <td style="min-width:280px;">
                                    <div class="action-cluster">
                                        @if($requestRecord->status === 'pending')
                                            <form method="POST" action="{{ route('staff.diagnostics.processing', ['type' => $selectedType, 'id' => $requestRecord->id]) }}">
                                                @csrf
                                                <button type="submit" class="btn-soft"><i class="bi bi-play"></i> Processing</button>
                                            </form>
                                        @endif

                                        @if($requestRecord->status !== 'completed')
                                            <form method="POST" action="{{ route('staff.diagnostics.complete', ['type' => $selectedType, 'id' => $requestRecord->id]) }}" enctype="multipart/form-data" class="d-grid gap-2" style="min-width:260px;">
                                                @csrf
                                                <input type="file" name="result_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp,.tif,.tiff,.dcm,.doc,.docx" required>
                                                <textarea name="notes" class="form-control" rows="2" placeholder="Result notes">{{ old('notes') }}</textarea>
                                                <button type="submit" class="btn-brand"><i class="bi bi-cloud-arrow-up"></i> Upload & Complete</button>
                                            </form>
                                        @else
                                            <span class="table-note">Completed {{ $requestRecord->completed_at?->format('Y-m-d H:i') }}</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8">
                                    <div class="empty-state">No {{ strtolower($sectionTitle) }} yet.</div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $requests->links() }}
            </div>
        </div>
    </div>

    <div class="card-surface table-shell">
        <div class="card-body">
            <div class="section-title">
                <h3>Recent Completed Results</h3>
                <span>Latest protected files</span>
            </div>

            <div class="table-responsive">
                <table class="table table-modern align-middle">
                    <thead>
                        <tr>
                            <th>Patient</th>
                            <th>Phone</th>
                            <th>Appointment</th>
                            <th>Title</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentResults as $result)
                            <tr>
                                <td>{{ $result->patient?->full_name ?? '-' }}</td>
                                <td>{{ $result->patient_phone ?: $result->patient?->phone ?: '-' }}</td>
                                <td>{{ $result->appointment_id ? '#' . $result->appointment_id : '-' }}</td>
                                <td>{{ $result->title }}</td>
                                <td>{{ $result->created_at?->format('Y-m-d') }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5"><div class="empty-state">No uploads yet.</div></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    const appointmentPicker = document.getElementById('appointmentPicker');
    const appointmentId = document.getElementById('appointmentId');
    const selectedPatientLabel = document.getElementById('selectedPatientLabel');
    const selectedAppointmentLabel = document.getElementById('selectedAppointmentLabel');
    const directResultTitle = document.getElementById('directResultTitle');

    function syncSelectedAppointment() {
        const selected = appointmentPicker?.selectedOptions?.[0];

        if (!selected || selected.disabled) {
            return;
        }

        appointmentId.value = selected.value;
        selectedPatientLabel.textContent = `${selected.dataset.patientName} (${selected.dataset.patientPhone || '-'})`;
        selectedAppointmentLabel.textContent = selected.dataset.appointmentLabel;

        if (directResultTitle && !directResultTitle.value) {
            directResultTitle.value = selected.dataset.titlePrefix || '';
        }
    }

    appointmentPicker?.addEventListener('change', syncSelectedAppointment);
    syncSelectedAppointment();
</script>
@endsection
