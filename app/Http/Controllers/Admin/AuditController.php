<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $logs = AuditLog::with('actor')->latest('id')->take(200)->get();

        return view('admin.audit', ['user' => $request->user(), 'logs' => $logs]);
    }
}
