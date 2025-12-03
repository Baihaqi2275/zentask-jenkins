<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\TranscriptParser;
use Illuminate\Http\JsonResponse;

class TranscriptController extends Controller
{
    public function parse(Request $request): JsonResponse
    {
        $request->validate(['transcript' => 'required|string']);
        $parsed = TranscriptParser::parse($request->input('transcript'));
        // return ISO strings for calendar
        return response()->json([
            'title' => $parsed['title'],
            'start' => $parsed['start'] ? $parsed['start']->toIso8601String() : null,
            'end' => $parsed['end'] ? $parsed['end']->toIso8601String() : null,
        ]);
    }
}
