<?php

namespace App\Helpers;

class AngkaHelper
{
    public static function terbilang($angka)
    {
        $angka = abs((int) $angka);

        $huruf = [
            '', 'satu', 'dua', 'tiga', 'empat', 'lima',
            'enam', 'tujuh', 'delapan', 'sembilan', 'sepuluh', 'sebelas'
        ];

        if ($angka < 12) {
            return $huruf[$angka];
        }

        if ($angka < 20) {
            return self::terbilang($angka - 10) . ' belas';
        }

        if ($angka < 100) {
            return self::terbilang((int) ($angka / 10))
                . ' puluh '
                . self::terbilang($angka % 10);
        }

        if ($angka < 200) {
            return 'seratus ' . self::terbilang($angka - 100);
        }

        if ($angka < 1000) {
            return self::terbilang((int) ($angka / 100))
                . ' ratus '
                . self::terbilang($angka % 100);
        }

        return (string) $angka;
    }
}