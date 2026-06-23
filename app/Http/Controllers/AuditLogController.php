<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Satker;
use Carbon\Carbon;

class AuditLogController extends Controller
{
    public static function log($auditableId, $auditableType, $event, $description)
    {
        DB::table('audit_logs')->insert([
            'user_id'        => auth()->id(),
            'event'          => $event,
            'auditable_type' => $auditableType,
            'auditable_id'   => $auditableId,
            'description'    => $description,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);
    }

    public function index(Request $request)
    {
        // Query Dasar dengan Eager Loading
        $query = DB::table('audit_logs')
            ->leftJoin('users', 'audit_logs.user_id', '=', 'users.id')
            ->leftJoin('satkers', 'users.satker_id', '=', 'satkers.id')
            ->select(
                'audit_logs.*',
                'users.name as user_name',
                'users.role as user_role',
                'satkers.nama_satker as satker_name'
            );

        // 1. Filter Pencarian Keyword
        if ($request->filled('search')) {
            $query->where('audit_logs.description', 'like', '%' . $request->search . '%');
        }

        // 2. Filter Rentang Tanggal
        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('audit_logs.created_at', [$request->dari . ' 00:00:00', $request->sampai . ' 23:59:59']);
        }

        // 3. Filter User & Role
        if ($request->filled('user_id')) $query->where('audit_logs.user_id', $request->user_id);
        if ($request->filled('role')) $query->where('users.role', $request->role);
        if ($request->filled('satker_id')) $query->where('users.satker_id', $request->satker_id);

        // 4. Filter Jenis & Modul
        if ($request->filled('event')) $query->where('audit_logs.event', $request->event);
        if ($request->filled('module')) $query->where('audit_logs.auditable_type', 'like', '%' . $request->module . '%');

        $logs = $query->latest('audit_logs.created_at')->paginate(15)->withQueryString();

        // Statistik Ringkas
        $stats = [
            'today' => DB::table('audit_logs')->whereDate('created_at', Carbon::today())->count(),
            'week'  => DB::table('audit_logs')->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->count(),
            'month' => DB::table('audit_logs')->whereMonth('created_at', Carbon::now()->month)->count(),
            'top_user' => DB::table('audit_logs')
                ->join('users', 'audit_logs.user_id', '=', 'users.id')
                ->select('users.name', DB::raw('count(*) as total'))
                ->groupBy('users.id', 'users.name')
                ->orderByDesc('total')
                ->first()
        ];

        // Kelompokkan log berdasarkan tanggal untuk timeline
        $groupedLogs = $logs->getCollection()->groupBy(function($log) {
            $date = Carbon::parse($log->created_at);
            if ($date->isToday()) return 'Hari Ini';
            if ($date->isYesterday()) return 'Kemarin';
            if ($date->greaterThan(Carbon::now()->startOfWeek())) return 'Minggu Ini';
            return $date->translatedFormat('d F Y');
        });

        $users = User::orderBy('name')->get();
        $satkers = Satker::orderBy('nama_satker')->get();
        $modules = ['PengajuanLelang', 'Perkara', 'Barang', 'Lelang', 'User', 'LaporanLelang'];

        return view('admin.aktivitas.index', compact('logs', 'groupedLogs', 'stats', 'users', 'satkers', 'modules'));
    }

    /**
     * Helper untuk warna badge berdasarkan event
     */
    public static function getBadgeColor($event)
    {
        return match (strtolower($event)) {
            'created'         => 'success',
            'updated'         => 'info',
            'deleted'         => 'danger',
            'approved', 'rejected', 'revision' => 'primary',
            'scheduled'       => 'warning',
            'cancelled'       => 'dark',
            'login', 'logout' => 'secondary',
            'system', 'scheduler' => 'purple',
            default           => 'light',
        };
    }

    /**
     * Helper icon berdasarkan modul
     */
    public static function getModuleIcon($module)
    {
        if (! $module) return 'fa-dot-circle';

        if (str_contains($module, 'Pengajuan')) return 'fa-file-invoice';
        if (str_contains($module, 'Perkara'))   return 'fa-balance-scale';
        if (str_contains($module, 'Barang'))    return 'fa-box';
        if (str_contains($module, 'Lelang'))    return 'fa-gavel';
        if (str_contains($module, 'User'))      return 'fa-user-cog';
        return 'fa-dot-circle';
    }
}
