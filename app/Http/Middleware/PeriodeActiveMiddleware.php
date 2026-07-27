<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\PeriodePenilaian;
use Symfony\Component\HttpFoundation\Response;

class PeriodeActiveMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $periodeAktif = PeriodePenilaian::where('is_active', true)->first();
        
        if (!$periodeAktif) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Belum ada periode penilaian yang aktif.'
                ], 403);
            }
            
            return redirect()->back()
                ->with('warning', 'Belum ada periode penilaian yang aktif. Silakan aktifkan periode terlebih dahulu.');
        }

        // Share ke semua view
        view()->share('periodeAktif', $periodeAktif);

        return $next($request);
    }
}