<?php

namespace App\Http\Controllers;

use App\Services\EntryService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class EntriesController extends Controller
{
    protected $entryService;

    public function __construct(EntryService $entryService)
    {
        $this->entryService = $entryService;
    }

    public function store(Request $request): JsonResponse
    {
        $result = $this->entryService->createEntry($request->input('user_id'));

        if (isset($result['error'])) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json($result, $result['status']);
    }
    public function count(Request $request)
    {
        $result = $this->entryService->getDailyEntryCount($request->query('user_id'));

        if (array_key_exists('error', $result)) {
            return response()->json(['message' => $result['error']], $result['status']);
        }

        return response()->json(['count' => $result['count']], $result['status']);
    }
}
