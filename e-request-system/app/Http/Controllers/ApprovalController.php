<?php

namespace App\Http\Controllers;

use App\Models\Request;
use App\Models\ApprovalLog;
use Illuminate\Http\Request as HttpRequest;
use Illuminate\Support\Facades\Auth;

class ApprovalController extends Controller
{
    public function __construct()
    {
        $this->middleware('role:approver');
    }

    public function index()
    {
        $requests = Request::where('status', Request::STATUS_SUBMITTED)
            ->with('creator')
            ->latest()
            ->get();

        return view('approvals.index', compact('requests'));
    }

    public function show($id)
    {
        $request = Request::findOrFail($id);

        // Pastikan request dalam status submitted
        if ($request->status !== Request::STATUS_SUBMITTED) {
            return redirect()->route('approvals.index')
                ->with('error', 'Request ini sudah diproses');
        }

        return view('approvals.show', compact('request'));
    }

    // Proses approval
    public function approve(HttpRequest $httpRequest, $id)
    {
        $requestModel = Request::findOrFail($id);

        $validated = $httpRequest->validate([
            'notes' => 'nullable|string|max:500'
        ]);

        // Pastikan request masih bisa diproses
        if ($requestModel->status !== Request::STATUS_SUBMITTED) {
            return redirect()->route('approvals.index')
                ->with('error', 'Request ini sudah diproses sebelumnya');
        }

        // Update status request
        $requestModel->update(['status' => Request::STATUS_APPROVED]);

        // Catat log approval
        ApprovalLog::create([
            'request_id' => $requestModel->id,
            'user_id' => Auth::id(),
            'action' => ApprovalLog::ACTION_APPROVE,
            'notes' => $validated['notes'] ?? null
        ]);

        return redirect()->route('approvals.index')
            ->with('success', 'Request berhasil disetujui');
    }

    // Proses reject
    public function reject(HttpRequest $httpRequest, $id)
    {
        $requestModel = Request::findOrFail($id);

        $validated = $httpRequest->validate([
            'notes' => 'required|string|max:500'
        ]);

        // Pastikan request masih bisa diproses
        if ($requestModel->status !== Request::STATUS_SUBMITTED) {
            return redirect()->route('approvals.index')
                ->with('error', 'Request ini sudah diproses sebelumnya');
        }

        $requestModel->update(['status' => Request::STATUS_REJECTED]);

        ApprovalLog::create([
            'request_id' => $requestModel->id,
            'user_id' => Auth::id(),
            'action' => ApprovalLog::ACTION_REJECT,
            'notes' => $validated['notes']
        ]);

        return redirect()->route('approvals.index')
            ->with('success', 'Request berhasil ditolak');
    }
}
