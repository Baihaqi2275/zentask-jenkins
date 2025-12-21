<?php
// test-upload.php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\Storage;

// Test 1: Check if storage is configured
echo "Test 1: Storage Configuration\n";
echo "Public disk exists: " . (Storage::disk('public') ? 'YES' : 'NO') . "\n";
echo "Storage path: " . storage_path('app/public') . "\n";
echo "Public storage URL: " . asset('storage') . "\n\n";

// Test 2: Check avatars folder
echo "Test 2: Avatars Folder\n";
echo "Avatars folder exists: " . (Storage::disk('public')->exists('avatars') ? 'YES' : 'NO') . "\n";
echo "Avatars path: " . storage_path('app/public/avatars') . "\n\n";

// Test 3: Try to create a test file
echo "Test 3: Write Test\n";
try {
    $testContent = "Test file created at " . now();
    Storage::disk('public')->put('avatars/test.txt', $testContent);
    echo "✅ Test file created successfully!\n";
    echo "Test file exists: " . (Storage::disk('public')->exists('avatars/test.txt') ? 'YES' : 'NO') . "\n";

    // Clean up
    Storage::disk('public')->delete('avatars/test.txt');
    echo "✅ Test file deleted successfully!\n";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}

echo "\nAll tests completed!\n";
