<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WatermarkSetting;
use App\Models\ProductMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WatermarkController extends Controller
{
    /**
     * Display watermark settings page
     */
    public function index()
    {
        $settings = WatermarkSetting::first();

        // Create default if not exists
        if (!$settings) {
            $settings = WatermarkSetting::create([
                'text' => 'ArimbiStore',
                'font_family' => 'Arial',
                'font_size' => 40,
                'position' => 'center',
                'opacity' => 40,
                'color' => '#FFFFFF',
            ]);
        }

        return view('admin.watermark.index', compact('settings'));
    }

    /**
     * Update watermark settings
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'text' => 'required|string|max:255',
            'font_family' => 'required|string|max:50',
            'font_size' => 'required|integer|min:10|max:200',
            'font_size_unit' => 'sometimes|in:px,percent',
            'position' => 'required|in:center,top-left,top-right,bottom-left,bottom-right,top-center,bottom-center',
            'opacity' => 'required|integer|min:10|max:100',
            'color' => 'required|string|max:7',
            'shadow' => 'sometimes|boolean',
            'angle' => 'required|integer|min:0|max:360',
            'is_active' => 'sometimes|boolean',
        ]);

        // Handle checkboxes
        $validated['shadow'] = $request->has('shadow');
        $validated['is_active'] = $request->has('is_active');
        $validated['font_size_unit'] = $validated['font_size_unit'] ?? 'px';

        $settings = WatermarkSetting::first();
        if ($settings) {
            $settings->update($validated);
        } else {
            WatermarkSetting::create($validated);
        }

        return back()->with('success', 'Pengaturan watermark berhasil disimpan!');
    }

    /**
     * Serve watermarked image using native GD
     */
    public function serve($path)
    {
        $settings = WatermarkSetting::first();

        // If watermark not active, serve original
        if (!$settings || !$settings->is_active) {
            $originalPath = storage_path('app/public/products/' . $path);
            if (file_exists($originalPath)) {
                return response()->file($originalPath);
            }
            abort(404);
        }

        // Check if watermarked version exists in cache
        $watermarkedPath = storage_path('app/public/products/watermarked/' . $path);
        if (file_exists($watermarkedPath)) {
            return response()->file($watermarkedPath);
        }

        // Generate watermark using native GD
        $originalPath = storage_path('app/public/products/' . $path);
        if (!file_exists($originalPath)) {
            abort(404);
        }

        try {
            // Load image
            $imageInfo = getimagesize($originalPath);
            $mimeType = $imageInfo['mime'];

            switch ($mimeType) {
                case 'image/jpeg':
                    $image = imagecreatefromjpeg($originalPath);
                    break;
                case 'image/png':
                    $image = imagecreatefrompng($originalPath);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                    break;
                case 'image/webp':
                    $image = imagecreatefromwebp($originalPath);
                    break;
                default:
                    return response()->file($originalPath);
            }

            $width = imagesx($image);
            $height = imagesy($image);

            // Calculate font size
            $fontSize = $settings->font_size_unit === 'percent'
                ? ($width * $settings->font_size) / 100
                : $settings->font_size;

            // Parse color
            $hex = str_replace('#', '', $settings->color);
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
            $alpha = 127 - (127 * ($settings->opacity / 100));

            // Create color with alpha
            $color = imagecolorallocatealpha($image, $r, $g, $b, $alpha);

            // Calculate position
            $positions = [
                'center' => ['x' => $width / 2, 'y' => $height / 2],
                'top-left' => ['x' => 30, 'y' => 30 + $fontSize],
                'top-center' => ['x' => $width / 2, 'y' => 30 + $fontSize],
                'top-right' => ['x' => $width - 30, 'y' => 30 + $fontSize],
                'bottom-left' => ['x' => 30, 'y' => $height - 30],
                'bottom-center' => ['x' => $width / 2, 'y' => $height - 30],
                'bottom-right' => ['x' => $width - 30, 'y' => $height - 30],
            ];

            $pos = $positions[$settings->position] ?? $positions['center'];

            // Add shadow if enabled
            if ($settings->shadow) {
                $shadowColor = imagecolorallocatealpha($image, 0, 0, 0, 50);
                imagettftext($image, $fontSize, $settings->angle, $pos['x'] + 2, $pos['y'] + 2, $shadowColor, $this->getFontPath(), $settings->text);
            }

            // Add watermark text
            imagettftext($image, $fontSize, $settings->angle, $pos['x'], $pos['y'], $color, $this->getFontPath(), $settings->text);

            // Save to cache
            $cacheDir = dirname($watermarkedPath);
            if (!file_exists($cacheDir)) {
                mkdir($cacheDir, 0755, true);
            }

            switch ($mimeType) {
                case 'image/jpeg':
                    imagejpeg($image, $watermarkedPath, 90);
                    break;
                case 'image/png':
                    imagepng($image, $watermarkedPath, 8);
                    break;
                case 'image/webp':
                    imagewebp($image, $watermarkedPath, 90);
                    break;
            }

            imagedestroy($image);

            return response()->file($watermarkedPath);
        } catch (\Exception $e) {
            // Fallback to original if watermark fails
            return response()->file($originalPath);
        }
    }

    /**
     * Get TTF font path (fallback to system fonts)
     */
    private function getFontPath()
    {
        // Try different font paths
        $fontPaths = [
            'C:\Windows\Fonts\arial.ttf',
            'C:\Windows\Fonts\verdana.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
        ];

        foreach ($fontPaths as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        // Fallback to built-in GD font (no TTF)
        return 5;
    }

    /**
     * Clear watermark cache
     */
    public function clearCache()
    {
        $watermarkedDir = storage_path('app/public/products/watermarked');
        if (is_dir($watermarkedDir)) {
            $files = glob($watermarkedDir . '/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file);
                }
            }
        }

        return back()->with('success', 'Cache watermark berhasil dibersihkan!');
    }

    /**
     * Cleanup unused images
     */
    public function cleanupUnused()
    {
        // Get all file paths in use
        $usedPaths = ProductMedia::pluck('file_path')->toArray();

        // Scan storage directory
        $productsDir = storage_path('app/public/products');
        if (!is_dir($productsDir)) {
            return back()->with('error', 'Directory products tidak ditemukan!');
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
                }
            }
        }

        return back()->with('success', "Berhasil menghapus {$deletedCount} gambar yang tidak digunakan!");
    }
}
