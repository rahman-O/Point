<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;

use App\Http\Resources\api\News\NewsResource;
use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return News::paginate(10);
    }
    public function latestNews()
    {
        $news = News::orderBy('created_at', 'desc')->take(10)->get();

        return response()->json($news);
    }
    public function getByEventTime(Request $request)
{
    $year = $request->input('year');

    $years = News::select(DB::raw('YEAR(event_time) as year'))
        ->distinct()
        ->orderBy('year', 'desc')
        ->pluck('year');

    $query = News::query();

    if ($year) {
        $query->whereYear('event_time', $year);
    }

    $news = $query->orderBy('created_at', 'desc')->paginate(30);

    return response()->json([
        'news' => $news,
        'years' => $years,
    ]);
}


    public function newsWithFilter(Request $request)
{
    $query = News::query();

    $news = $query->orderBy('created_at', 'desc')->paginate(10);

    return response()->json($news);
}

    public function allNews()
    {
        $news = News::all();

        return response()->json($news);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $news = News::find($id);

        if (!$news) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        // Return the raw model without wrapping it in a resource
        return response()->json($news);
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }


    /**
     * Show the form for editing the specified resource.
     */
    public function edit(News $news)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, News $news)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(News $news)
    {
        //
    }
}
