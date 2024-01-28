<?php

namespace App\Services;

use App\Http\Requests\PaymentRequest;
use Illuminate\Support\Facades\Http;

class PaymentService
{
    public function requestHashValidator(PaymentRequest $paymentRequest): array
    {
        $callBackFailUrl = $paymentRequest->callback_fail_url;
        $callbackSuccesUrl = $paymentRequest->callback_success_url;
        $price = $paymentRequest->price;
        $hash = $paymentRequest->hash;
        $salt = config('custom.salt');

        $cHash = sha1(sprintf(
            '%s%s%s%s',
            $salt,
            $callBackFailUrl,
            $callbackSuccesUrl,
            $price,
        ));

        if ($hash !== $cHash) {
            return ['error' => 'Hash not correct', 'status' => 403];
        }

        return ['message' => 'Hash Correct', 'status' => 200];
    }

    public function getCallbackHash(PaymentRequest $paymentRequest): string
    {
        $callBackFailUrl = $paymentRequest->callback_fail_url;
        $callbackSuccesUrl = $paymentRequest->callback_success_url;
        $price = $paymentRequest->price;
        $salt = config('custom.salt');

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
            return ['message' => 'Payment Callback Successfull', 'status' => $response->status()];
        }

        return ['error' => 'Payment Callback failed -> ' . $response->json()['message'], 'status' => $response->status()];
    }
}
