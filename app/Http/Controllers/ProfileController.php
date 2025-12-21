<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class ProfileController extends Controller
{
    /**
     * Show profile edit page
     */           
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user()
        ]);
    }

    /**
     * Update profile information
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'min:2'],
            'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'], // 2MB max
        ], [
            'name.required' => 'Name is required.',
            'name.min' => 'Name must be at least 2 characters.',
            'avatar.image' => 'File must be an image.',
            'avatar.mimes' => 'Avatar must be a file of type: jpeg, png, jpg, gif, webp.',
            'avatar.max' => 'Avatar size must not exceed 2MB.',
        ]);

        try {
            // Handle avatar upload
            if ($request->hasFile('avatar')) {
                // Delete old avatar if exists
                if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // Store new avatar with optimized name
                $file = $request->file('avatar');
                $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();

                // Store avatar
                $path = $file->storeAs('avatars', $filename, 'public');

                // Optional: Resize image for performance (requires intervention/image package)
                // $this->optimizeAvatar($path);

                $validated['avatar'] = $path;
            }

            // Update user
            $user->update([
                'name' => $validated['name'],
                'avatar' => $validated['avatar'] ?? $user->avatar,
            ]);

            // Log activity (optional)
            activity()
                ->causedBy($user)
                ->log('Profile updated');

            return back()->with('success', 'Profile updated successfully! ✨');

        } catch (\Exception $e) {
            // Log error
            \Log::error('Profile update failed: ' . $e->getMessage());

            return back()
                ->withInput()
                ->with('error', 'Failed to update profile. Please try again.');
        }
    }

    /**
     * Update profile avatar only
     */
    public function updateAvatar(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);

        try {
            // Delete old avatar
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Store new avatar
            $file = $request->file('avatar');
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('avatars', $filename, 'public');

            // Update user
            $user->update(['avatar' => $path]);

            return back()->with('success', 'Avatar updated successfully! 📸');

        } catch (\Exception $e) {
            \Log::error('Avatar update failed: ' . $e->getMessage());

            return back()->with('error', 'Failed to update avatar. Please try again.');
        }
    }

    /**
     * Delete avatar
     */
    public function deleteAvatar()
    {
        $user = Auth::user();

        try {
            if ($user->avatar) {
                // Delete from storage
                if (Storage::disk('public')->exists($user->avatar)) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // Update user record
                $user->update(['avatar' => null]);

                return back()->with('success', 'Avatar removed successfully! 🗑️');
            }

            return back()->with('info', 'No avatar to remove.');

        } catch (\Exception $e) {
            \Log::error('Avatar deletion failed: ' . $e->getMessage());

            return back()->with('error', 'Failed to remove avatar. Please try again.');
        }
    }

    /**
     * Update password
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', Password::defaults(), 'confirmed'],
        ], [
            'current_password.current_password' => 'The provided password does not match your current password.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        try {
            $user = Auth::user();

            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            // Log activity
            activity()
                ->causedBy($user)
                ->log('Password changed');

            return back()->with('success', 'Password updated successfully! 🔐');

        } catch (\Exception $e) {
            \Log::error('Password update failed: ' . $e->getMessage());

            return back()->with('error', 'Failed to update password. Please try again.');
        }
    }

    /**
     * Delete user account
     */
    public function destroy(Request $request)
    {
        $validated = $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ], [
            'password.current_password' => 'The provided password is incorrect.',
        ]);

        try {
            $user = Auth::user();

            // Logout user
            Auth::logout();

            // Delete avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            // Delete user's tasks (cascade or soft delete)
            // $user->tasks()->delete();

            // Log before deletion
            \Log::info('User account deleted', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            // Delete user
            $user->delete();

            // Invalidate session
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/')
                ->with('success', 'Your account has been deleted successfully.');

        } catch (\Exception $e) {
            \Log::error('Account deletion failed: ' . $e->getMessage());

            return back()->with('error', 'Failed to delete account. Please contact support.');
        }
    }

    /**
     * Show user statistics
     */
    public function statistics()
    {
        $user = Auth::user();

        try {
            $stats = [
                'total_tasks' => $user->tasks()->count(),
                'completed_tasks' => $user->tasks()->where('status', 'selesai')->count(),
                'pending_tasks' => $user->tasks()->where('status', 'berjalan')->count(),
                'overdue_tasks' => $user->tasks()
                    ->where('status', '!=', 'selesai')
                    ->where('deadline', '<', now())
                    ->count(),
                'productivity_rate' => $this->calculateProductivityRate($user),
                'member_since' => $user->created_at->diffForHumans(),
                'current_plan' => $user->plan_label,
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            \Log::error('Failed to fetch statistics: ' . $e->getMessage());

            return response()->json(['error' => 'Failed to load statistics'], 500);
        }
    }

    /**
     * Calculate user productivity rate
     */
    private function calculateProductivityRate($user): int
    {
        $totalTasks = $user->tasks()->count();

        if ($totalTasks === 0) {
            return 0;
        }

        $completedTasks = $user->tasks()->where('status', 'selesai')->count();

        return round(($completedTasks / $totalTasks) * 100);
    }

    /**
     * Optimize avatar image (requires intervention/image)
     * Uncomment if you install: composer require intervention/image
     */
    private function optimizeAvatar(string $path): void
    {
        try {
            $fullPath = storage_path('app/public/' . $path);

            // Resize and optimize
            $img = Image::make($fullPath);

            // Resize to max 400x400 while maintaining aspect ratio
            $img->fit(400, 400, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });

            // Optimize quality
            $img->save($fullPath, 85);

        } catch (\Exception $e) {
            \Log::warning('Avatar optimization failed: ' . $e->getMessage());
            // Continue without optimization
        }
    }

    /**
     * Export user data (GDPR compliance)
     */
    public function exportData()
    {
        try {
            $user = Auth::user();

            $data = [
                'user' => [
                    'name' => $user->name,
                    'email' => $user->email,
                    'subscription_plan' => $user->subscription_plan,
                    'created_at' => $user->created_at->toDateTimeString(),
                ],
                'tasks' => $user->tasks()->get()->toArray(),
                'exported_at' => now()->toDateTimeString(),
            ];

            $filename = 'zentask_data_' . $user->id . '_' . date('Y-m-d') . '.json';

            return response()->json($data)
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');

        } catch (\Exception $e) {
            \Log::error('Data export failed: ' . $e->getMessage());

            return back()->with('error', 'Failed to export data. Please try again.');
        }
    }
}
