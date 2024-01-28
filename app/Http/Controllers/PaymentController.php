<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentRequest;
use App\Services\EntryService;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    protected $paymentService;
    protected $entriesService;

    public function __construct(PaymentService $paymentService, EntryService $entriesService)
    {
        $this->paymentService = $paymentService;
        $this->entriesService = $entriesService;
    }
    public function index(PaymentRequest $paymentRequest)
    {
        $callbackSuccesUrl = $paymentRequest->callback_success_url;
        $callbackFailUrl = $paymentRequest->callback_fail_url;

        $hashCheck = $this->paymentService->requestHashValidator($paymentRequest);

        if (isset($hashCheck['error'])) {
            return $hashCheck;
        }

        $hash = $this->paymentService->getCallbackHash($paymentRequest);

        $entriesResponse = $this->entriesService->createEntry($paymentRequest);

        if (isset($entriesResponse['error'])) {
            $callbackResponse = $this->paymentService->callback($callbackFailUrl, $hash);
            if (isset($callbackResponse['error'])) {
                return response()->json($callbackResponse);
            }
            return response()->json(['message' => $entriesResponse['error']], $entriesResponse['status']);
        }

        return $this->paymentService->callback($callbackSuccesUrl, $hash);
    }
}
