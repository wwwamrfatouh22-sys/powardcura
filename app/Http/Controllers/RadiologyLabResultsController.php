<?php

namespace App\Http\Controllers;

use App\Models\LabTest;
use App\Models\RadiologyResult;
use App\Support\AuditLogger;
use App\Support\ProtectedFile;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class RadiologyLabResultsController extends Controller
{
    public function radiologyIndex(Request $request): View
    {
        return $this->viewForType($request, 'radiology');
    }

    public function laboratoryIndex(Request $request): View
    {
        return $this->viewForType($request, 'laboratory');
    }

    public function radiologySearch(Request $request): View
    {
        return $this->searchForType($request, 'radiology');
    }

    public function laboratorySearch(Request $request): View
    {
        return $this->searchForType($request, 'laboratory');
    }

    public function radiologyPreview(Request $request, int $id): BinaryFileResponse
    {
        return $this->preview($request, 'radiology', $id);
    }

    public function laboratoryPreview(Request $request, int $id): BinaryFileResponse
    {
        return $this->preview($request, 'laboratory', $id);
    }

    public function radiologyDownload(Request $request, int $id): StreamedResponse
    {
        return $this->download($request, 'radiology', $id);
    }

    public function laboratoryDownload(Request $request, int $id): StreamedResponse
    {
        return $this->download($request, 'laboratory', $id);
    }

    private function searchForType(Request $request, string $type): View
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:30', 'regex:/^[0-9+\-\s()]+$/'],
        ]);

        $phone = $this->sanitizePhone($validated['phone']);
        $request->session()->put($this->sessionKey($type), $phone);

        return $this->resultsView($type, $phone);
    }

    private function viewForType(Request $request, string $type): View
    {
        $phone = trim((string) $request->session()->get($this->sessionKey($type), ''));

        return $this->resultsView($type, $phone);
    }

    private function resultsView(string $type, string $phone): View
    {
        $results = collect();

        if ($phone !== '') {
            $model = $type === 'laboratory' ? LabTest::class : RadiologyResult::class;
            $needle = $this->normalizePhone($phone);

            $results = $model::query()
                ->with(['patient', 'appointment'])
                ->latest()
                ->get()
                ->filter(fn ($result): bool => $this->recordMatchesPhone($result, $needle))
                ->values();
        }

        return view('radiology_lab_results.index', [
            'phone' => $phone,
            'results' => $results,
            'resultType' => $type,
            'title' => $type === 'laboratory' ? 'Laboratory Results' : 'Radiology Results',
            'searchRoute' => $type === 'laboratory' ? 'laboratory_results.search' : 'radiology_results.search',
            'previewRoute' => $type === 'laboratory' ? 'laboratory_results.preview' : 'radiology_results.preview',
            'downloadRoute' => $type === 'laboratory' ? 'laboratory_results.download' : 'radiology_results.download',
        ]);
    }

    private function preview(Request $request, string $type, int $id): BinaryFileResponse
    {
        $record = $this->authorizedRecord($request, $type, $id);

        AuditLogger::log('medical_result.public_file_previewed', $record, [
            'result_type' => $type,
        ]);

        return ProtectedFile::inline($this->medicalResultPath($type, $record->file_name), 'medical-result-' . $record->id);
    }

    private function download(Request $request, string $type, int $id): StreamedResponse
    {
        $record = $this->authorizedRecord($request, $type, $id);

        AuditLogger::log('medical_result.public_file_accessed', $record, [
            'result_type' => $type,
        ]);

        return ProtectedFile::download($this->medicalResultPath($type, $record->file_name), 'medical-result-' . $record->id);
    }

    private function authorizedRecord(Request $request, string $type, int $id): Model
    {
        $phone = trim((string) $request->session()->get($this->sessionKey($type), ''));
        abort_unless($phone !== '', 403);

        $record = ($type === 'laboratory' ? LabTest::query() : RadiologyResult::query())
            ->with(['patient', 'appointment'])
            ->findOrFail($id);

        abort_unless($record->file_name, 404, 'No file attached.');
        abort_unless($this->recordMatchesPhone($record, $this->normalizePhone($phone)), 403);

        return $record;
    }

    private function recordMatchesPhone(Model $record, string $phone): bool
    {
        return in_array($phone, [
            $this->normalizePhone($record->patient_phone),
            $this->normalizePhone($record->appointment?->phone),
            $this->normalizePhone($record->patient?->phone),
        ], true);
    }

    private function sanitizePhone(string $phone): string
    {
        return trim(preg_replace('/[^0-9+\-\s()]/', '', $phone));
    }

    private function normalizePhone(?string $phone): string
    {
        return preg_replace('/\D+/', '', (string) $phone);
    }

    private function sessionKey(string $type): string
    {
        return $type . '_results_phone';
    }

    private function medicalResultPath(string $type, string $fileName): string
    {
        if (str_contains($fileName, '/')) {
            return $fileName;
        }

        return 'medical-results/' . ($type === 'laboratory' ? 'lab' : 'radiology') . '/' . basename($fileName);
    }
}
