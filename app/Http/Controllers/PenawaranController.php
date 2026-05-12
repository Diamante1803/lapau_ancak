<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\Pembeli;
use App\Models\Lelang;
use App\Models\Penawaran;
use App\Mail\MagicLinkMail;
use Carbon\Carbon;

class PenawaranController extends Controller
{
    // =============================================
    // STEP 1: Pembeli minta magic link
    // =============================================
    public function requestMagicLink(Request $request, Lelang $lelang)
    {
        $request->validate([
            'nama'  => 'required|string|max:255',
            'no_hp' => 'required|string|max:20',
            'email' => 'required|email',
        ], [
            'nama.required'  => 'Nama wajib diisi.',
            'no_hp.required' => 'Nomor HP wajib diisi.',
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
        ]);

        // Cek apakah lelang masih aktif
        if ($lelang->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Lelang ini sudah tidak aktif.'
            ], 422);
        }

        // Cek apakah sudah punya token valid hari ini
        $pembeli = Pembeli::where('email', $request->email)->first();

        if ($pembeli && $pembeli->magic_token && 
            $pembeli->token_expired_at && 
            Carbon::now()->lt($pembeli->token_expired_at)) {

            // Token masih valid — kirim ulang email saja
            $magicUrl = route('public.verify', [
                'token' => $pembeli->magic_token,
                'email' => $pembeli->email,
                'lelang'=> $lelang->id,
            ]);

            Mail::to($pembeli->email)->send(
                new MagicLinkMail($magicUrl, $pembeli->nama)
            );

            return response()->json([
                'success' => true,
                'message' => 'Link verifikasi telah dikirim ulang ke email Anda.'
            ]);
        }

        // Buat atau update pembeli
        $pembeli = Pembeli::updateOrCreate(
            ['email' => $request->email],
            [
                'nama'  => $request->nama,
                'no_hp' => $request->no_hp,
            ]
        );

        // Generate token baru
        $token = Str::random(64);

        // Expired jam 23:59 hari ini
        $expired = Carbon::today()->endOfDay();

        $pembeli->update([
            'magic_token'      => $token,
            'token_expired_at' => $expired,
        ]);

        // Buat magic URL
        $magicUrl = route('public.verify', [
            'token' => $token,
            'email' => $pembeli->email,
            'lelang'=> $lelang->id,
        ]);

        // Kirim email
        Mail::to($pembeli->email)->send(
            new MagicLinkMail($magicUrl, $pembeli->nama)
        );

        return response()->json([
            'success' => true,
            'message' => 'Link verifikasi telah dikirim ke ' . $pembeli->email . '. Cek inbox atau spam Anda.'
        ]);
    }

    // =============================================
    // STEP 2: Verifikasi token dari link email
    // =============================================
    public function verifyMagicLink(Request $request)
    {
        $token   = $request->query('token');
        $email   = $request->query('email');
        $lelangId = $request->query('lelang');

        // Cari pembeli
        $pembeli = Pembeli::where('email', $email)
            ->where('magic_token', $token)
            ->first();

        // Validasi token
        if (!$pembeli) {
            return redirect()->route('public.detail', $lelangId)
                ->with('error', 'Link verifikasi tidak valid.');
        }

        if (Carbon::now()->gt($pembeli->token_expired_at)) {
            return redirect()->route('public.detail', $lelangId)
                ->with('error', 'Link verifikasi sudah kedaluwarsa. Silakan minta link baru.');
        }

        // Update verified_at jika belum
        if (!$pembeli->verified_at) {
            $pembeli->update(['verified_at' => now()]);
        }

        // Simpan ke session — valid sampai akhir hari
        session([
            'verified_pembeli_id'   => $pembeli->id,
            'verified_pembeli_nama' => $pembeli->nama,
            'verified_expired'      => Carbon::today()->endOfDay()->toIso8601String(),
        ]);

        return redirect()->route('public.detail', $lelangId)
            ->with('success', 'Verifikasi berhasil! Anda sekarang bisa mengajukan penawaran.');
    }

    // =============================================
    // STEP 3: Submit penawaran
    // =============================================
    public function submitPenawaran(Request $request, Lelang $lelang)
    {
        // Cek session verifikasi
        $pembeliId = session('verified_pembeli_id');
        $expired   = session('verified_expired');

        if (!$pembeliId || !$expired || Carbon::now()->gt(Carbon::parse($expired))) {
            return response()->json([
                'success' => false,
                'message' => 'Sesi verifikasi habis. Silakan verifikasi ulang.',
                'reVerify'=> true,
            ], 401);
        }

        // Cek lelang masih aktif
        if ($lelang->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Lelang ini sudah tidak aktif.'
            ], 422);
        }

        $request->validate([
            'nilai_penawaran' => 'required|numeric|min:' . ($lelang->harga_awal ?? 1),
        ]);

        $nilaiPenawaran = (float) $request->nilai_penawaran;
        try {

            $result = DB::transaction(function () use (
                $nilaiPenawaran,
                $lelang,
                $pembeliId
            ) {

                // 🔒 Lock row lelang agar tidak bentrok
                $lelang = Lelang::lockForUpdate()->find($lelang->id);

                // Ambil harga terkini dari database
                $hargaSekarang = $lelang->harga_tertinggi
                    ?? $lelang->harga_awal;

                // Minimal kenaikan
                $minPenawaran = $hargaSekarang + 10000;

                // Validasi nominal
                if ($nilaiPenawaran < $minPenawaran) {
                    throw new \Exception(
                        'Penawaran minimal Rp ' .
                        number_format($minPenawaran, 0, ',', '.')
                    );
                }

                // Simpan penawaran
                Penawaran::create([
                    'lelang_id'       => $lelang->id,
                    'pembeli_id'      => $pembeliId,
                    'nilai_penawaran' => $nilaiPenawaran,
                ]);

                // Update harga tertinggi
                $lelang->update([
                    'harga_tertinggi'       => $nilaiPenawaran,
                    'pemenang_sementara_id' => $pembeliId,
                ]);

                return [
                    'harga_tertinggi' => $nilaiPenawaran,
                    'min_berikutnya'  => $nilaiPenawaran + 10000,
                ];
            });

            return response()->json([
                'success'         => true,
                'message'         => 'Penawaran berhasil dikirim!',
                'harga_tertinggi' => $result['harga_tertinggi'],
                'harga_formatted' => 'Rp ' .
                    number_format($result['harga_tertinggi'], 0, ',', '.'),
                'min_berikutnya'  => $result['min_berikutnya'],
            ]);

        } catch (\Exception $e) {

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function pollingPenawaran(Lelang $lelang)
    {
        $penawarans = $lelang->penawarans()
            ->with('pembeli')
            ->orderByDesc('nilai_penawaran')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,

            'updated_at' => optional(
                $penawarans->first()
            )->updated_at?->timestamp,

            'html' => view('partials.penawaran-list', [
                'penawarans' => $penawarans
            ])->render(),

            'harga_tertinggi' => $lelang->harga_tertinggi,

            'min_penawaran' =>
                ($lelang->harga_tertinggi ?? $lelang->harga_awal) + 10000,
        ]);
    }
}