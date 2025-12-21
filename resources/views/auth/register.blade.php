<x-guest-layout>
    <div class="min-h-screen w-full flex items-center justify-center bg-slate-950 text-white">
        <div class="relative w-full max-w-6xl rounded-[28px] border border-slate-800/60 bg-slate-900/60 backdrop-blur-2xl shadow-[0_25px_70px_rgba(0,0,0,0.45)] overflow-hidden">
            <div class="grid lg:grid-cols-2">
                <section class="relative p-10 lg:p-14 flex flex-col justify-between min-h-[520px] bg-gradient-to-br from-slate-950 via-slate-900 to-blue-900/70">
                    <div class="absolute inset-0 opacity-60">
                        <div class="absolute -top-24 -left-24 w-72 h-72 rounded-full bg-cyan-500/20 blur-3xl"></div>
                        <div class="absolute -bottom-24 right-0 w-72 h-72 rounded-full bg-blue-500/20 blur-3xl"></div>
                    </div>
                    <div class="relative z-10 flex items-center gap-3">
                        <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-blue-500 via-cyan-500 to-blue-600 flex items-center justify-center shadow-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                        </div>
                        <div>
                            <div class="text-xl font-bold">ZenTask</div>
                            <div class="text-xs uppercase tracking-[0.3em] text-blue-200/70">Task Management</div>
                        </div>
                    </div>

                    <div class="relative z-10 mt-10">
                        <div class="text-3xl font-semibold leading-snug">Build focus. Finish more.</div>
                        <p class="mt-4 text-sm text-blue-100/70 max-w-sm">
                            Kelola task dengan ritme yang lebih fokus, modern, dan terasa premium.
                        </p>
                    </div>

                    <div class="relative z-10 text-xs text-blue-200/60">ZenTask Productivity System</div>
                </section>

                <section class="p-10 lg:p-14 bg-slate-900/30">
                    <div class="max-w-md mx-auto">
                        <h1 class="text-3xl font-bold text-white">Daftar Akun Baru</h1>
                        <p class="mt-2 text-sm text-blue-100/70">
                            Kelola task-mu dengan cara yang lebih fokus dan modern.
                        </p>

                        @if (session('status'))
                            <div class="mt-6 rounded-xl border border-emerald-400/30 bg-emerald-400/10 px-4 py-3 text-sm text-emerald-100">
                                {{ session('status') }}
                            </div>
                        @endif

                        <form method="POST" action="{{ route('register') }}" class="mt-8 space-y-5 auth-form">
                            @csrf

                            <div>
                                <label class="text-sm text-blue-100/70">Nama Lengkap</label>
                                <input
                                    class="auth-input"
                                    type="text"
                                    name="name"
                                    value="{{ old('name') }}"
                                    required
                                    autofocus
                                    autocomplete="name"
                                    placeholder="Masukkan nama lengkap">
                                @error('name')
                                    <span class="auth-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="text-sm text-blue-100/70">Email</label>
                                <input
                                    class="auth-input"
                                    type="email"
                                    name="email"
                                    value="{{ old('email') }}"
                                    required
                                    autocomplete="username"
                                    placeholder="nama@email.com">
                                @error('email')
                                    <span class="auth-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="text-sm text-blue-100/70">Password</label>
                                <input
                                    class="auth-input"
                                    id="password"
                                    type="password"
                                    name="password"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Minimal 8 karakter">
                                <span class="auth-helper" id="passwordHint">Minimal 8 karakter.</span>
                                @error('password')
                                    <span class="auth-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <div>
                                <label class="text-sm text-blue-100/70">Konfirmasi Password</label>
                                <input
                                    class="auth-input"
                                    id="passwordConfirmation"
                                    type="password"
                                    name="password_confirmation"
                                    required
                                    autocomplete="new-password"
                                    placeholder="Ulangi password">
                                <span class="auth-helper" id="passwordMatch">Pastikan password sama.</span>
                                @error('password_confirmation')
                                    <span class="auth-error">{{ $message }}</span>
                                @enderror
                            </div>

                            <button type="submit" class="auth-button">
                                Daftar Sekarang
                            </button>

                            <p class="text-sm text-blue-100/70 text-center">
                                Sudah punya akun?
                                <a href="{{ route('login') }}" class="text-blue-100 underline">
                                    Masuk di sini
                                </a>
                            </p>
                        </form>
                    </div>
                </section>
            </div>
        </div>
    </div>

    <style>
        .auth-input {
            width: 100%;
            margin-top: 0.35rem;
            border-radius: 14px;
            border: 1px solid rgba(148, 163, 184, 0.25);
            background: rgba(15, 23, 42, 0.7);
            color: #f8fafc;
            padding: 0.7rem 0.9rem;
            font-size: 0.95rem;
            transition: border-color 0.25s ease, box-shadow 0.25s ease, background 0.25s ease;
        }

        .auth-input::placeholder {
            color: rgba(226, 232, 240, 0.4);
        }

        .auth-input:hover {
            border-color: rgba(59, 130, 246, 0.4);
        }

        .auth-input:focus {
            outline: none;
            border-color: rgba(59, 130, 246, 0.6);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
            background: rgba(15, 23, 42, 0.85);
        }

        .auth-button {
            width: 100%;
            padding: 0.85rem 1rem;
            border-radius: 14px;
            background: linear-gradient(135deg, #2563eb, #38bdf8);
            color: #fff;
            font-weight: 600;
            transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
        }

        .auth-button:hover {
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.35);
            filter: brightness(1.05);
            transform: translateY(-1px);
        }

        .auth-button:disabled {
            opacity: 0.7;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .auth-error {
            display: block;
            margin-top: 0.35rem;
            font-size: 0.75rem;
            color: rgba(252, 165, 165, 0.9);
        }

        .auth-helper {
            display: block;
            margin-top: 0.25rem;
            font-size: 0.75rem;
            color: rgba(148, 163, 184, 0.8);
        }
    </style>

    <script>
        (function () {
            const form = document.querySelector('.auth-form');
            const button = form?.querySelector('button[type="submit"]');
            const password = document.getElementById('password');
            const confirm = document.getElementById('passwordConfirmation');
            const hint = document.getElementById('passwordHint');
            const match = document.getElementById('passwordMatch');

            function updateHints() {
                if (!password || !confirm || !hint || !match) return;
                const value = password.value || '';
                const confirmValue = confirm.value || '';

                hint.textContent = value.length >= 8 ? 'Password sudah cukup kuat.' : 'Minimal 8 karakter.';
                hint.style.color = value.length >= 8 ? 'rgba(134, 239, 172, 0.9)' : 'rgba(148, 163, 184, 0.8)';

                if (!confirmValue) {
                    match.textContent = 'Pastikan password sama.';
                    match.style.color = 'rgba(148, 163, 184, 0.8)';
                    return;
                }

                const same = value === confirmValue;
                match.textContent = same ? 'Password cocok.' : 'Password belum sama.';
                match.style.color = same ? 'rgba(134, 239, 172, 0.9)' : 'rgba(252, 165, 165, 0.9)';
            }

            password?.addEventListener('input', updateHints);
            confirm?.addEventListener('input', updateHints);

            form?.addEventListener('submit', () => {
                if (!button) return;
                button.disabled = true;
                button.textContent = 'Memproses...';
            });
        })();
    </script>
</x-guest-layout>
