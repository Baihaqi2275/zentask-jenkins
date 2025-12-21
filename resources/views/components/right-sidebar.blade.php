{{-- Right Sidebar: Today Note, Files, Activity --}}
<aside class="w-80 xl:w-96 bg-slate-900/30 backdrop-blur-xl border-l border-slate-800/50 flex-shrink-0 hidden lg:flex">
<div class="flex-1 p-6 space-y-4 overflow-y-auto custom-scrollbar">

{{-- Today's Note Widget --}}
<div class="glass-card rounded-2xl p-5 space-y-4" x-data="{ editing: false, note: '{{ Auth::user()->today_note ?? 'Going to the company and planning meetings for the week ahead' }}' }">
    <div class="flex items-center justify-between">
        <h3 class="text-white font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Today note
        </h3>
        <button @click="editing = !editing" class="p-1.5 text-gray-400 hover:text-blue-400 hover:bg-slate-800/50 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
            </svg>
        </button>
    </div>

    {{-- View Mode --}}
    <div x-show="!editing" class="space-y-3">
        <p class="text-gray-300 text-sm leading-relaxed" x-text="note"></p>
        <div class="flex items-center gap-2 text-xs text-gray-500">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>20 min ago</span>
            <span class="flex items-center gap-1 ml-auto px-2 py-1 bg-green-500/20 text-green-400 rounded-lg">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                I'm going
            </span>
        </div>
    </div>

    {{-- Edit Mode --}}
    <div x-show="editing" x-transition class="space-y-3">
        <textarea x-model="note"
                  class="w-full px-3 py-2 bg-slate-800/50 border border-slate-700 rounded-lg text-white text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                  rows="3"></textarea>
        <div class="flex gap-2">
            <button @click="editing = false" class="flex-1 px-3 py-1.5 bg-slate-700 hover:bg-slate-600 text-white text-sm rounded-lg transition-colors">
                Cancel
            </button>
            <button @click="editing = false" class="flex-1 px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition-colors">
                Save
            </button>
        </div>
    </div>
</div>
{{-- My Files Widget --}}
<div class="glass-card rounded-2xl p-5 space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="text-white font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"/>
            </svg>
            My files
        </h3>
        <button class="p-1.5 text-gray-400 hover:text-purple-400 hover:bg-slate-800/50 rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
            </svg>
        </button>
    </div>

    @php
        $userFiles = Auth::user()->files()->latest()->take(8)->get();
    @endphp

    @if(session('file_success'))
        <div class="rounded-xl border border-green-500/20 bg-green-500/10 px-3 py-2 text-xs text-green-200">
            {{ session('file_success') }}
        </div>
    @endif

    @if($errors->has('file'))
        <div class="rounded-xl border border-red-500/20 bg-red-500/10 px-3 py-2 text-xs text-red-200">
            {{ $errors->first('file') }}
        </div>
    @endif

    <form method="POST" action="{{ route('files.upload') }}" enctype="multipart/form-data" class="space-y-3">
        @csrf
        <input id="myFilesUpload" type="file" name="file" accept=".pdf,.png,.jpg,.jpeg" class="hidden" onchange="this.form.submit()">
        <label for="myFilesUpload"
               class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-500 to-blue-500 hover:from-purple-600 hover:to-blue-600 text-white rounded-xl font-medium transition-all transform hover:scale-105 cursor-pointer">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Add file
        </label>
        <p class="text-xs text-gray-500">Format: PDF, PNG, JPG (maks 5 MB)</p>
    </form>

    @if($userFiles->isEmpty())
        <div class="flex flex-col items-center justify-center py-6 text-center">
            <div class="w-20 h-20 mb-4 bg-slate-800/50 rounded-2xl flex items-center justify-center">
                <svg class="w-10 h-10 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                </svg>
            </div>
            <p class="text-gray-400 font-medium mb-1">Belum ada file</p>
            <p class="text-gray-600 text-sm">Upload PDF, PNG, atau JPG</p>
        </div>
    @else
        <div class="space-y-2">
            @foreach($userFiles as $file)
                @php
                    $isImage = in_array($file->extension, ['png', 'jpg', 'jpeg']);
                    $sizeKb = $file->size / 1024;
                    $sizeLabel = $sizeKb >= 1024
                        ? number_format($sizeKb / 1024, 1) . ' MB'
                        : number_format($sizeKb, 1) . ' KB';
                @endphp
                <div class="file-item flex items-center gap-3 p-3 bg-slate-800/40 border border-slate-700/40 rounded-xl cursor-grab"
                     draggable="true"
                     data-file-id="{{ $file->id }}"
                     data-file-name="{{ $file->original_name }}"
                     data-file-url="{{ Storage::url($file->path) }}"
                     data-file-type="{{ $isImage ? 'image' : 'pdf' }}">
                    <div class="w-10 h-10 rounded-lg bg-slate-900/40 flex items-center justify-center">
                        @if($isImage)
                            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4-4a3 3 0 014 0l4 4m0 0l4-4a3 3 0 014 0l4 4M4 16h16v4H4z"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h10M7 11h10M7 15h6M5 3h8l6 6v12a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2z"/>
                            </svg>
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="text-sm text-white font-semibold truncate">{{ $file->original_name }}</div>
                        <div class="text-xs text-gray-500">{{ $sizeLabel }} · Uploaded</div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-purple-300">Drag</span>
                        <form method="POST" action="{{ route('files.destroy', $file) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="text-xs text-red-300 hover:text-red-200 px-2 py-1 rounded-lg hover:bg-red-500/10 transition">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- Activity Widget --}}
<div class="glass-card rounded-2xl p-5 space-y-4">
    <div class="flex items-center justify-between">
        <h3 class="text-white font-semibold flex items-center gap-2">
            <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Activity
        </h3>
        <button class="px-3 py-1.5 bg-blue-500/20 text-blue-400 text-xs font-semibold rounded-lg hover:bg-blue-500/30 transition-colors">
            Get the report
        </button>
    </div>

    {{-- Tasks Completed Badge --}}
    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-green-500/10 to-blue-500/10 rounded-xl border border-green-500/20">
        <span class="text-gray-300 text-sm">13 Tasks Completed</span>
        <span class="px-2 py-1 bg-green-500/20 text-green-400 text-xs font-semibold rounded-full">New</span>
    </div>

    {{-- Activity Chart --}}
    <div class="relative h-40">
        @php
            $user = Auth::user();
            $months = ['Feb', 'Mar', 'Apr', 'May'];
            $data = [
                'Feb' => $user->tasks()->whereMonth('created_at', 2)->count(),
                'Mar' => $user->tasks()->whereMonth('created_at', 3)->count(),
                'Apr' => $user->tasks()->whereMonth('created_at', 4)->count(),
                'May' => $user->tasks()->whereMonth('created_at', 5)->count(),
            ];
            $maxValue = max(array_values($data)) ?: 1;
        @endphp

        <div class="flex items-end justify-between h-full gap-3">
            @foreach($data as $month => $count)
                @php
                    $percentage = ($count / $maxValue) * 100;
                    $colors = [
                        'Feb' => 'from-blue-500 to-blue-600',
                        'Mar' => 'from-purple-500 to-purple-600',
                        'Apr' => 'from-pink-500 to-pink-600',
                        'May' => 'from-cyan-500 to-cyan-600',
                    ];
                @endphp
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-slate-800/50 rounded-t-xl overflow-hidden relative group" style="height: {{ $percentage }}%;">
                        <div class="absolute inset-0 bg-gradient-to-t {{ $colors[$month] }} opacity-80 group-hover:opacity-100 transition-opacity"></div>
                        <div class="absolute inset-0 bg-gradient-to-t from-white/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                    </div>
                    <span class="text-xs text-gray-400 font-medium">{{ $month }}</span>
                </div>
            @endforeach
        </div>

        {{-- Percentage Badge --}}
        <div class="absolute top-0 right-0 px-3 py-1.5 bg-green-500/20 text-green-400 rounded-xl border border-green-500/30 text-sm font-bold">
            {{ $user->tasks()->where('status', 'selesai')->count() > 0 ? round(($user->tasks()->where('status', 'selesai')->count() / $user->tasks()->count()) * 100) : 0 }}%
        </div>
    </div>
</div>

{{-- Quick Stats --}}
<div class="glass-card rounded-2xl p-5 space-y-3">
    <h3 class="text-white font-semibold text-sm mb-4">Quick Stats</h3>

    <div class="space-y-3">
        <div class="flex items-center justify-between p-3 bg-slate-800/30 rounded-xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-blue-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-gray-400 text-xs">Total Tasks</p>
                    <p class="text-white font-bold text-lg">{{ Auth::user()->tasks()->count() }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between p-3 bg-slate-800/30 rounded-xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-yellow-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-gray-400 text-xs">In Progress</p>
                    <p class="text-white font-bold text-lg">{{ Auth::user()->tasks()->where('status', 'berjalan')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between p-3 bg-slate-800/30 rounded-xl">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-green-500/20 rounded-lg flex items-center justify-center">
                    <svg class="w-5 h-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-gray-400 text-xs">Completed</p>
                    <p class="text-white font-bold text-lg">{{ Auth::user()->tasks()->where('status', 'selesai')->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

</div>
</aside>
