@extends('layouts.dashboard-layout')

@section('content')

@php
    $voiceLocked = auth()->check() && !auth()->user()->canUseVoiceRecorder();
@endphp

    <div class="space-y-6">

            {{-- =========================
            [A] HEADER
            ========================== --}}
            <div class="mb-6">
                <h1 class="text-3xl lg:text-4xl font-bold text-white">Tasks</h1>
                <p class="text-blue-100/70 mt-1">Kelola aktivitas, deadline, dan status tugas.</p>
            </div>

            {{-- =========================
            [B] FLASH MESSAGE
            ========================== --}}
            @if (session('success'))
                <div class="mb-4 px-4 py-3 rounded-xl bg-green-500/10 border border-green-400/20 text-green-200">
                    {{ session('success') }}
                </div>
            @endif

            {{-- =========================
            [C] SUMMARY CARDS (URUTAN BARU)
            1. TOTAL KEGIATAN
            2. IN PROGRESS
            3. TASK COMPLETED
            4. DEADLINE TERDEKAT
            ========================== --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

                {{-- [C1] Total Kegiatan --}}
                <div class="glass-card rounded-2xl p-5">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-blue-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                            </svg>
                        </div>
                        <span class="text-xs px-3 py-1 rounded-full bg-blue-500/15 border border-blue-400/20 text-blue-100">
                            +{{ $totalTasks ?? 0 }} TOTAL
                        </span>
                    </div>
                    <div class="mt-4 text-blue-100/70 text-sm">TOTAL KEGIATAN</div>
                    <div class="text-4xl font-extrabold text-white mt-1">{{ $totalTasks ?? 0 }}</div>
                    <div class="mt-4 h-1 rounded-full bg-white/10 overflow-hidden">
                        <div class="h-full bg-blue-400/60" style="width: 70%"></div>
                    </div>
                </div>

                {{-- [C2] In Progress --}}
                <div class="glass-card rounded-2xl p-5">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-yellow-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 22a10 10 0 110-20 10 10 0 010 20z" />
                            </svg>
                        </div>
                        <span
                            class="text-xs px-3 py-1 rounded-full bg-yellow-500/15 border border-yellow-400/20 text-yellow-100">
                            BERJALAN
                        </span>
                    </div>
                    <div class="mt-4 text-blue-100/70 text-sm">IN PROGRESS</div>
                    <div class="text-4xl font-extrabold text-white mt-1">{{ $inProgressTasks ?? 0 }}</div>
                    <div class="mt-4 h-1 rounded-full bg-white/10 overflow-hidden">
                        <div class="h-full bg-yellow-400/60" style="width: 45%"></div>
                    </div>
                </div>

                {{-- [C3] Task Completed --}}
                <div class="glass-card rounded-2xl p-5">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-green-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                        <span
                            class="text-xs px-3 py-1 rounded-full bg-green-500/15 border border-green-400/20 text-green-100">
                            SELESAI
                        </span>
                    </div>
                    <div class="mt-4 text-blue-100/70 text-sm">TASK COMPLETED</div>
                    <div class="text-4xl font-extrabold text-white mt-1">{{ $completedTasks ?? 0 }}</div>
                    <div class="mt-4 h-1 rounded-full bg-white/10 overflow-hidden">
                        <div class="h-full bg-green-400/60" style="width: 60%"></div>
                    </div>
                </div>

                {{-- [C4] Deadline Terdekat --}}
                <div class="glass-card rounded-2xl p-5">
                    <div class="flex items-center justify-between">
                        <div class="w-10 h-10 rounded-xl bg-red-500/20 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v4m0 4h.01" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z" />
                            </svg>
                        </div>
                        <span class="text-xs px-3 py-1 rounded-full bg-red-500/15 border border-red-400/20 text-red-100">
                            URGENT
                        </span>
                    </div>

                    <div class="mt-4 text-blue-100/70 text-sm">DEADLINE TERDEKAT</div>

                    @if(!empty($nearestDeadlineTask))
                        <div class="text-2xl font-extrabold text-white mt-1">
                            {{ \Carbon\Carbon::parse($nearestDeadlineTask->due_date)->format('d M Y') }}
                        </div>
                        <div class="text-blue-100/70 text-sm mt-1">
                            {{ $nearestDeadlineTask->title ?? 'Task' }}
                        </div>
                    @else
                        <div class="text-2xl font-extrabold text-white mt-1">-</div>
                        <div class="text-blue-100/70 text-sm mt-1">Tidak ada</div>
                    @endif

                    <div class="mt-4 h-1 rounded-full bg-white/10 overflow-hidden">
                        <div class="h-full bg-red-400/60" style="width: 55%"></div>
                    </div>
                </div>

            </div>


           {{-- =========================
[D] FORM BUAT AKTIVITAS (FULL WIDTH)  FIX LAYOUT
- Voice Record dipindah ke BAWAH Deskripsi
- Voice Record full width (lg:col-span-2)
========================== --}}
<div class="glass-card rounded-2xl p-6 mb-6">
    <h3 class="text-xl font-semibold text-white mb-4">Buat Aktivitas</h3>

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-xl bg-red-500/10 border border-red-400/20 text-red-200 text-sm">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('tasks.store') }}" class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @csrf

        {{-- [D1] Judul (FULL WIDTH) --}}
        <div class="lg:col-span-2">
            <label class="block text-sm text-white">Judul</label>
            <input name="title" type="text" required
                class="mt-1 w-full rounded-xl bg-white/5 border border-white/10 text-white placeholder:text-blue-100/40 focus:ring-0 focus:border-blue-400/40"
                placeholder="Contoh: Kerjakan laporan DWBI">
        </div>

        {{-- [D2] Category (KIRI) --}}
        <div>
            <label class="block text-sm text-white">Category</label>
            <input name="category" type="text" required
                class="mt-1 w-full rounded-xl bg-white/5 border border-white/10 text-white placeholder:text-blue-100/40 focus:ring-0 focus:border-blue-400/40"
                placeholder="Contoh: Kuliah, Pribadi, Kerja">
        </div>

        {{-- [D3] Deadline (KANAN) --}}
        <div>
            <label class="block text-sm text-white">Deadline</label>
            <input name="due_date" type="date" required
                class="mt-1 w-full rounded-xl bg-white/5 border border-white/10 text-white focus:ring-0 focus:border-blue-300/40">
        </div>

        {{-- [D4] Deskripsi (FULL WIDTH) --}}
        <div class="lg:col-span-2">
            <label class="block text-sm text-white">Deskripsi</label>
            <textarea name="description" rows="3" required
                class="mt-1 w-full rounded-xl bg-white/5 border border-white/10 text-white placeholder:text-blue-100/40 focus:ring-0 focus:border-blue-300/40"
                placeholder="Catatan singkat..."></textarea>
        </div>

        {{-- [D5] Voice Record (FULL WIDTH + DI BAWAH DESKRIPSI)  --}}
        <div class="lg:col-span-2">
            <div class="flex items-center justify-between gap-3">
                <label class="block text-sm text-white">Voice Record jadi teks (opsional)</label>

                <div class="flex items-center gap-2">
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
            </div>

           <textarea name="voice_text" id="voiceText" rows="3" readonly
            class="mt-2 w-full rounded-xl bg-white/5 border border-white/10 text-white placeholder:text-blue-100/40 focus:ring-0 focus:border-blue-300/40"
            placeholder="{{ $voiceLocked ? 'Voice recorder is available on Pro and Plus.' : 'Tekan \'Mulai Rekam\' lalu bicara...' }}"
            >{{ $voiceLocked ? 'Voice recorder is available on Pro and Plus.' : '' }}</textarea>


           <p id="vrHint" class="mt-2 text-xs text-white">
    @if($voiceLocked)
        Voice recorder is available on Pro and Plus.
    @else
        *Voice Record akan disimpan sebagai teks. Pastikan izin mikrofon di browser diizinkan.
    @endif
</p>
        </div>

        {{-- [D6] Tombol Simpan (FULL WIDTH) --}}
        <div class="lg:col-span-2">
            <button type="submit"
                class="w-full py-2.5 rounded-xl bg-blue-500/90 hover:bg-blue-500 text-white font-semibold transition">
                Simpan
            </button>
        </div>
    </form>
</div>


            {{-- =========================
[E] DAFTAR AKTIVITAS
+ Kolom EDIT terpisah
+ DELETE pakai MODAL (center)
========================== --}}
<div class="glass-card rounded-2xl p-6">
    <h3 class="text-xl font-semibold text-white mb-4">Daftar Aktivitas</h3>
    @if(request('view') === 'deadlines')
        @php
            $deadlineGroups = $tasks->groupBy(function ($task) {
                return $task->due_date
                    ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d')
                    : 'no-deadline';
            });
        @endphp
        <div class="space-y-5 mb-6">
            @foreach($deadlineGroups as $deadlineKey => $groupTasks)
                @php
                    $deadlineLabel = $deadlineKey === 'no-deadline'
                        ? 'No deadline'
                        : \Carbon\Carbon::parse($deadlineKey)->format('d M Y');
                @endphp
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-semibold text-blue-100">{{ $deadlineLabel }}</h4>
                        <span class="text-xs text-blue-100/60">{{ $groupTasks->count() }} task</span>
                    </div>
                    <div class="flex gap-4 overflow-x-auto pb-2 custom-scrollbar">
                        @foreach($groupTasks as $task)
                            <div class="min-w-[260px] max-w-[260px] glass-card rounded-2xl p-4">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs px-2 py-1 rounded-full bg-blue-500/15 border border-blue-400/20 text-blue-100">
                                        {{ $task->category ?? 'Tanpa kategori' }}
                                    </span>
                                    @if(($task->status ?? '') === 'selesai')
                                        <span class="text-xs px-2 py-1 rounded-full bg-green-500/15 border border-green-400/20 text-green-100">
                                            SELESAI
                                        </span>
                                    @else
                                        <span class="text-xs px-2 py-1 rounded-full bg-purple-500/15 border border-purple-400/20 text-purple-100">
                                            IN PROGRESS
                                        </span>
                                    @endif
                                </div>
                                <h5 class="mt-3 text-white font-semibold text-sm line-clamp-2">
                                    {{ $task->title ?? 'Untitled' }}
                                </h5>
                                <p class="mt-2 text-xs text-blue-100/70 line-clamp-2">
                                    {{ $task->description ?? '-' }}
                                </p>
                                <div class="mt-4 flex items-center justify-between">
                                    <span class="text-xs text-blue-100/60">
                                        {{ $task->created_at ? $task->created_at->format('d M Y') : '-' }}
                                    </span>
                                    <a href="{{ route('tasks.edit', $task) }}"
                                        class="text-xs font-semibold text-blue-300 hover:text-blue-200 transition">
                                        Edit
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead>
                <tr class="text-left text-blue-100/70 border-b border-white/10">
                    <th class="py-3 pr-4">Judul</th>
                    <th class="py-3 pr-4">Category</th>
                    <th class="py-3 pr-4">Deadline</th>
                    <th class="py-3 pr-4">Status</th>
                    <th class="py-3 pr-4">Edit</th> {{--  KOLOM BARU --}}
                    <th class="py-3 pr-4">Aksi</th>
                </tr>
            </thead>

            <tbody>
                @forelse($tasks as $task)
                    @php
                        $isEditing = (string) request('edit') === (string) $task->id;
                    @endphp

                    <tr class="border-b border-white/10 align-top">

                        {{-- =====================
                        [E1] JUDUL (kalau edit: form lengkap)
                        ====================== --}}
                        <td class="py-4 pr-4">
                            @if(!$isEditing)
                                <div class="font-medium text-white">
                                    {{ $task->title ?? 'Untitled' }}
                                </div>
                            @else
                                {{-- FORM EDIT (inline) --}}
                                <form method="POST" action="{{ route('tasks.update', $task) }}" class="space-y-3">
                                    @csrf
                                    @method('PUT')

                                    <div>
                                        <label class="block text-xs text-blue-100/70">Judul</label>
                                        <input name="title" type="text" required
                                            value="{{ old('title', $task->title) }}"
                                            class="mt-1 w-full rounded-xl bg-white/5 border border-white/10 text-white
                                                   focus:ring-0 focus:border-blue-300/40">
                                    </div>

                                    <div>
                                        <label class="block text-xs text-blue-100/70">Category</label>
                                        <input name="category" type="text"
                                            value="{{ old('category', $task->category) }}"
                                            class="mt-1 w-full rounded-xl bg-white/5 border border-white/10 text-white
                                                   focus:ring-0 focus:border-blue-300/40">
                                    </div>

                                    <div>
                                        <label class="block text-xs text-blue-100/70">Deskripsi</label>
                                        <textarea name="description" rows="3"
                                            class="mt-1 w-full rounded-xl bg-white/5 border border-white/10 text-white
                                                   focus:ring-0 focus:border-blue-300/40">{{ old('description', $task->description) }}</textarea>
                                    </div>

                                    <div>
                                        <label class="block text-xs text-blue-100/70">Deadline</label>
                                        <input name="due_date" type="date"
                                            value="{{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('Y-m-d') : '' }}"
                                            class="mt-1 w-full rounded-xl bg-white/5 border border-white/10 text-white
                                                   focus:ring-0 focus:border-blue-300/40">
                                    </div>

                                    <div class="pt-2">
                                        <label class="block text-xs text-blue-100/70 mb-2">Status</label>
                                        <div class="flex gap-2">
                                            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-blue-100/80 text-xs">
                                                <input type="radio" name="status" value="berjalan"
                                                    class="accent-blue-400"
                                                    {{ old('status', $task->status) === 'berjalan' ? 'checked' : '' }}>
                                                IN PROGRESS
                                            </label>

                                            <label class="inline-flex items-center gap-2 px-3 py-2 rounded-xl bg-white/5 border border-white/10 text-blue-100/80 text-xs">
                                                <input type="radio" name="status" value="selesai"
                                                    class="accent-green-400"
                                                    {{ old('status', $task->status) === 'selesai' ? 'checked' : '' }}>
                                                SELESAI
                                            </label>
                                        </div>
                                    </div>

                                    <div class="flex gap-2 pt-2">
                                        <button type="submit"
                                            class="px-4 py-2 rounded-xl bg-blue-500/90 hover:bg-blue-500
                                                   text-white text-xs font-semibold transition">
                                            Simpan
                                        </button>

                                        <a href="{{ url()->current() }}"
                                            class="px-4 py-2 rounded-xl bg-white/10 hover:bg-white/15
                                                   border border-white/10 text-blue-100 text-xs font-semibold transition">
                                            Batal
                                        </a>
                                    </div>
                                </form>
                            @endif
                        </td>

                        {{-- =====================
                        [E2] CATEGORY
                        ====================== --}}
                        <td class="py-4 pr-4">
                            @if(!$isEditing)
                                <span class="inline-flex text-xs px-3 py-1 rounded-full
                                             bg-blue-500/15 border border-blue-400/20 text-blue-100">
                                    {{ $task->category ?? 'Tanpa kategori' }}
                                </span>
                            @else
                                <span class="text-blue-100/50">-</span>
                            @endif
                        </td>

                        {{-- =====================
                        [E3] DEADLINE
                        ====================== --}}
                        <td class="py-4 pr-4 text-blue-100/80">
                            @if(!$isEditing)
                                {{ $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d M Y') : '-' }}
                            @else
                                <span class="text-blue-100/50">-</span>
                            @endif
                        </td>

                        {{-- =====================
                        STATUS (READ ONLY)
                        Status diubah lewat halaman EDIT saja 
                        ====================== --}}
                        <td class="py-4 pr-4">
                            @php $st = $task->status ?? 'berjalan'; @endphp

                            @if($st === 'selesai')
                                <span class="inline-flex text-xs px-3 py-2 rounded-xl border
                                            bg-green-500/20 border-green-400/30 text-green-100">
                                    SELESAI
                                </span>
                            @else
                                <span class="inline-flex text-xs px-3 py-2 rounded-xl border
                                            bg-purple-500/20 border-purple-400/30 text-purple-100">
                                    IN PROGRESS
                                </span>
                            @endif
                        </td>

                        {{-- =====================
                        [E5] EDIT (kolom terpisah)
                        ====================== --}}
                        <td class="py-4 pr-4">
                            @if(!$isEditing)
                                <a href="{{ route('tasks.edit', $task) }}"
                                    class="inline-flex px-4 py-2 rounded-xl
                                           bg-blue-500/20 hover:bg-blue-500/30
                                           border border-blue-400/30
                                           text-blue-100 text-xs font-semibold transition">
                                    Edit
                                </a>
                            @else
                                <span class="text-blue-100/50">-</span>
                            @endif
                        </td>

                        {{-- =====================
                        [E6] AKSI (DELETE pakai modal)
                        ====================== --}}
                        <td class="py-4 pr-4">
                            <button type="button"
                                class="px-4 py-2 rounded-xl bg-red-500/20 hover:bg-red-500/30 border border-red-400/20 text-red-100 text-xs font-semibold transition"
                                onclick="openDeleteModal('{{ route('tasks.destroy', $task) }}', '{{ addslashes($task->title ?? 'Untitled') }}')">
                                Delete
                            </button>
                        </td>

                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-8 text-center text-blue-100/70">
                            Belum ada aktivitas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>


{{-- =========================
[F] MODAL DELETE (CENTER)
- Tempel sekali saja
========================== --}}
<div id="deleteModal" class="hidden fixed inset-0 z-[9999]">
    {{-- overlay --}}
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" onclick="closeDeleteModal()"></div>

    {{-- modal box --}}
    <div class="relative min-h-full flex items-center justify-center p-4">
        <div class="w-full max-w-md rounded-2xl bg-[#0b1220]/90 border border-white/10 shadow-2xl">
            <div class="p-6">
                <h3 class="text-xl font-semibold text-white">Konfirmasi Hapus</h3>

                <p class="text-blue-100/70 mt-2">
                    Yakin ingin menghapus task:
                    <span id="deleteTaskTitle" class="text-white font-semibold"></span> ?
                </p>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button type="button"
                        class="px-5 py-2 rounded-xl bg-white/10 hover:bg-white/15 border border-white/10 text-blue-100 font-semibold transition"
                        onclick="closeDeleteModal()">
                        Batal
                    </button>

                    <form id="deleteForm" method="POST" action="">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="px-5 py-2 rounded-xl bg-red-500/30 hover:bg-red-500/40 border border-red-400/20 text-red-100 font-semibold transition">
                            Hapus
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    </div>

{{-- =========================
[G] SCRIPT MODAL DELETE
========================== --}}
<script>
    function openDeleteModal(actionUrl, taskTitle) {
        const modal = document.getElementById('deleteModal');
        const form = document.getElementById('deleteForm');
        const title = document.getElementById('deleteTaskTitle');

        form.action = actionUrl;
        title.textContent = taskTitle;

        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }

    function closeDeleteModal() {
        const modal = document.getElementById('deleteModal');
        modal.classList.add('hidden');
        document.body.style.overflow = '';
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') closeDeleteModal();
    });
</script>

{{-- =========================
[VOICE] SCRIPT SPEECH TO TEXT
========================== --}}
<script>
(function () {
    const startBtn = document.getElementById('vrStartBtn');
    const stopBtn  = document.getElementById('vrStopBtn');
    const clearBtn = document.getElementById('vrClearBtn');
    const voiceEl  = document.getElementById('voiceText');
    const hintEl   = document.getElementById('vrHint');

    //  PLAN LOCK (Free plan can't use voice recorder)
    const VOICE_LOCKED = @json($voiceLocked);

    if (VOICE_LOCKED) {
        if (startBtn) {
            startBtn.disabled = true;
            startBtn.className =
                "px-3 py-2 rounded-xl bg-white/10 border border-white/10 text-blue-100/40 text-xs font-semibold transition cursor-not-allowed";
        }

        if (stopBtn) {
            stopBtn.disabled = true;
            stopBtn.className =
                "px-3 py-2 rounded-xl bg-white/10 border border-white/10 text-blue-100/40 text-xs font-semibold transition cursor-not-allowed";
        }

        if (voiceEl) voiceEl.value = "Voice recorder is available on Pro and Plus.";
        if (hintEl) hintEl.textContent = "Voice recorder is available on Pro and Plus.";

        return;
    }

    // cek dukungan browser
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) {
        hintEl.textContent = "Browser tidak mendukung Voice Record (Speech Recognition). Gunakan Chrome/Edge.";
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

            hintEl.textContent = "*Voice Record akan disimpan sebagai teks. Pastikan izin mikrofon di browser diizinkan.";
        }
    }

    startBtn?.addEventListener('click', async () => {
        try {
            // minta izin mic dulu agar jelas errornya kalau ditolak
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
        // kalau stop manual, UI sudah di-set, tapi ini buat jaga-jaga
        setRecordingUI(false);
    };
})();
</script>


@endsection
