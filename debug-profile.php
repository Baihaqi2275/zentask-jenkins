<?php
// debug-profile.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

echo "=== ZenTask Profile Debug ===\n\n";

// Test 1: Storage Configuration
echo "1. STORAGE CONFIGURATION:\n";
echo "   - Default disk: " . config('filesystems.default') . "\n";
echo "   - Public disk root: " . config('filesystems.disks.public.root') . "\n";
echo "   - Public disk URL: " . config('filesystems.disks.public.url') . "\n";
echo "   - Storage path: " . storage_path('app/public') . "\n";
echo "   - Public storage path: " . public_path('storage') . "\n\n";

// Test 2: Folder Permissions
echo "2. FOLDER PERMISSIONS:\n";
$storagePath = storage_path('app/public/avatars');
echo "   - Avatars folder exists: " . (file_exists($storagePath) ? 'YES' : 'NO') . "\n";
echo "   - Avatars folder writable: " . (is_writable($storagePath) ? 'YES' : 'NO') . "\n";
echo "   - Storage link exists: " . (file_exists(public_path('storage')) ? 'YES' : 'NO') . "\n\n";

// Test 3: Storage Facade
echo "3. STORAGE FACADE TEST:\n";
try {
    echo "   - Can access public disk: " . (Storage::disk('public') ? 'YES' : 'NO') . "\n";
    echo "   - Avatars folder in disk: " . (Storage::disk('public')->exists('avatars') ? 'YES' : 'NO') . "\n";

    // Try to list files
    $files = Storage::disk('public')->files('avatars');
    echo "   - Files in avatars: " . count($files) . " files\n";

} catch (Exception $e) {
    echo "   - ERROR: " . $e->getMessage() . "\n";
}

echo "\n=== Debug Complete ===\n";
