<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $query = AuditLog::with('user:id,name');
        if ($s = $request->input('search')) {
            $query->where(fn($q)=>$q->where('description','ilike',"%{$s}%")->orWhere('action','ilike',"%{$s}%"));
        }
        if ($module = $request->input('module')) { $query->where('module', $module); }
        if ($action = $request->input('action')) { $query->where('action', $action); }
        $query->orderBy('created_at','desc');

        return Inertia::render('AuditLogs/Index', [
            'logs' => $query->paginate(20)->withQueryString(),
            'filters' => $request->only(['search','module','action']),
            'modules' => AuditLog::select('module')->distinct()->pluck('module'),
        ]);
    }
}
