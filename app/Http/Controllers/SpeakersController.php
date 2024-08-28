<?php

namespace App\Http\Controllers;

use App\Models\Speakers;
use Illuminate\Http\Request;

class SpeakersController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Speakers::paginate(10);
    }

//    public function allSpeakers()
//    {
//        return Speakers::all();
//    }

    public function allSpeakers()
    {
        $speakers = Speakers::all(); // Fetch all speakers

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
     * Display the specified resource.
     */
    public function show(Speakers $speakers)
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
