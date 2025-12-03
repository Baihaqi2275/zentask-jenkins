<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\Jobs\TranscribeAudioJob;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    public function index()
    {
        return view('calendar');
    }

    public function events(Request $request)
    {
        $query = Event::query();

        if ($s = $request->query('search')) {
            $query->where(function ($q) use ($s) {
                $q->where('title', 'like', "%{$s}%")
                  ->orWhere('description', 'like', "%{$s}%");
            });
        }

        if ($cat = $request->query('category')) {
            $query->where('category_id', $cat);
        }

        if ($start = $request->query('start')) {
            $query->where('start_at', '>=', $start);
        }
        if ($end = $request->query('end')) {
            $query->where('start_at', '<=', $end);
        }

        $events = $query->get()->map(function ($e) {
            return [
                'id' => $e->id,
                'title' => $e->title,
                'start' => $e->start_at?->toIso8601String(),
                'end' => $e->end_at?->toIso8601String(),
                'allDay' => (bool)$e->all_day,
                'color' => $e->color ?? null,
                'extendedProps' => [
                    'description' => $e->description,
                    'category_id' => $e->category_id,
                ],
            ];
        });

        return response()->json($events);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'=>'required|string|max:255',
            'description'=>'nullable|string',
            'start'=>'required|date',
            'end'=>'nullable|date',
            'allDay'=>'nullable|boolean',
            'color'=>'nullable|string',
            'category_id'=>'nullable|exists:categories,id',
            'rrule'=>'nullable|string',
        ]);

        $event = Event::create([
            'title'=>$data['title'],
            'description'=>$data['description'] ?? null,
            'start_at'=>$data['start'],
            'end_at'=>$data['end'] ?? null,
            'all_day'=>$data['allDay'] ?? false,
            'color'=>$data['color'] ?? null,
            'category_id'=>$data['category_id'] ?? null,
            'rrule'=>$data['rrule'] ?? null,
            'is_recurring'=>!empty($data['rrule']),
        ]);

        return response()->json($event, 201);
    }

    public function update(Request $request, Event $event)
    {
        $data = $request->validate([
            'title'=>'sometimes|required|string|max:255',
            'description'=>'nullable|string',
            'start'=>'sometimes|required|date',
            'end'=>'nullable|date',
            'allDay'=>'nullable|boolean',
            'color'=>'nullable|string',
            'category_id'=>'nullable|exists:categories,id',
            'rrule'=>'nullable|string',
        ]);

        $event->update([
            'title'=>$data['title'] ?? $event->title,
            'description'=>$data['description'] ?? $event->description,
            'start_at'=>$data['start'] ?? $event->start_at,
            'end_at'=>$data['end'] ?? $event->end_at,
            'all_day'=>$data['allDay'] ?? $event->all_day,
            'color'=>$data['color'] ?? $event->color,
            'category_id'=>$data['category_id'] ?? $event->category_id,
            'rrule'=>$data['rrule'] ?? $event->rrule,
            'is_recurring'=>!empty($data['rrule']),
        ]);

        return response()->json($event);
    }

    // --- Improved destroy method (safe delete + remove audio file if present) ---
    public function destroy(Event $event)
    {
        try {
            // hapus file audio jika ada
            if (!empty($event->audio_path)) {
                $audioRel = ltrim($event->audio_path, '/');
                $audioFull = storage_path('app/' . $audioRel);

                if (file_exists($audioFull) && is_readable($audioFull)) {
                    try {
                        @unlink($audioFull);
                        Log::info("EventController::destroy - removed audio file {$audioFull} for event {$event->id}");
                    } catch (\Throwable $e) {
                        Log::warning("EventController::destroy - failed to unlink audio file {$audioFull}: " . $e->getMessage(), ['event'=>$event->id]);
                    }
                } else {
                    Log::info("EventController::destroy - audio file not found or unreadable: {$audioFull}", ['event'=>$event->id]);
                }
            }

            $event->delete();

            return response()->json(['deleted' => true]);
        } catch (\Throwable $e) {
            Log::error("EventController::destroy - exception deleting event {$event->id}: " . $e->getMessage());
            return response()->json(['deleted' => false, 'error' => 'Failed to delete event'], 500);
        }
    }

    public function exportIcs()
    {
        $events = Event::orderBy('start_at')->get();
        $lines = ["BEGIN:VCALENDAR","VERSION:2.0","PRODID:-//ZENTASK//EN"];
        foreach($events as $e){
            $lines[] = "BEGIN:VEVENT";
            $lines[] = "UID:zentask-{$e->id}";
            $lines[] = "DTSTAMP:".Carbon::now()->format('Ymd\THis\Z');
            if ($e->start_at) $lines[] = "DTSTART:".$e->start_at->format('Ymd\THis');
            if ($e->end_at) $lines[] = "DTEND:".$e->end_at->format('Ymd\THis');
            $lines[] = "SUMMARY:".str_replace(["\r","\n"], ['\\r','\\n'], $e->title);
            if ($e->description) $lines[] = "DESCRIPTION:".str_replace(["\r","\n"], ['\\r','\\n'], $e->description);
            $lines[] = "END:VEVENT";
        }
        $lines[] = "END:VCALENDAR";
        $content = implode("\r\n",$lines);
        return response($content,200)->header('Content-Type','text/calendar')->header('Content-Disposition','attachment; filename=zentask_export.ics');
    }

    public function import(Request $request)
    {
        $request->validate(['file'=>'required|file|mimes:txt,ics,ical|max:10240']);
        $path = $request->file('file')->store('imports');
        $content = Storage::get($path);
        preg_match_all('/BEGIN:VEVENT(.*?)END:VEVENT/s', $content, $blocks);
        $count = 0;
        foreach ($blocks[1] as $block) {
            preg_match('/SUMMARY:(.+)/', $block, $mTitle);
            preg_match('/DTSTART(?:;[^:]+)?:([0-9TZ-:+]+)/', $block, $mStart);
            preg_match('/DTEND(?:;[^:]+)?:([0-9TZ-:+]+)/', $block, $mEnd);
            preg_match('/DESCRIPTION:(.+)/s', $block, $mDesc);
            if (empty($mTitle[1]) || empty($mStart[1])) continue;
            $title = trim($mTitle[1]);
            try {
                $start = Carbon::parse(trim($mStart[1]));
            } catch (\Throwable $e) {
                try { $start = Carbon::createFromFormat('Ymd\THis', trim($mStart[1])); } catch (\Throwable $ee) { continue; }
            }
            $end = null;
            if (!empty($mEnd[1])) {
                try { $end = Carbon::parse(trim($mEnd[1])); } catch (\Throwable $e) {
                    try { $end = Carbon::createFromFormat('Ymd\THis', trim($mEnd[1])); } catch (\Throwable $ee) { $end = null; }
                }
            }
            $description = !empty($mDesc[1]) ? trim($mDesc[1]) : null;
            Event::create(['title'=>$title,'description'=>$description,'start_at'=>$start,'end_at'=>$end]);
            $count++;
        }
        return response()->json(['imported' => $count]);
    }

    public function transcribe(Request $request)
    {
        $request->validate(['audio'=>'required|file|mimes:mp3,wav,ogg,m4a,webm|max:15360']);
        $file = $request->file('audio');
        $filename = 'audio_' . time() . '_' . Str::random(8) . '.' . $file->getClientOriginalExtension();
        $path = $file->storeAs('audio', $filename);

        $eventId = $request->input('event_id');
        if (!$eventId) {
            $ev = Event::create([
                'title' => 'Voice note',
                'start_at' => now(),
                'all_day' => false,
                'audio_path' => $path,
            ]);
            $eventId = $ev->id;
        } else {
            $ev = Event::find($eventId);
            if ($ev) {
                $ev->audio_path = $path;
                $ev->save();
            }
        }

        TranscribeAudioJob::dispatch($eventId, $path);

        return response()->json([
            'ok' => true,
            'event_id' => $eventId,
            'path' => $path,
            'message' => 'Audio uploaded. Transcription will run in background.'
        ]);
    }

    public function transcriptStatus(Event $event)
    {
        return response()->json([
            'id' => $event->id,
            'audio_path' => $event->audio_path,
            'transcript' => $event->transcript,
            'status' => $event->transcript ? 'done' : 'pending',
        ]);
    }

    public function realtimeTranscript(Request $request)
    {
        $data = $request->validate([
            'transcript' => 'required|string|max:65500',
            'lang' => 'nullable|string|max:10',
            'auto_create' => 'nullable|boolean',
        ]);

        $text = trim($data['transcript']);
        if ($text === '') {
            return response()->json(['ok' => false, 'message' => 'Empty transcript'], 422);
        }

        $title = mb_substr(preg_replace('/\s+/', ' ', $text), 0, 120);
        $start = now();
        $end = now()->addHour();

        $event = Event::create([
            'title' => $title ?: 'Voice note',
            'description' => $text,
            'start_at' => $start,
            'end_at' => $end,
            'all_day' => false,
            'color' => '#0ea5e9'
        ]);

        return response()->json([
            'ok' => true,
            'event_id' => $event->id,
            'message' => 'Event created from live transcript',
            'event' => $event
        ], 201);
    }
}
