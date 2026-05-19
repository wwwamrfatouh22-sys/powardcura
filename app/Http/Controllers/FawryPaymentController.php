<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Services\Payments\FawryPaymentService;
use App\Support\AuditLogger;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FawryPaymentController extends Controller
{
    public function callback(Request $request, FawryPaymentService $fawry): RedirectResponse
    {
        $payment = $this->handleGatewayPayload($request, $fawry);

        if (! $payment) {
            return redirect()->route('home')->with('error', 'Payment verification failed.');
        }

        if (in_array($payment->status, ['confirmed', 'paid'], true)) {
            return redirect()
                ->route('appointments.invoice', $payment->appointment_id)
                ->with('success', 'Payment completed successfully.');
        }

        $appointment = $payment->appointment;

        return redirect()
            ->route('doctors.show', [
                'doctor' => $appointment->doctor_id,
                'date' => $appointment->date,
                'type' => $appointment->type,
            ])
            ->withErrors(['payment_method' => 'Payment was not completed. Please try again or choose another method.']);
    }

    public function webhook(Request $request, FawryPaymentService $fawry): JsonResponse
    {
        $payment = $this->handleGatewayPayload($request, $fawry);

        return response()->json([
            'ok' => (bool) $payment,
            'status' => $payment?->status,
        ], $payment ? 200 : 422);
    }

    private function handleGatewayPayload(Request $request, FawryPaymentService $fawry): ?Payment
    {
        $payload = array_filter($request->all(), static fn ($value) => $value !== null && $value !== '');
        $reference = $payload['merchantRefNumber'] ?? $payload['merchantRefNum'] ?? null;

        if (! $reference) {
            Log::warning('Fawry callback missing merchant reference.', ['payload' => $payload]);
            return null;
        }

        $payment = Payment::with('appointment')->where('reference_number', $reference)->first();
        if (! $payment || ! $payment->appointment) {
            Log::warning('Fawry callback payment not found.', ['reference' => $reference]);
            return null;
        }

        if (! $fawry->verifyCallback($payload, $payment)) {
            Log::warning('Fawry callback verification failed.', ['payment_id' => $payment->id, 'payload' => $payload]);
            return null;
        }

        $status = $fawry->statusFromPayload($payload);

        DB::transaction(function () use ($payment, $payload, $status): void {
            $gatewayResponse = array_merge($payment->gateway_response ?? [], ['callback' => $payload]);
            $isPaid = $status === 'paid';
            $storedPaymentStatus = $isPaid ? 'confirmed' : $status;

            $payment->update([
                'status' => $storedPaymentStatus,
                'transaction_id' => $payload['referenceNumber'] ?? $payload['fawryRefNumber'] ?? $payment->transaction_id,
                'gateway_response' => $gatewayResponse,
                'paid_at' => $isPaid ? now() : $payment->paid_at,
            ]);

            $payment->appointment->update([
                'payment_status' => $storedPaymentStatus,
                'status' => $isPaid ? 'Confirmed' : ($status === 'pending' ? 'Pending' : 'Canceled'),
            ]);
        });

        AuditLogger::log('payment.fawry_callback', $payment->fresh(), [
            'status' => $status,
            'appointment_id' => $payment->appointment_id,
        ]);

        return $payment->fresh();
    }
}
