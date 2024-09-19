<?php

namespace App\Http\Controllers;

use App\Http\Resources\api\Programs\ProgramsResource;
use App\Http\Resources\api\Programs\specifiedProgramResource;
use App\Models\Programs;
use Illuminate\Http\Request;

class ProgramsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $programs = Programs::with('sessionsProgram')->get();
        return new ProgramsResource($programs);
    }

    /**
     * Display the specified resource.
     */
    public function show( $id)
    {
        $programs = Programs::with('sessionsProgram')->find($id);
        if (!$programs) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        return new specifiedProgramResource($programs);
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
    public function edit(Programs $programs)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Programs $programs)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Programs $programs)
    {
        //
    }
}
