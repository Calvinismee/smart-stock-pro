<?php

namespace App\Http\Controllers;

use App\Models\ErrorLog;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ErrorLogController extends Controller
{
    public function index(Request $request)
    {
        $query = ErrorLog::query();
        if ($severity = $request->input('severity')) { $query->where('severity', $severity); }
        if ($request->input('unresolved')) { $query->unresolved(); }
        $query->orderBy('created_at','desc');

        return Inertia::render('ErrorLogs/Index', [
            'logs' => $query->paginate(20)->withQueryString(),
            'filters' => $request->only(['severity','unresolved']),
        ]);
    }

    public function resolve(ErrorLog $errorLog)
    {
        $errorLog->update(['resolved_at' => now()]);
        return back()->with('success','Error ditandai resolved.');
    }
}
