<?php

namespace App\Helpers;

use App\Models\BPR;

class Helper
{
    /**
     * Get BPR logo URL
     */
    public static function getLogo()
    {
        $bpr = BPR::first();
        
        if ($bpr && $bpr->logo) {
            return asset('storage/' . $bpr->logo);
        }
        
        return null;
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
     * Get BPR data
     */
    public static function getBpr()
    {
        return BPR::first();
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
     * Check if BPR has logo
     */
    public static function hasLogo()
    {
        $bpr = BPR::first();
        return $bpr && $bpr->logo ? true : false;
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
        
        // Default favicon jika belum ada logo
        return asset('favicon.ico');
    }
}