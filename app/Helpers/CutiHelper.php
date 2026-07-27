<?php

namespace App\Helpers;

use Carbon\Carbon;

class CutiHelper
{
    /**
     * Hitung hari kerja (Senin-Jumat) antara dua tanggal
     */
    public static function hitungHariKerja($tanggalMulai, $tanggalSelesai)
    {
        $start = Carbon::parse($tanggalMulai);
        $end = Carbon::parse($tanggalSelesai);
        
        $hariKerja = 0;
        $current = $start->copy();
        
        while ($current <= $end) {
            // Cek apakah hari Senin-Jumat (1-5)
            if ($current->dayOfWeek >= Carbon::MONDAY && $current->dayOfWeek <= Carbon::FRIDAY) {
                $hariKerja++;
            }
            $current->addDay();
        }
        
        return $hariKerja;
    }

    /**
     * Hitung tanggal selesai berdasarkan jumlah hari kerja
     * Misal: mulai Jumat, 1 hari kerja => selesai Jumat
     */
    public static function hitungTanggalSelesai($tanggalMulai, $jumlahHariKerja)
    {
        $start = Carbon::parse($tanggalMulai);
        $current = $start->copy();
        $hariKerjaDihitung = 0;
        
        // Jika jumlah hari kerja 0, kembalikan tanggal mulai
        if ($jumlahHariKerja <= 0) {
            return $start;
        }
        
        while ($hariKerjaDihitung < $jumlahHariKerja) {
            // Jika hari ini adalah hari kerja (Senin-Jumat)
            if ($current->dayOfWeek >= Carbon::MONDAY && $current->dayOfWeek <= Carbon::FRIDAY) {
                $hariKerjaDihitung++;
                // Jika sudah mencapai jumlah hari kerja yang diminta
                if ($hariKerjaDihitung == $jumlahHariKerja) {
                    return $current;
                }
            }
            $current->addDay();
        }
        
        return $current;
    }

    /**
     * Cek apakah tanggal adalah hari kerja
     */
    public static function isHariKerja($tanggal)
    {
        $date = Carbon::parse($tanggal);
        return $date->dayOfWeek >= Carbon::MONDAY && $date->dayOfWeek <= Carbon::FRIDAY;
    }

    /**
     * Dapatkan tanggal hari kerja berikutnya
     */
    public static function getNextHariKerja($tanggal)
    {
        $date = Carbon::parse($tanggal);
        while (!$date->isWeekday()) {
            $date->addDay();
        }
        return $date;
    }

    /**
     * Dapatkan tanggal hari kerja sebelumnya
     */
    public static function getPreviousHariKerja($tanggal)
    {
        $date = Carbon::parse($tanggal);
        while (!$date->isWeekday()) {
            $date->subDay();
        }
        return $date;
    }

    /**
     * Hitung total hari kalender antara dua tanggal
     */
    public static function hitungTotalHari($tanggalMulai, $tanggalSelesai)
    {
        $start = Carbon::parse($tanggalMulai);
        $end = Carbon::parse($tanggalSelesai);
        return $start->diffInDays($end) + 1;
    }

    /**
     * Hitung jumlah hari libur (Sabtu/Minggu) antara dua tanggal
     */
    public static function hitungHariLibur($tanggalMulai, $tanggalSelesai)
    {
        $start = Carbon::parse($tanggalMulai);
        $end = Carbon::parse($tanggalSelesai);
        
        $hariLibur = 0;
        $current = $start->copy();
        
        while ($current <= $end) {
            if ($current->isWeekend()) {
                $hariLibur++;
            }
            $current->addDay();
        }
        
        return $hariLibur;
    }
}