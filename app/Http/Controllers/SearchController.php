<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Search\SearchService;
use Illuminate\Http\JsonResponse;

class SearchController extends Controller
{
    public function __invoke(SearchRequest $request, SearchService $search): JsonResponse
    {
        $data = $request->validated();

        return response()->json($search->search(
            trim($data['q']),
            $data['type'] ?? null,
            $data['lang'] ?? 'ar',
            (int) ($data['page'] ?? 1),
            (int) ($data['limit'] ?? 20),
        ));
    }
}
