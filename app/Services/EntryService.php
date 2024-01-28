<?php

namespace App\Services;

use App\Http\Requests\PaymentRequest;
use App\Models\Entries;
use App\Models\Transactions;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EntryService
{
    private const DAILY_LIMIT = 2;
    private const TOTAL_LIMIT = 50;

    public function createEntry(PaymentRequest $paymentRequest): array
    {
        $userId =  $paymentRequest->user_id;
        $price = $paymentRequest->price;

        $response =  DB::transaction(function () use ($userId, $price) {

            try {

                $user = User::lockForUpdate()->find($userId);

                if (!$user) {
                    return ['error' => 'User not found', 'status' => 404];
                }

                $todayEntries = self::getDailyEntryCount($user->id);

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
                ]);
                $transaction->save();
                $user->wallet -= $price;
                $user->save();
                $entry = new Entries();
                $entry->user_id = $userId;
                $entry->save();

                Log::info('Entry creation successful'  . $userId);

                return ['message' => 'Entry creation successful', 'entry' => $entry, 'status' => 200];

            } catch (\Exception $e) {
                Log::error('Entry creation failed: ' . $e->getMessage());

                return ['error' => 'Transaction failed', 'status' => 500];
            }
        });


        return $response;
    }
    public function getDailyEntryCount(int $userId): array
    {
        $userEntry = User::find($userId);

        if (!$userEntry) {
            Log::alert('User not found');
            return ['error' => 'User not found', 'status' => 404];
        }

        $count = Entries::where('user_id', $userId)
            ->whereDate('created_at', now()->today())
            ->count();

        if ($count >= self::DAILY_LIMIT) {
            log::alert('You have reached the daily login limit');
            return ['error' => 'You have reached the daily login limit', 'status' => 403];
        }

        return ['count' => $count, 'status' => 200];
    }
    public function getTotalEntriesForToday()
    {
        $count = Entries::whereDate('created_at', today())->count();

        if ($count >= self::TOTAL_LIMIT) {
            log::alert('The cafeteria"s total entry limit has been exceeded');
            return ['error' => 'The cafeteria"s total entry limit has been exceeded', 'status' => 403];
        }

        return ['count' => $count, 'status' => 200];
    }
}
