<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ProductMedia;
use Illuminate\Support\Facades\Storage;

class CleanupUnusedImages extends Command
{
    protected $signature = 'images:cleanup';
    protected $description = 'Cleanup unused product images';

    public function handle()
    {
        $this->info('Starting cleanup of unused images...');

        // Get all file paths in use
        $usedPaths = ProductMedia::pluck('file_path')->toArray();
        $this->info('Found ' . count($usedPaths) . ' images in use');

        // Scan storage directory
        $productsDir = storage_path('app/public/products');
        if (!is_dir($productsDir)) {
            $this->error('Products directory not found!');
            return 1;
        }

        $allFiles = glob($productsDir . '/*');
        $deletedCount = 0;

        foreach ($allFiles as $file) {
            if (is_file($file)) {
                $fileName = basename($file);
                if (!in_array($fileName, $usedPaths) && !in_array('products/' . $fileName, $usedPaths)) {
                    // Delete from products/
                    unlink($file);

                    // Delete from watermarked/ if exists
                    $watermarkedFile = $productsDir . '/watermarked/' . $fileName;
                    if (file_exists($watermarkedFile)) {
                        unlink($watermarkedFile);
                    }

                    $deletedCount++;
                    $this->line("Deleted: {$fileName}");
                }
            }
        }

        $this->info("Cleanup complete! Deleted {$deletedCount} unused images.");
        return 0;
    }
}
