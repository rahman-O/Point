<?php

namespace App\Http\Controllers;

use App\Http\Resources\api\Programs\ProgramsResource;
use App\Http\Resources\api\Programs\specifiedProgramResource;
use App\Models\Programs;
use Illuminate\Http\Request;
use Carbon\Carbon;
class ProgramsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {$programs = Programs::with(['sessionsProgram.speakers'])->get();

        return response()->json($programs);
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

    public function currentYearProgram()
    {
        $currentYear = Carbon::now()->year;

        // Prefer the current year's program; otherwise fall back to the most recent
        // program available, so the page still shows the latest agenda even if it is
        // from a past year.
        $program = Programs::with(['sessionsProgram.speakers'])
            ->where('year', $currentYear)
            ->first()
            ?? Programs::with(['sessionsProgram.speakers'])
                ->orderByRaw('CAST(year AS UNSIGNED) DESC')
                ->first();

        if (!$program) {
            return response()->json(['message' => 'No program found'], 404);
        }

        return response()->json($program);
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
