<?php

namespace App\Http\Controllers;

use App\Models\UserFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FileController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:pdf,png,jpg,jpeg|max:5120',
        ], [
            'file.mimes' => 'Format file tidak didukung. Gunakan PDF, PNG, atau JPG.',
            'file.max' => 'Ukuran file maksimal 5 MB.',
            'file.required' => 'Pilih file terlebih dahulu.',
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());
        $filename = 'file_' . Auth::id() . '_' . Str::uuid() . '.' . $extension;
        $path = $file->storeAs('uploads', $filename, 'public');

        UserFile::create([
            'user_id' => Auth::id(),
            'original_name' => $file->getClientOriginalName(),
            'file_name' => $filename,
            'path' => $path,
            'mime_type' => $file->getClientMimeType(),
            'extension' => $extension,
            'size' => $file->getSize(),
        ]);

        return back()->with('file_success', 'File berhasil diupload.');
    }

    public function destroy(UserFile $file)
    {
        if ($file->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        Storage::disk('public')->delete($file->path);
        $file->delete();

        return back()->with('file_success', 'File berhasil dihapus.');
    }
}
