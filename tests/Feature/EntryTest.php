<?php

namespace Tests\Feature;

use App\Http\Requests\PaymentRequest;
use App\Models\User;
use App\Services\EntryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EntryTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function testCreateEntrySuccess()
    {
        // Arrange
        $user = User::find(1);

        $user = [
            "id" => $user->id,
            "user_id" => $user->id,
            "wallet" => $user->wallet
        ];

        
        $price = 55.33; // example price

        // Mock a PaymentRequest with expected data
        $paymentRequest = new PaymentRequest();
        $paymentRequest->merge([
            'price' => $price
        ]);

        // Act
        $service = new EntryService();
        $response = $service->createEntry($paymentRequest);

        // Assert
        $this->assertEquals(200, $response['status']);
        $this->assertEquals('Login successful', $response['message']);
        $this->assertDatabaseHas('users', [
            'id' => $user['id'],
            'wallet' => $user['wallet'] - $price // wallet should be decreased by the price
        ]);
        $this->assertDatabaseHas('entries', [
            'user_id' => $user['id'],
        ]);
        $this->assertDatabaseHas('transactions', [
            'user_id' => $user['id'],
            'amount' => $price
        ]);
    }
}
