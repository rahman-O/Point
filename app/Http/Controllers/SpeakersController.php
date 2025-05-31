<?php

namespace App\Http\Controllers;

use App\Models\Speakers;
use Illuminate\Http\Request;

class SpeakersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $year = $request->input('year');

    // Get all unique years from the speakers table
    $years = Speakers::select('year')->distinct()->orderBy('year', 'desc')->pluck('year');

    $query = Speakers::query();

    if ($year) {
        $query->where('year', $year);
    }

    $speakers = $query->orderBy('created_at', 'desc')->paginate(10);

    return response()->json([
        'speakers' => $speakers,
        'years' => $years,
    ]);
}
public function getTopSpeakers()
    {
         $speakers = Speakers::orderBy('created_at', 'desc')
        ->limit(30)
        ->get();


    return response()->json([
        'speakers' => $speakers,
    ]);
    }
// public function getTopSpeakers()
// {
//     dd('Inside getTopSpeakers()');
//     // $speakers = Speakers::orderBy('created_at', 'desc')
//     //     ->limit(30)
//     //     ->get();

//     // return response()->json([
//     //     'speakers' => $speakers,
//     // ]);
// }

    public function show($id)
    {
        $speakers = Speakers::find($id);

        if (!$speakers) {
            return response()->json(['message' => 'Not Found'], 404);
        }

        // Return the raw model without wrapping it in a resource
        return response()->json($speakers);
    }

    public function allSpeakers()
    {
        $speakers = Speakers::all(); // Fetch all speakers

        return response()->json($speakers);
    }
public function speakersByYear(Request $request)
{
    $year = $request->input('year');
    $search = $request->input('search');

    if (!$year) {
        return response()->json(['message' => 'Year is required'], 400);
    }

    $query = Speakers::where('year', $year);

    if ($search) {
        $query->where(function ($q) use ($search) {
            $q->where('name_en', 'like', '%' . $search . '%')
              ->orWhere('name_ar', 'like', '%' . $search . '%');
        });
    }

    $speakers = $query->orderBy('created_at', 'desc')
        ->limit(20)
        ->get(['id', 'name_en']);

    return response()->json($speakers);
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
    public function edit(Speakers $speakers)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Speakers $speakers)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Speakers $speakers)
    {
        //
    }
}
