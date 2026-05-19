<?php

namespace App\Http\Controllers;

use App\Http\Requests\SiteRatingStoreRequest;
use App\Models\Appointment;
use App\Models\WebsiteRating;
use Illuminate\Database\QueryException;
use Illuminate\Http\RedirectResponse;

class WebsiteRatingController extends Controller
{
    public function store(SiteRatingStoreRequest $request, Appointment $appointment): RedirectResponse
    {
        $patient = auth('patient')->user();
        $authId = $patient?->getAuthIdentifier();

        abort_unless($authId !== null, 403);

        if ((int) $appointment->patient_id !== (int) $authId) {
            abort(403, 'You can only submit website rating for your own booking.');
        }

        if ($request->filled('appointment_id') && (int) $request->input('appointment_id') !== (int) $appointment->id) {
            abort(422, 'Invalid appointment.');
        }

        if ($appointment->websiteRating()->exists()) {
            return redirect()
                ->route('appointments.invoice', $appointment->id)
                ->with('error', 'Already rated');
        }

        $validated = $request->validated();

        $feedback = isset($validated['feedback']) && trim((string) $validated['feedback']) !== ''
            ? trim((string) $validated['feedback'])
            : null;
        $rating = isset($validated['rating']) ? (int) $validated['rating'] : null;

        if ($rating === null && $feedback === null) {
            return redirect()
                ->route('appointments.invoice', $appointment->id);
        }

        try {
            WebsiteRating::create([
                'appointment_id' => $appointment->id,
                'user_id' => $patient->user_id,
                'patient_id' => $authId,
                'rating' => $rating,
                'comment' => $feedback,
            ]);
        } catch (QueryException $exception) {
            if ($this->isDuplicateWebsiteRating($exception)) {
                return redirect()
                    ->route('appointments.invoice', $appointment->id)
                    ->with('error', 'Already rated');
            }

            throw $exception;
        }

        return redirect()
            ->route('appointments.invoice', $appointment->id)
            ->with('success', 'Thanks for rating your website experience.');
    }

    private function isDuplicateWebsiteRating(QueryException $exception): bool
    {
        $sqlState = $exception->errorInfo[0] ?? null;

        $message = $exception->getMessage();

        return $sqlState === '23000'
            && (
                str_contains($message, 'website_ratings_appointment_id_unique')
                || str_contains($message, 'website_ratings.appointment_id')
            );
    }
}
