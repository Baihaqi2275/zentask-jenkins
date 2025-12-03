<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class AIParserController extends Controller
{
    public function parseWithOpenAI(Request $request): JsonResponse
    {
        $request->validate(['transcript' => 'required|string']);
        $transcript = $request->input('transcript');

        $apiKey = env('OPENAI_API_KEY');
        if (!$apiKey) {
            return response()->json(['error' => 'OpenAI key not configured'], 500);
        }

        // Prompt: ask model to return JSON only with fields
        $system = "You are a calendar assistant. Extract structured event info from user's transcript. Respond with valid JSON only, keys: title, start, end, timezone (optional), rrule (optional e.g. FREQ=WEEKLY;INTERVAL=1), category (optional). Use ISO 8601 for datetimes or null.";
        $user = "Transcript: \"{$transcript}\". Return JSON only.";

        try {
            $resp = Http::withToken($apiKey)->post('https://api.openai.com/v1/chat/completions', [
                'model' => 'gpt-4o-mini', // replace with an available chat model; use one appropriate for your account
                'messages' => [
                    ['role' => 'system', 'content' => $system],
                    ['role' => 'user', 'content' => $user],
                ],
                'temperature' => 0,
                'max_tokens' => 400,
            ]);

            if (!$resp->successful()) {
                return response()->json(['error' => 'OpenAI parse failed', 'detail' => $resp->body()], 500);
            }

            $data = $resp->json();
            $text = $data['choices'][0]['message']['content'] ?? null;
            if (!$text) {
                return response()->json(['error' => 'No response text'], 500);
            }

            // try decode JSON
            $json = json_decode($text, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                // attempt to extract JSON substring
                if (preg_match('/\{.*\}/s', $text, $m)) {
                    $json = json_decode($m[0], true);
                }
            }

            if (!$json || !is_array($json)) {
                return response()->json(['error' => 'Could not parse JSON from model', 'raw' => $text], 500);
            }

            // Validate and coerce dates if possible
            if (!empty($json['start'])) {
                try {
                    $json['start'] = Carbon::parse($json['start'])->toIso8601String();
                } catch (\Throwable $e) {
                    $json['start'] = null;
                }
            }
            if (!empty($json['end'])) {
                try {
                    $json['end'] = Carbon::parse($json['end'])->toIso8601String();
                } catch (\Throwable $e) {
                    $json['end'] = null;
                }
            }

            return response()->json(['result' => $json, 'raw' => $text]);
        } catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
