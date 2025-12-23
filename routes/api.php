<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/pet-name', function (Request $request) {
    $catNames = [
        'Rosie',
        'Jim',
        'Skitz',
        'Taz',
        'Pebble',
        'Coco',
        'Pearl',
    ];

    $dogNames = [
        'Selby',
        'Alfie',
        'Winnie',
    ];

    $allPetNames = [
        ...$catNames,
        ...$dogNames,
    ];

    return response()->json([
        'name' => $allPetNames[array_rand($allPetNames)],
    ]);
});

Route::put('/pet-name', function (Request $request) {
    $request->validate([
        'name' => 'required',
    ]);
    // This route only exists so we can demonstrate OpenAPI Specification validator
    // working, so does not need any implementation.
});
