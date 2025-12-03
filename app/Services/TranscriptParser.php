<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Str;

class TranscriptParser
{
    /**
     * Very simple heuristic parser.
     * Returns array: ['title'=>..., 'start'=>Carbon|null, 'end'=>Carbon|null]
     */
    public static function parse(string $text): array
    {
        $text = trim($text);
        $lower = mb_strtolower($text);

        $result = [
            'title' => Str::limit($text, 120),
            'start' => null,
            'end' => null,
        ];

        // Patterns to find date/time like "tomorrow at 10", "on 12 Aug at 9:30", "next monday 14:00"
        // Check relative words
        if (preg_match('/\btomorrow\b/', $lower)) {
            $date = Carbon::tomorrow();
        } elseif (preg_match('/\b(today|this day)\b/', $lower)) {
            $date = Carbon::today();
        } elseif (preg_match('/next\s+([a-z]+)/', $lower, $m)) {
            try {
                $date = Carbon::parse('next ' . $m[1]);
            } catch (\Throwable $e) {
                $date = null;
            }
        } else {
            $date = null;
        }

        // Time patterns hh[:mm] or at hh
        if (preg_match('/\b(?:at\s*)?([01]?\d|2[0-3])(?::([0-5]\d))\b/', $lower, $mt)) {
            $hour = intval($mt[1]);
            $minute = isset($mt[2]) ? intval($mt[2]) : 0;
            if ($date) {
                $result['start'] = $date->copy()->setTime($hour, $minute);
            } else {
                // if no date, assume today or tomorrow depending on keyword
                $result['start'] = Carbon::today()->setTime($hour, $minute);
            }
        } elseif (preg_match('/\b(?:at\s*)?([01]?\d|2[0-3])\s*(am|pm)\b/', $lower, $mt2)) {
            $hour = intval($mt2[1]);
            if (str_contains($mt2[2], 'pm') && $hour < 12) $hour += 12;
            if ($date) {
                $result['start'] = $date->copy()->setTime($hour, 0);
            } else {
                $result['start'] = Carbon::today()->setTime($hour, 0);
            }
        }

        // Range e.g. "from 9 to 11" or "9-11"
        if (preg_match('/\b(?:from\s*)?([01]?\d|2[0-3])(?::([0-5]\d))?\s*(?:to|\-|\u2013)\s*([01]?\d|2[0-3])(?::([0-5]\d))?\b/', $lower, $m)) {
            $h1 = intval($m[1]);
            $min1 = isset($m[2]) && $m[2] !== '' ? intval($m[2]) : 0;
            $h2 = intval($m[3]);
            $min2 = isset($m[4]) && $m[4] !== '' ? intval($m[4]) : 0;
            $base = $result['start'] ?? Carbon::today();
            $result['start'] = $base->copy()->setTime($h1, $min1);
            $result['end'] = $base->copy()->setTime($h2, $min2);
        }

        // If start found and no end, set default duration 1 hour
        if ($result['start'] && !$result['end']) {
            $result['end'] = $result['start']->copy()->addHour();
        }

        // Fallback: try parse ISO-like dates in text
        if (!$result['start']) {
            try {
                $dt = Carbon::parse($text);
                if ($dt) {
                    $result['start'] = $dt;
                    $result['end'] = $dt->copy()->addHour();
                }
            } catch (\Throwable $e) {
                // ignore
            }
        }

        return $result;
    }
}
