<?php

namespace App\Helpers;

use App\Models\BPR;
use Illuminate\Support\Facades\Storage;

class AppHelper
{
    /**
     * Get BPR logo URL - dengan multiple fallback
     */
    public static function getLogo()
    {
        $bpr = BPR::first();
        
        if (!$bpr || empty($bpr->logo)) {
            return null;
        }

        $logoPath = $bpr->logo;

        // 1. Coba dari storage (via symlink)
        if (Storage::disk('public')->exists($logoPath)) {
            return asset('storage/' . $logoPath);
        }

        // 2. Coba dari public/images (backup)
        $filename = basename($logoPath);
        if (file_exists(public_path('images/' . $filename))) {
            return asset('images/' . $filename);
        }

        // 3. Coba dari public/storage (alternative)
        if (file_exists(public_path('storage/' . $logoPath))) {
            return asset('storage/' . $logoPath);
        }

        return null;
    }

    /**
     * Get BPR logo with default fallback
     */
    public static function getLogoOrDefault()
    {
        $logo = self::getLogo();
        
        if ($logo) {
            return $logo;
        }
        
        return asset('images/default-logo.png');
    }

    /**
     * Get BPR logo untuk PDF (menggunakan path absolut)
     */
    public static function getLogoPdf()
    {
        $bpr = BPR::first();
        
        if (!$bpr || empty($bpr->logo)) {
            return public_path('images/default-logo.png');
        }

        $logoPath = $bpr->logo;

        // 1. Coba dari storage
        $storagePath = storage_path('app/public/' . $logoPath);
        if (file_exists($storagePath)) {
            return $storagePath;
        }

        // 2. Coba dari public
        $publicPath = public_path('storage/' . $logoPath);
        if (file_exists($publicPath)) {
            return $publicPath;
        }

        // 3. Coba dari images
        $imagesPath = public_path('images/' . basename($logoPath));
        if (file_exists($imagesPath)) {
            return $imagesPath;
        }

        return public_path('images/default-logo.png');
    }

    /**
     * Get logo sebagai base64 (untuk PDF inline)
     */
    public static function getLogoBase64()
    {
        $path = self::getLogoPdf();
        
        if (file_exists($path)) {
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = file_get_contents($path);
            return 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
        
        return null;
    }

    /**
     * Check if BPR has logo
     */
    public static function hasLogo()
    {
        $bpr = BPR::first();
        return $bpr && !empty($bpr->logo);
    }

    /**
     * Get BPR name
     */
    public static function getBprName()
    {
        $bpr = BPR::first();
        return $bpr ? $bpr->nama_bpr : 'BPRS Amanah Bangsa';
    }

    /**
     * Get BPR data lengkap
     */
    public static function getBpr()
    {
        return BPR::first();
    }

    /**
     * Get favicon URL (for browser tab icon)
     */
    public static function getFavicon()
    {
        $logo = self::getLogo();
        
        if ($logo) {
            return $logo;
        }
        
        return asset('favicon.ico');
    }

    /**
     * Format tanggal Indonesia
     */
    public static function formatDate($date, $format = 'd F Y')
    {
        if (!$date) {
            return '-';
        }
        
        $months = [
            1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
            5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
            9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
        ];
        
        $dateObj = \Carbon\Carbon::parse($date);
        
        $result = $dateObj->format($format);
        
        // Ganti bulan Inggris ke Indonesia
        foreach ($months as $num => $name) {
            $result = str_replace($dateObj->format('F'), $name, $result);
        }
        
        return $result;
    }

    /**
     * Format rupiah
     */
    public static function formatRupiah($amount)
    {
        if (!$amount) {
            return 'Rp 0';
        }
        
        return 'Rp ' . number_format($amount, 0, ',', '.');
    }

    /**
     * Get status badge HTML
     */
    public static function statusBadge($status, $customColors = [])
    {
        $colors = array_merge([
            'aktif' => 'success',
            'nonaktif' => 'danger',
            'pending' => 'warning',
            'disetujui' => 'success',
            'ditolak' => 'danger',
            'draft' => 'secondary',
            'active' => 'success',
            'closed' => 'danger',
        ], $customColors);
        
        $color = $colors[$status] ?? 'secondary';
        
        $labels = [
            'aktif' => 'Aktif',
            'nonaktif' => 'Non Aktif',
            'pending' => 'Pending',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            'draft' => 'Draft',
            'active' => 'Aktif',
            'closed' => 'Ditutup',
        ];
        
        $label = $labels[$status] ?? ucfirst($status);
        
        return '<span class="badge badge-' . $color . '">' . $label . '</span>';
    }
}