<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Patient;
use App\Models\Rating;
use Illuminate\Http\Request;

class RatingController extends Controller
{
    public function store(Request $request, Appointment $appointment)
    {
        $patient = auth('patient')->user();

        abort_unless($patient instanceof Patient, 403);

        if ((int) $appointment->patient_id !== (int) $patient->id) {
            abort(403, 'You can only rate your own appointments.');
        }

        if ($appointment->rating()->exists()) {
            return redirect()
                ->route('appointments.invoice', $appointment->id)
                ->with('error', 'Already rated');
        }

        abort_unless($appointment->canReceiveDoctorRating(), 403, 'Doctor ratings are available after your appointment is completed by the doctor.');

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $appointment->loadMissing('doctor');

        Rating::create([
            'appointment_id' => $appointment->id,
            'doctor_id' => $appointment->doctor_id,
            'patient_id' => $patient->id,
            'rating' => (int) $request->input('rating'),
            'comment' => $request->filled('comment') && trim((string) $request->input('comment')) !== ''
                ? trim((string) $request->input('comment'))
                : null,
        ]);

        if ($appointment->doctor) {
            $appointment->doctor->update([
                'rating' => round((float) $appointment->doctor->ratings()->avg('rating'), 1),
            ]);
        }

        return redirect()
            ->route('appointments.invoice', $appointment->id)
            ->with('success', 'Rating saved');
    }
}
