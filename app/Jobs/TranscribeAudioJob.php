<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Event;
use Throwable;

class TranscribeAudioJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int|string $eventId;
    public string $audioPath;

    /**
     * Create a new job instance.
     *
     * @param int|string $eventId  The Event model id to attach transcript to
     * @param string $audioPath    Storage path (relative) e.g. "audio/audio_123.webm"
     */
    public function __construct($eventId, string $audioPath)
    {
        $this->eventId = $eventId;
        $this->audioPath = $audioPath;
        $this->tries = 3;
        $this->timeout = 300; // allow more time for conversion + upload
    }

    /**
     * Execute the job.
     *
     * - Converts .webm -> .mp3 using ffmpeg when needed
     * - Calls OpenAI transcription endpoint
     * - Falls back to local whisper CLI if configured
     * - Saves transcript to Event->transcript
     */
    public function handle(): void
    {
        $event = Event::find($this->eventId);
        if (!$event) {
            Log::warning("TranscribeAudioJob: Event not found, id={$this->eventId}");
            return;
        }

        if (empty($this->audioPath)) {
            Log::error("TranscribeAudioJob: audio_path is empty for event {$this->eventId}");
            $event->transcript = null;
            $event->save();
            return;
        }

        $localPath = storage_path('app/' . ltrim($this->audioPath, '/'));

        if (!file_exists($localPath) || !is_readable($localPath)) {
            Log::error("TranscribeAudioJob: audio file missing or unreadable at {$localPath} for event {$this->eventId}");
            $event->transcript = null;
            $event->save();
            return;
        }

        // Prepare a converted audio file path (if needed)
        $ext = strtolower(pathinfo($localPath, PATHINFO_EXTENSION));
        $usePath = $localPath; // path to send to API (maybe converted)
        $convertedPath = null;

        // If .webm (or other less-supported ext) convert to mp3
        $needConvert = in_array($ext, ['webm', 'weba', 'opus']);
        if ($needConvert) {
            $tmpDir = storage_path('app/tmp');
            if (!is_dir($tmpDir)) @mkdir($tmpDir, 0755, true);

            $convertedPath = $tmpDir . DIRECTORY_SEPARATOR . 'conv_' . $this->eventId . '_' . time() . '.mp3';
            // build ffmpeg command - ensure ffmpeg exists in PATH
            // -y overwrite, -i input, output as mp3
            $cmd = sprintf('ffmpeg -y -i %s -ar 16000 -ac 1 %s 2>&1',
                escapeshellarg($localPath),
                escapeshellarg($convertedPath)
            );

            exec($cmd, $outLines, $exitCode);
            if ($exitCode !== 0 || !file_exists($convertedPath)) {
                Log::error("TranscribeAudioJob: ffmpeg convert failed for {$localPath}, exit={$exitCode}, output=" . implode("\n", $outLines), ['event' => $this->eventId]);
                // don't throw here; we'll try fallback later. Mark transcript null for now.
                $convertedPath = null;
            } else {
                $usePath = $convertedPath;
                Log::info("TranscribeAudioJob: converted {$localPath} -> {$convertedPath} for event {$this->eventId}");
            }
        }

        $transcript = null;
        $apiKey = env('OPENAI_API_KEY');

        // Option A: Use OpenAI Whisper API if API key available
        if ($apiKey && file_exists($usePath) && is_readable($usePath)) {
            try {
                // attach file and call transcription endpoint
                $response = Http::withToken($apiKey)
                    ->timeout(300)
                    ->attach('file', fopen($usePath, 'r'), basename($usePath))
                    ->post('https://api.openai.com/v1/audio/transcriptions', [
                        'model' => 'whisper-1',
                    ]);

                if ($response->successful()) {
                    $body = (string)$response->body();
                    $json = json_decode($body, true);
                    if (is_array($json) && isset($json['text'])) {
                        $transcript = trim($json['text']);
                    } elseif (is_string($body) && trim($body) !== '') {
                        $transcript = trim($body);
                    }
                    Log::info('TranscribeAudioJob: OpenAI transcription successful', ['event' => $this->eventId, 'len' => strlen($transcript ?? '')]);
                } else {
                    Log::error('TranscribeAudioJob: OpenAI response error', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'event' => $this->eventId,
                    ]);
                }
            } catch (Throwable $e) {
                Log::error('TranscribeAudioJob: OpenAI request failed - ' . $e->getMessage(), [
                    'event' => $this->eventId,
                ]);
            }
        } else {
            if (!$apiKey) {
                Log::warning('TranscribeAudioJob: OPENAI_API_KEY not set, skipping OpenAI transcription', ['event' => $this->eventId]);
            } else {
                Log::warning('TranscribeAudioJob: usePath not readable for OpenAI: ' . $usePath, ['event' => $this->eventId]);
            }
        }

        // Option B: Fallback to local whisper CLI if transcript still null
        if (!$transcript) {
            try {
                $outDir = storage_path('app/transcripts');
                if (!is_dir($outDir)) {
                    @mkdir($outDir, 0755, true);
                }

                // If converted path present use it else try original
                $sourceForWhisper = $usePath;
                if (!$sourceForWhisper || !file_exists($sourceForWhisper)) {
                    $sourceForWhisper = $localPath;
                }

                // Make sure whisper exists in PATH if you want this fallback
                $cmd = 'whisper ' . escapeshellarg($sourceForWhisper) . ' --model small --output_format txt --output_dir ' . escapeshellarg($outDir) . ' 2>&1';
                exec($cmd, $outputLines, $exitCode);

                if ($exitCode === 0) {
                    $files = glob(rtrim($outDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . '*.txt');
                    if ($files && count($files) > 0) {
                        usort($files, function($a, $b) {
                            return filemtime($b) - filemtime($a);
                        });
                        $txtFile = $files[0];
                        $transcript = trim(file_get_contents($txtFile));
                        Log::info('TranscribeAudioJob: Whisper CLI produced transcript', ['event' => $this->eventId, 'file' => $txtFile]);
                    } else {
                        Log::warning('TranscribeAudioJob: Whisper CLI succeeded but no output txt found', ['event' => $this->eventId, 'out' => $outputLines]);
                    }
                } else {
                    Log::error("TranscribeAudioJob: Whisper CLI failed exit={$exitCode} output=" . implode("\n", $outputLines), ['event' => $this->eventId]);
                }
            } catch (Throwable $e) {
                Log::error('TranscribeAudioJob: Whisper CLI exception - ' . $e->getMessage(), ['event' => $this->eventId]);
            }
        }

        // Save transcript (may be null); also ensure event->audio_path set
        try {
            $relPath = ltrim($this->audioPath, '/');
            $event->audio_path = $relPath;
            $event->transcript = $transcript ? mb_substr($transcript, 0, 65500) : null;
            $event->save();

            Log::info("TranscribeAudioJob: saved transcript for event {$this->eventId}", [
                'has_transcript' => $transcript ? true : false
            ]);
        } catch (Throwable $e) {
            Log::error('TranscribeAudioJob: failed to save transcript - ' . $e->getMessage(), ['event' => $this->eventId]);
            // If we cannot save, make job fail so we see stacktrace in failed_jobs
            throw $e;
        } finally {
            // clean temporary converted file if exists
            if (!empty($convertedPath) && file_exists($convertedPath)) {
                @unlink($convertedPath);
            }
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(Throwable $exception): void
    {
        Log::error('TranscribeAudioJob failed: ' . $exception->getMessage(), [
            'event' => $this->eventId,
            'audio' => $this->audioPath,
        ]);

        try {
            $event = Event::find($this->eventId);
            if ($event) {
                $event->transcript = null;
                $event->save();
            }
        } catch (Throwable $e) {
            Log::error('TranscribeAudioJob failed handler error: ' . $e->getMessage());
        }
    }
}
