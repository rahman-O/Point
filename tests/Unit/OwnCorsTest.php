<?php

use App\Http\Middleware\OwnCors;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

it('adds the CORS headers to streamed responses too', function () {
    // Livewire serves temporary upload previews as Symfony streamed responses, which have
    // no header() helper.
    $response = (new OwnCors)->handle(Request::create('/preview'), fn () => new StreamedResponse(fn () => print ('x')));

    expect($response)->toBeInstanceOf(StreamedResponse::class)
        ->and($response->headers->get('Access-Control-Allow-Methods'))->toBe('POST, GET, OPTIONS, PUT, DELETE')
        ->and($response->headers->get('Access-Control-Allow-Headers'))->toBe('Content-Type, X-Auth-Token, Origin, Authorization');
});
