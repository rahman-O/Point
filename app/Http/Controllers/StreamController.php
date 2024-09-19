<?php

namespace App\Http\Controllers;

use App\Http\Resources\api\Stream\SpecifiedStreamResource;
use App\Models\Stream;
use Illuminate\Http\Request;

class StreamController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Stream::paginate(10);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $stream = Stream::find($id);
        if (!$stream) {
            return response()->json(['message' => 'Not Found'], 404);
        }
        return new SpecifiedStreamResource($stream);
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
    public function edit(Stream $stream)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Stream $stream)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Stream $stream)
    {
        //
    }
}
