@extends('layouts.dashboard-layout')

@section('content')
<div class="max-w-6xl mx-auto space-y-6">
    <div>
        <h1 class="text-3xl lg:text-4xl font-bold text-white">Settings / Profile</h1>
        <p class="text-blue-100/70 mt-1">Kelola profil, plan, dan keamanan akun.</p>
    </div>
    @if(session('success'))
        <div class="glass-card rounded-2xl p-4 border border-green-500/20 text-green-200"
             x-data="{ show: true }"
             x-show="show"
             x-transition>
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
                <button @click="show = false" class="ml-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="glass-card rounded-2xl p-4 border border-red-500/20 text-red-200"
             x-data="{ show: true }"
             x-show="show"
             x-transition>
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
                <button @click="show = false" class="ml-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-1 space-y-6">
            <div class="glass-card rounded-2xl p-6">
                <h3 class="text-lg font-semibold text-white mb-4">Profile Card</h3>

                <div class="flex flex-col items-center">
                    @if($user->avatar)
                        <img src="{{ asset('storage/' . $user->avatar) }}"
                             alt="{{ $user->name }}"
                             class="w-32 h-32 rounded-full object-cover border-4 border-blue-500/30 mb-4">
                    @else
                        <div class="w-32 h-32 rounded-full bg-gradient-to-br from-blue-500 to-blue-600 flex items-center justify-center text-white font-bold text-4xl border-4 border-blue-500/30 mb-4">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </div>
                    @endif

                    <form action="{{ route('profile.update') }}"
                          method="POST"
                          enctype="multipart/form-data"
                          class="w-full">
                        @csrf
                        @method('PATCH')

                        <input type="hidden" name="name" value="{{ $user->name }}">

                        <label class="block w-full mb-2">
                            <input type="file"
                                   name="avatar"
                                   accept="image/*"
                                   class="hidden"
                                   onchange="this.form.submit()">
                            <span class="cursor-pointer w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors text-center block text-sm font-medium">
                                Change Photo
                            </span>
                        </label>

                        @error('avatar')
                            <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </form>

                    @if($user->avatar)
                        <form action="{{ route('profile.avatar.delete') }}" method="POST" class="w-full mt-2">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('Remove avatar?')"
                                    class="w-full px-4 py-2 bg-red-600/20 hover:bg-red-600/30 text-red-300 rounded-xl transition-colors text-sm">
                                Remove Photo
                            </button>
                        </form>
                    @endif

                    <p class="text-blue-100/60 text-xs mt-4 text-center">
                        Max 2MB. JPG, PNG, GIF
                    </p>
                </div>

                <div class="mt-6 pt-6 border-t border-white/10 space-y-3">
                    @php
                        $planBadge = match($user->subscription_plan) {
                            'pro' => 'bg-purple-500/20 text-purple-100 border border-purple-400/30',
                            'plus' => 'bg-purple-500/20 text-purple-100 border border-purple-400/30',
                            default => 'bg-white/10 text-white/80 border border-white/20',
                        };
                    @endphp
                    <div>
                        <p class="text-blue-100/60 text-xs mb-1">Current Plan</p>
                        <span class="px-3 py-1 text-xs font-semibold rounded-full {{ $planBadge }}">
                            {{ ucfirst($user->subscription_plan) }} Plan
                        </span>
                    </div>
                    <div>
                        <p class="text-blue-100/60 text-xs mb-1">Member Since</p>
                        <p class="text-white text-sm">{{ $user->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-blue-100/60 text-xs mb-1">Email</p>
                        <p class="text-white text-sm truncate">{{ $user->email }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="lg:col-span-2 space-y-6">
            <div class="glass-card rounded-2xl p-6">
                <h2 class="text-xl font-bold text-white mb-6 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Personal Information
                </h2>

                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-blue-100/70 mb-2">Full Name</label>
                            <input type="text"
                                   name="name"
                                   value="{{ old('name', $user->name) }}"
                                   class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent transition"
                                   required>
                            @error('name')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-blue-100/70 mb-2">Email Address</label>
                            <input type="email"
                                   value="{{ $user->email }}"
                                   class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-blue-100/50 cursor-not-allowed"
                                   disabled
                                   readonly>
                            <p class="text-blue-100/50 text-xs mt-1">Email cannot be changed</p>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                    class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl transition-colors font-medium flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                                Save Changes
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <div class="glass-card rounded-2xl p-6 border border-red-500/30">
                <h2 class="text-xl font-bold text-red-400 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Danger Zone
                </h2>
                <p class="text-blue-100/60 text-sm mb-4">
                    Once you delete your account, there is no going back. Please be certain.
                </p>

                <button onclick="document.getElementById('deleteModal').classList.remove('hidden')"
                        class="px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-colors font-medium">
                    Delete Account
                </button>
            </div>
        </div>
    </div>
</div>

<div id="deleteModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="glass-card rounded-2xl p-6 max-w-md w-full border border-white/10">
        <h3 class="text-xl font-bold text-white mb-4">Delete Account</h3>
        <p class="text-blue-100/60 mb-6">
            Are you sure you want to delete your account? This action cannot be undone.
        </p>

        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf
            @method('DELETE')

            <div class="mb-4">
                <label class="block text-sm font-medium text-blue-100/70 mb-2">Confirm your password</label>
                <input type="password"
                       name="password"
                       class="w-full px-4 py-2.5 bg-white/5 border border-white/10 rounded-xl text-white focus:ring-2 focus:ring-red-500"
                       required>
            </div>

            <div class="flex gap-3 justify-end">
                <button type="button"
                        onclick="document.getElementById('deleteModal').classList.add('hidden')"
                        class="px-4 py-2 bg-white/10 hover:bg-white/15 text-white rounded-xl transition-colors">
                    Cancel
                </button>
                <button type="submit"
                        class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl transition-colors">
                    Delete Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
