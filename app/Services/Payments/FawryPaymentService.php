<?php

namespace App\Services\Payments;

use App\Models\Appointment;
use App\Models\Payment;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class FawryPaymentService
{
    public function isConfigured(): bool
    {
        return filled(config('payment.fawry.merchant_code')) && filled(config('payment.fawry.secure_key'));
    }

    public function createPendingPayment(Appointment $appointment, string $method): Payment
    {
        return Payment::create([
            'appointment_id' => $appointment->id,
            'payment_method' => $method,
            'reference_number' => $this->makeMerchantReference($appointment),
            'amount' => (float) $appointment->payment_amount,
            'status' => 'pending',
        ]);
    }

    /**
     * @return array<string, mixed>
     *
     * @throws RequestException
     */
    public function initiate(Payment $payment): array
    {
        if (! $this->isConfigured()) {
            throw new RuntimeException('Fawry credentials are not configured.');
        }

        $appointment = $payment->appointment()->with(['doctor.department', 'patient'])->firstOrFail();
        $amount = $this->formatAmount((float) $payment->amount);
        $paymentMethod = $this->gatewayPaymentMethod($payment->payment_method);
        $returnUrl = route('payments.fawry.callback');
        $webhookUrl = route('payments.fawry.webhook');

        $payload = [
            'merchantCode' => config('payment.fawry.merchant_code'),
            'merchantRefNum' => $payment->reference_number,
            'customerName' => trim(($appointment->first_name ?? '') . ' ' . ($appointment->last_name ?? '')) ?: 'Patient',
            'customerMobile' => $appointment->phone,
            'customerEmail' => $appointment->email ?: 'patient-' . $appointment->id . '@cura-axis.local',
            'customerProfileId' => (string) $appointment->patient_id,
            'paymentMethod' => $paymentMethod,
            'amount' => $amount,
            'currencyCode' => config('payment.fawry.currency', 'EGP'),
            'language' => config('payment.fawry.language', 'en-gb'),
            'chargeItems' => [[
                'itemId' => 'appointment-' . $appointment->id,
                'description' => 'Medical appointment #' . $appointment->id,
                'price' => $amount,
                'quantity' => 1,
            ]],
            'enable3DS' => true,
            'authCaptureModePayment' => true,
            'returnUrl' => $returnUrl,
            'orderWebHookUrl' => $webhookUrl,
            'description' => 'Medical appointment #' . $appointment->id,
        ];

        $payload['signature'] = $this->chargeSignature(
            (string) $payload['merchantRefNum'],
            (string) $payload['customerProfileId'],
            $paymentMethod,
            $amount,
            $returnUrl
        );

        $response = Http::acceptJson()
            ->asJson()
            ->timeout(30)
            ->post(config('payment.fawry.charge_url'), $payload)
            ->throw()
            ->json();

        $payment->update([
            'gateway_response' => $response,
            'transaction_id' => $response['referenceNumber'] ?? $response['fawryRefNumber'] ?? $payment->transaction_id,
        ]);

        return is_array($response) ? $response : [];
    }

    public function redirectUrl(array $response): ?string
    {
        return $response['nextAction']['redirectUrl']
            ?? $response['redirectUrl']
            ?? $response['paymentUrl']
            ?? null;
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function verifyCallback(array $payload, Payment $payment): bool
    {
        $reference = $payload['merchantRefNumber'] ?? $payload['merchantRefNum'] ?? null;
        if ((string) $reference !== (string) $payment->reference_number) {
            return false;
        }

        $orderAmount = $payload['orderAmount'] ?? $payload['amount'] ?? $payload['paymentAmount'] ?? null;
        if ($orderAmount !== null && $this->amountCents((float) $orderAmount) !== $this->amountCents((float) $payment->amount)) {
            return false;
        }

        if (! (bool) config('payment.fawry.verify_signature', true)) {
            return true;
        }

        $signature = $payload['signature'] ?? $payload['messageSignature'] ?? null;
        if (! is_string($signature) || $signature === '') {
            return false;
        }

        return hash_equals($this->responseSignature($payload), strtolower($signature));
    }

    /**
     * @param array<string, mixed> $payload
     */
    public function statusFromPayload(array $payload): string
    {
        $status = strtoupper((string) ($payload['orderStatus'] ?? $payload['paymentStatus'] ?? $payload['status'] ?? ''));

        return match ($status) {
            'PAID', 'CAPTURED', 'SUCCESS', 'SUCCESSFUL' => 'paid',
            'CANCELED', 'CANCELLED', 'EXPIRED' => 'canceled',
            'FAILED', 'DECLINED', 'UNPAID' => 'failed',
            default => 'pending',
        };
    }

    public function formatAmount(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }

    private function makeMerchantReference(Appointment $appointment): string
    {
        return 'APT-' . $appointment->id . '-' . now()->format('YmdHis') . '-' . Str::upper(Str::random(6));
    }

    private function gatewayPaymentMethod(string $method): string
    {
        return config('payment.fawry.payment_methods.' . $method, 'PayUsingCC');
    }

    private function chargeSignature(string $merchantRef, string $profileId, string $method, string $amount, string $returnUrl): string
    {
        return hash('sha256', implode('', [
            config('payment.fawry.merchant_code'),
            $merchantRef,
            $profileId,
            $method,
            $amount,
            $returnUrl,
            config('payment.fawry.secure_key'),
        ]));
    }

    /**
     * @param array<string, mixed> $payload
     */
    private function responseSignature(array $payload): string
    {
        $parts = [
            $payload['referenceNumber'] ?? '',
            $payload['merchantRefNumber'] ?? $payload['merchantRefNum'] ?? '',
            isset($payload['paymentAmount']) ? $this->formatAmount((float) $payload['paymentAmount']) : '',
            isset($payload['orderAmount']) ? $this->formatAmount((float) $payload['orderAmount']) : '',
            $payload['orderStatus'] ?? $payload['paymentStatus'] ?? '',
            $payload['paymentMethod'] ?? '',
            isset($payload['fawryFees']) ? $this->formatAmount((float) $payload['fawryFees']) : '',
            isset($payload['shippingFees']) ? $this->formatAmount((float) $payload['shippingFees']) : '',
            $payload['authNumber'] ?? '',
            $payload['customerMail'] ?? $payload['customerEmail'] ?? '',
            $payload['customerMobile'] ?? '',
            config('payment.fawry.secure_key'),
        ];

        return hash('sha256', implode('', $parts));
    }

    private function amountCents(float $amount): int
    {
        return (int) round($amount * 100);
    }
}
