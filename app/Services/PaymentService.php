<?php

namespace App\Services;

use App\Http\Requests\PaymentRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentService
{
    public function requestHashValidator(PaymentRequest $paymentRequest): array
    {
        $callBackFailUrl = $paymentRequest->callback_fail_url;
        $callbackSuccesUrl = $paymentRequest->callback_success_url;
        $price = $paymentRequest->price;
        $hash = $paymentRequest->hash;
        $salt = config('app.salt');

        $cHash = sha1(sprintf(
            '%s%s%s%s',
            $salt,
            $callBackFailUrl,
            $callbackSuccesUrl,
            $price,
        ));

        if ($hash !== $cHash) {
            Log::alert('This hash not correct', ['message' =>  $hash]);
            return ['error' => 'Hash not correct', 'status' => 403];
        }

        Log::info('This hash correct');
        return ['message' => 'Hash Correct', 'status' => 200];
    }

    public function getCallbackHash(PaymentRequest $paymentRequest): string
    {
        $callBackFailUrl = $paymentRequest->callback_fail_url;
        $callbackSuccesUrl = $paymentRequest->callback_success_url;
        $price = $paymentRequest->price;
        $salt = config('app.salt');

        return sha1(sprintf(
            '%s%s%s%s',
            $price,
            $callbackSuccesUrl,
            $callBackFailUrl,
            $salt
        ));
    }


    public function callback(string $callbackUrl, string $hash): array
    {

        $response = Http::post($callbackUrl, [
            'hash' => $hash,
        ]);

        if ($response->successful()) {
            Log::info('Payment Callback Successfull');
            return ['message' => 'Payment Callback Successfull', 'status' => $response->status()];

        }
        Log::error('Payment Callback failed', ['message' => $response->json()['message']]);
        return ['error' => 'Payment Callback failed -> ' . $response->json()['message'], 'status' => $response->status()];
    }
}
