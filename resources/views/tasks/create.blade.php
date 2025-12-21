@extends('layouts.dashboard-layout')

@section('content')
@php
    $voiceLocked = auth()->check() && !auth()->user()->canUseVoiceRecorder();
@endphp

<div class="space-y-6">
    <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-3xl lg:text-4xl font-bold text-white">Create Task</h1>
            <p class="text-blue-100/70 mt-1">Buat task baru dengan detail lengkap.</p>
        </div>
        <a href="{{ route('dashboard') }}"
            class="inline-flex items-center justify-center px-4 py-2 rounded-xl bg-white/10 hover:bg-white/15 border border-white/10 text-blue-100 font-semibold transition">
            Kembali ke Dashboard
        </a>
    </div>

    @if ($errors->any())
        <div class="p-4 rounded-xl bg-red-500/10 border border-red-400/20 text-red-200 text-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="glass-card rounded-2xl p-6">
                <form method="POST" action="{{ route('tasks.store') }}" class="space-y-6">
                    @csrf

                    <div>
                        <h2 class="text-lg font-semibold text-white">Detail Task</h2>
                        <p class="text-sm text-blue-100/60">Informasi utama untuk task baru.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-blue-100/70">Nama Task</label>
                            <input name="title" type="text" required value="{{ old('title') }}"
                                class="mt-1 w-full rounded-xl bg-white/5 border border-white/10 text-white placeholder:text-blue-100/40 focus:ring-0 focus:border-blue-300/40"
                                placeholder="Contoh: Kerjakan laporan DWBI">
                        </div>

                        <div>
                            <label class="block text-sm text-blue-100/70">Category</label>
                            <select name="category" required
                                class="mt-1 w-full rounded-xl bg-white/5 border border-white/10 text-white focus:ring-0 focus:border-blue-300/40 zentask-select">
                                <option value="" disabled {{ old('category') ? '' : 'selected' }}>Pilih kategori</option>
                                <option value="Work" {{ old('category') === 'Work' ? 'selected' : '' }}>Work</option>
                                <option value="Personal" {{ old('category') === 'Personal' ? 'selected' : '' }}>Personal</option>
                                <option value="Study" {{ old('category') === 'Study' ? 'selected' : '' }}>Study</option>
                                <option value="Other" {{ old('category') === 'Other' ? 'selected' : '' }}>Other</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm text-blue-100/70">Deskripsi</label>
                        <div class="relative mt-1" data-drop-wrapper>
                            <textarea name="description" rows="4"
                                class="w-full rounded-xl bg-white/5 border border-white/10 text-white placeholder:text-blue-100/40 focus:ring-0 focus:border-blue-300/40 drop-target"
                                placeholder="Catatan singkat..." data-drop-target>{{ old('description') }}</textarea>
                            <div class="drop-helper">
                                Drop file di sini untuk melampirkan
                            </div>
                        </div>
                        <div id="attachmentList" class="mt-3 space-y-2"></div>
                        <div id="attachmentInputs">
                            @foreach(old('attachments', []) as $fileId)
                                <input type="hidden" name="attachments[]" value="{{ $fileId }}">
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <div class="flex items-center justify-between gap-3">
                            <label class="block text-sm text-blue-100/70">Voice Recorder (teks)</label>
                            <span class="text-xs px-2 py-1 rounded-full bg-purple-500/20 border border-purple-400/30 text-purple-100 {{ $voiceLocked ? '' : 'hidden' }}">
                                Pro Feature
                            </span>
                        </div>
                        <div class="mt-2 flex items-center gap-2">
                            <button type="button" id="vrStartBtn"
                                {{ $voiceLocked ? 'disabled' : '' }}
                                class="px-3 py-2 rounded-xl border text-xs font-semibold transition
                                    {{ $voiceLocked
                                        ? 'bg-white/10 border-white/10 text-blue-100/40 cursor-not-allowed'
                                        : 'bg-blue-500/20 hover:bg-blue-500/30 border-blue-400/30 text-blue-100' }}">
                                Mulai Rekam
                            </button>
                            <button type="button" id="vrStopBtn" disabled
                                class="px-3 py-2 rounded-xl bg-white/10 border border-white/10 text-blue-100/60 text-xs font-semibold transition cursor-not-allowed">
                                Stop
                            </button>
                            <button type="button" id="vrClearBtn"
                                class="px-3 py-2 rounded-xl bg-white/10 hover:bg-white/15 border border-white/10 text-blue-100 text-xs font-semibold transition">
                                Hapus
                            </button>
                        </div>
                        <textarea name="voice_text" id="voiceText" rows="3" readonly
                            class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 text-white placeholder:text-blue-100/40 focus:ring-0 focus:border-blue-300/40"
                            placeholder="{{ $voiceLocked ? 'Voice recorder is available on Pro and Plus.' : 'Tekan Mulai Rekam lalu bicara...' }}"
                            >{{ $voiceLocked ? 'Voice recorder is available on Pro and Plus.' : '' }}</textarea>
                        <p id="vrHint" class="mt-2 text-xs text-white">
                            @if($voiceLocked)
                                Voice recorder is available on Pro and Plus.
                            @else
                                Voice Record akan disimpan sebagai teks. Pastikan izin mikrofon di browser diizinkan.
                            @endif
                        </p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-blue-100/70">Deadline</label>
                            <input name="due_date" type="date" value="{{ old('due_date', request('date')) }}"
                                class="mt-1 w-full rounded-xl bg-white/5 border border-white/10 text-white focus:ring-0 focus:border-blue-300/40">
                        </div>

                        <div>
                            <label class="block text-sm text-blue-100/70">Status</label>
                            <div class="mt-2 grid grid-cols-2 gap-3">
                                <label class="relative">
                                    <input type="radio" name="status" value="berjalan" class="peer hidden"
                                        @checked(old('status', 'berjalan') === 'berjalan')>
                                    <span
                                        class="flex items-center justify-center rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-xs font-semibold text-blue-100/70 transition hover:bg-white/10
                                        peer-checked:border-purple-400/30 peer-checked:bg-purple-500/20 peer-checked:text-purple-100">
                                        IN PROGRESS
                                    </span>
                                </label>

                                <label class="relative">
                                    <input type="radio" name="status" value="selesai" class="peer hidden"
                                        @checked(old('status') === 'selesai')>
                                    <span
                                        class="flex items-center justify-center rounded-xl border border-white/10 bg-white/5 px-4 py-2 text-xs font-semibold text-blue-100/70 transition hover:bg-white/10
                                        peer-checked:border-green-400/30 peer-checked:bg-green-500/20 peer-checked:text-green-100">
                                        COMPLETED
                                    </span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit"
                            class="px-6 py-2 rounded-xl bg-blue-500/90 hover:bg-blue-500 text-white font-semibold transition">
                            Simpan Task
                        </button>
                        <a href="{{ route('dashboard') }}"
                            class="px-6 py-2 rounded-xl bg-white/10 hover:bg-white/15 border border-white/10 text-blue-100 font-semibold transition">
                            Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="lg:col-span-1 space-y-6">
            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Ringkasan</h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-blue-100/70">Created at</span>
                        <span class="text-blue-100/90">-</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-blue-100/70">Last update</span>
                        <span class="text-blue-100/90">-</span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-blue-100/70">Status</span>
                        <span class="text-xs px-3 py-1 rounded-full bg-purple-500/15 border border-purple-400/20 text-purple-100">
                            IN PROGRESS
                        </span>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <span class="text-blue-100/70">Deadline</span>
                        <span class="text-blue-100/90">-</span>
                    </div>
                    <div class="pt-3 border-t border-white/10">
                        <div class="text-blue-100/70 mb-2">Plan user</div>
                        <span class="text-xs px-3 py-1 rounded-full bg-white/10 border border-white/10 text-blue-100">
                            {{ Auth::user()->plan_label ?? 'Free Plan' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const startBtn = document.getElementById('vrStartBtn');
    const stopBtn  = document.getElementById('vrStopBtn');
    const clearBtn = document.getElementById('vrClearBtn');
    const voiceEl  = document.getElementById('voiceText');
    const hintEl   = document.getElementById('vrHint');

    const VOICE_LOCKED = @json($voiceLocked);

    if (VOICE_LOCKED) {
        return;
    }

    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) {
        hintEl.textContent = "Browser tidak mendukung Voice Record. Gunakan Chrome/Edge.";
        startBtn.disabled = true;
        startBtn.classList.add("opacity-50", "cursor-not-allowed");
        return;
    }

    const recognition = new SpeechRecognition();
    recognition.lang = 'id-ID';
    recognition.interimResults = true;
    recognition.continuous = true;

    let finalTranscript = "";

    function setRecordingUI(isRecording) {
        if (isRecording) {
            startBtn.disabled = true;
            startBtn.className = "px-3 py-2 rounded-xl bg-white/10 border border-white/10 text-blue-100/60 text-xs font-semibold transition cursor-not-allowed";
            stopBtn.disabled = false;
            stopBtn.className = "px-3 py-2 rounded-xl bg-red-500/20 hover:bg-red-500/30 border border-red-400/30 text-red-100 text-xs font-semibold transition";
            hintEl.textContent = "Merekam... klik Stop untuk selesai.";
        } else {
            startBtn.disabled = false;
            startBtn.className = "px-3 py-2 rounded-xl bg-blue-500/20 hover:bg-blue-500/30 border border-blue-400/30 text-blue-100 text-xs font-semibold transition";
            stopBtn.disabled = true;
            stopBtn.className = "px-3 py-2 rounded-xl bg-white/10 border border-white/10 text-blue-100/60 text-xs font-semibold transition cursor-not-allowed";
            hintEl.textContent = "Voice Record akan disimpan sebagai teks. Pastikan izin mikrofon di browser diizinkan.";
        }
    }

    startBtn?.addEventListener('click', async () => {
        try {
            await navigator.mediaDevices.getUserMedia({ audio: true });
            finalTranscript = voiceEl.value ? voiceEl.value + " " : "";
            recognition.start();
            setRecordingUI(true);
        } catch (err) {
            hintEl.textContent = "Izin mikrofon ditolak / tidak tersedia. Cek permission browser.";
        }
    });

    stopBtn?.addEventListener('click', () => {
        recognition.stop();
        setRecordingUI(false);
    });

    clearBtn?.addEventListener('click', () => {
        finalTranscript = "";
        voiceEl.value = "";
    });

    recognition.onresult = (event) => {
        let interim = "";
        for (let i = event.resultIndex; i < event.results.length; i++) {
            const transcript = event.results[i][0].transcript;
            if (event.results[i].isFinal) {
                finalTranscript += transcript + " ";
            } else {
                interim += transcript;
            }
        }
        voiceEl.value = (finalTranscript + interim).trim();
    };

    recognition.onerror = () => {
        setRecordingUI(false);
        hintEl.textContent = "Terjadi error pada Voice Record. Coba ulang atau refresh halaman.";
    };

    recognition.onend = () => {
        setRecordingUI(false);
    };
})();
</script>
@endsection

