<?php

namespace Tests\Feature;

use App\Http\Requests\PaymentRequest;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class PaymentServiceTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testRequestHashValidator()
    {
        $request = PaymentRequest::create('/dummy-url', 'POST', [
            'callback_fail_url' => "https://case.altpay.dev/fail",
            'callback_success_url' => "https://case.altpay.dev/success",
            'price' => "55.33",
            'hash' => "2918f946ce80bd37e7dbf4ade4888df9d281de0d"
        ]);

        config(['app.salt' => 'case2023']);

        $paymentService = new PaymentService();

        $response = $paymentService->requestHashValidator($request);

        $this->assertEquals(['message' => 'Hash Correct', 'status' => 200], $response);
    }
    public function testGetCallbackHash()
    {
        $request =  PaymentRequest::create('/dummy-url', 'POST', [
            'callback_fail_url' => "https://case.altpay.dev/fail",
            'callback_success_url' => "https://case.altpay.dev/success",
            'price' => "55.33",

        ]);

        config(['app.salt' => 'case2023']);

        $paymentService = new PaymentService();

        $hash = $paymentService->getCallbackHash($request);

        $expectedHash = sha1(sprintf(
            '%s%s%s%s',
            "55.33",
            "https://case.altpay.dev/success",
            "https://case.altpay.dev/fail",
            'case2023'
        ));

        $this->assertEquals($expectedHash, $hash);
    }
    public function testCallbackSuccess()
    {
        Http::fake([
            '*' => Http::response(['message' => 'Success'], 200),
        ]);

        $paymentService = new PaymentService();
        $callbackUrl = 'https://case.altpay.dev/success';
        $hash = 'case2023';

        $response = $paymentService->callback($callbackUrl, $hash);

        $this->assertEquals(['message' => 'Payment Callback Successfull', 'status' => 200], $response);
    }

    public function testCallbackFailure()
    {

        Http::fake([
            '*' => Http::response(['message' => 'Error occurred'], 400),
        ]);

        $paymentService = new PaymentService();
        $callbackUrl = 'https://case.altpay.dev/fail';
        $hash = 'case2023';

        $response = $paymentService->callback($callbackUrl, $hash);

        $this->assertEquals(['error' => 'Payment Callback failed -> Error occurred', 'status' => 400], $response);
    }
}
