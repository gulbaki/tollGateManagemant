<?php

namespace App\Services;

use App\Http\Requests\PaymentRequest;
use App\Models\Entries;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EntryService
{
    private const DAILY_LIMIT = 2;
    private const TOTAL_LIMIT = 50;

    public function createEntry(PaymentRequest $paymentRequest): array
    {
        $userId =  1; //$paymentRequest->userId;
        $price = $paymentRequest->price;

        $response =  DB::transaction(function () use ($userId, $price) {
            // Obtain a lock on the user record
            $user = User::lockForUpdate()->find($userId);

          
            if (!$user) {
                return ['error' => 'User not found', 'status' => 404];
            }

            $todayEntries = self::getDailyEntryCount($userId);

            if (isset($todayEntries['error'])) {
                return $todayEntries;
            }

            $totalEntriesToday = self::getTotalEntriesForToday();

            if (isset($totalEntriesToday['error'])) {
                return $totalEntriesToday;
            }

            if ($user->wallet < $price) {
                return ['error' => "insufficient funds", 'status' => 422];
            }


            $transaction = new Transactions([
                'user_id' => $userId,
                'amount'  => $price,
                // Populate other fields as necessary
            ]);
            $transaction->save();
            $user->wallet -= $price;
            $user->save();
            $entry = new Entries();
            $entry->user_id = $userId;
            $entry->save();

            return ['message' => 'Login successful', 'entry' => $entry, 'status' => 200];
        });

       
        return $response;
    }
    public function getDailyEntryCount(int $userId): array
    {
        // Find the user's entry record
        $userEntry = User::find($userId);

        // Check if the user exists, if not, return an error response
        if (!$userEntry) {
            return ['error' => 'User not found', 'status' => 404];
        }

        // Retrieve the count of entries for the user created today
        $count = Entries::where('user_id', $userId)
            ->whereDate('created_at', now()->today())
            ->count();

        if ($count >= self::DAILY_LIMIT) {
            return ['error' => 'You have reached the daily login limit', 'status' => 403];
        }

        // Return the count and a success status code
        return ['count' => $count, 'status' => 200];
    }
    public function getTotalEntriesForToday()
    {
        $count = Entries::whereDate('created_at', today())->count();

        if ($count >= self::TOTAL_LIMIT) {
            return ['error' => 'The cafeteria"s total entry limit has been exceeded', 'status' => 403];
        }

        return ['count' => $count, 'status' => 200];
    }
}