@push('scripts')
<style>
    .drop-helper {
        position: absolute;
        inset: 10px;
        border-radius: 14px;
        border: 1px dashed rgba(59, 130, 246, 0.5);
        background: rgba(15, 23, 42, 0.65);
        color: rgba(226, 232, 240, 0.8);
        font-size: 12px;
        display: none;
        align-items: center;
        justify-content: center;
        text-align: center;
        pointer-events: none;
    }

    .drop-active .drop-helper {
        display: flex;
    }

    .drop-active .drop-target {
        border-color: rgba(59, 130, 246, 0.6);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15);
    }

    .attachment-item {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 10px 12px;
        border-radius: 12px;
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(148, 163, 184, 0.2);
    }

    .attachment-thumb {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        overflow: hidden;
        flex-shrink: 0;
        background: rgba(30, 41, 59, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .zentask-select {
        background-color: rgba(15, 23, 42, 0.7);
    }

    .zentask-select option {
        background-color: #0f172a;
        color: #e2e8f0;
    }
</style>

<script>
    (function () {
        const fileItems = document.querySelectorAll('.file-item[draggable="true"]');
        fileItems.forEach((item) => {
            item.addEventListener('dragstart', (event) => {
                const payload = {
                    id: item.dataset.fileId,
                    name: item.dataset.fileName,
                    url: item.dataset.fileUrl,
                    type: item.dataset.fileType,
                };
                event.dataTransfer.setData('application/json', JSON.stringify(payload));
                event.dataTransfer.effectAllowed = 'copy';
            });
        });

        const wrapper = document.querySelector('[data-drop-wrapper]');
        const textarea = document.querySelector('[data-drop-target]');
        const list = document.getElementById('attachmentList');
        const inputs = document.getElementById('attachmentInputs');
        if (!wrapper || !textarea || !list || !inputs) return;

        function addAttachment(payload) {
            if (inputs.querySelector(`input[value="${payload.id}"]`)) return;

            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'attachments[]';
            input.value = payload.id;
            inputs.appendChild(input);

            const item = document.createElement('div');
            item.className = 'attachment-item';
            item.dataset.fileId = payload.id;

            const thumb = document.createElement('div');
            thumb.className = 'attachment-thumb';
            if (payload.type === 'image') {
                const img = document.createElement('img');
                img.src = payload.url;
                img.alt = payload.name;
                img.className = 'w-full h-full object-cover';
                thumb.appendChild(img);
            } else {
                thumb.innerHTML = '<svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 11h10M7 15h6M5 3h8l6 6v12a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/></svg>';
            }

            const info = document.createElement('div');
            info.className = 'flex-1 min-w-0';
            info.innerHTML = `
                <div class="text-sm text-white font-semibold truncate">${payload.name}</div>
                <div class="text-xs text-blue-100/60">Attached</div>
            `;

            const actions = document.createElement('div');
            actions.className = 'flex items-center gap-2';
            actions.innerHTML = `
                <a href="${payload.url}" target="_blank" class="text-xs text-blue-300 hover:text-blue-200">View</a>
                <button type="button" class="text-xs text-red-300 hover:text-red-200">Remove</button>
            `;

            actions.querySelector('button').addEventListener('click', () => {
                input.remove();
                item.remove();
                textarea.value = textarea.value.replace(`[file:${payload.id}]`, '').trim();
            });

            item.appendChild(thumb);
            item.appendChild(info);
            item.appendChild(actions);
            list.appendChild(item);

            const token = `[file:${payload.id}]`;
            const start = textarea.selectionStart || textarea.value.length;
            const end = textarea.selectionEnd || textarea.value.length;
            textarea.value = textarea.value.slice(0, start) + token + textarea.value.slice(end);
        }

        ['dragenter', 'dragover'].forEach((eventName) => {
            textarea.addEventListener(eventName, (event) => {
                event.preventDefault();
                wrapper.classList.add('drop-active');
            });
        });

        ['dragleave', 'dragend'].forEach((eventName) => {
            textarea.addEventListener(eventName, () => {
                wrapper.classList.remove('drop-active');
            });
        });

        textarea.addEventListener('drop', (event) => {
            event.preventDefault();
            wrapper.classList.remove('drop-active');
            const data = event.dataTransfer.getData('application/json');
            if (!data) return;
            try {
                const payload = JSON.parse(data);
                addAttachment(payload);
            } catch (error) {
                // ignore
            }
        });
    })();
</script>
@endpush
