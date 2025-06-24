<?php

namespace App\Http\Controllers;

use App\Models\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class RequestController extends Controller
{
    // Middleware di constructor
    public function __construct()
    {
        $this->middleware('role:requestor')->except(['show', 'trash', 'restore']);
        $this->middleware('role:admin')->only(['trash', 'restore']);
    }

    // Daftar request milik user
    public function index()
    {
        $requests = Request::where('created_by', Auth::id())
            ->with('approvalLogs')
            ->latest()
            ->get();

        return view('requests.index', compact('requests'));
    }

    // Form create
    public function create()
    {
        return view('requests.create');
    }

    // Simpan request baru
    public function store(\Illuminate\Http\Request $httpRequest): RedirectResponse
    {
        $validated = $this->validateRequest($httpRequest);

        $request = new Request($validated);
        $request->created_by = Auth::id();
        $request->status = Request::STATUS_DRAFT;

        if ($httpRequest->hasFile('attachment')) {
            $validated['attachment'] = $this->storeAttachment(
                $httpRequest->file('attachment')
            );
        }

        $request->save();

        return redirect()->route('requests.index')
            ->with('success', 'Request berhasil dibuat');
    }

    // Tampilkan detail request
    public function show(Request $request)
    {
        // Authorization: Requestor hanya bisa lihat miliknya
        if (Auth::user()->role === 'requestor' && $request->created_by !== Auth::id()) {
            abort(403);
        }

        return view('requests.show', compact('request'));
    }

    // Form edit
    public function edit(Request $request)
    {
        // Hanya bisa edit jika status draft
        if (!$request->isEditable()) {
            return redirect()->back()
                ->with('error', 'Request tidak bisa diedit setelah disubmit');
        }

        return view('requests.edit', compact('request'));
    }

    // Update request
    public function update(\Illuminate\Http\Request $httpRequest, Request $request): RedirectResponse
    {
        if (!$request->isEditable()) {
            abort(403, 'Request tidak bisa diubah');
        }

        $validated = $this->validateRequest($httpRequest);

        if ($httpRequest->hasFile('attachment')) {
            // Hapus file lama
            $this->deleteAttachment($request->attachment);

            // Simpan file baru
            $validated['attachment'] = $this->storeAttachment(
                $httpRequest->file('attachment')
            );
        } elseif ($httpRequest->input('remove_attachment')) {
            // Hapus attachment
            $this->deleteAttachment($request->attachment);
            $validated['attachment'] = null;
        } else {
            // Pertahankan file yang ada
            $validated['attachment'] = $request->attachment;
        }

        $request->update($validated);

        return redirect()->route('requests.index')
            ->with('success', 'Request berhasil diperbarui');
    }

    // Soft delete
    public function destroy(Request $request): RedirectResponse
    {
        // Hanya pemilik yang bisa hapus
        if ($request->created_by !== Auth::id()) {
            abort(403);
        }

        $request->delete();

        return redirect()->route('requests.index')
            ->with('success', 'Request berhasil dihapus');
    }

    // Submit draft
    public function submit(Request $request): RedirectResponse
    {
        if (!$request->isEditable()) {
            return redirect()->back()
                ->with('error', 'Request tidak bisa disubmit');
        }

        $request->update(['status' => Request::STATUS_SUBMITTED]);

        return redirect()->route('requests.index')
            ->with('success', 'Request berhasil disubmit untuk persetujuan');
    }

    // Validasi request
    private function validateRequest(\Illuminate\Http\Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'request_type' => 'required|in:cuti,atk,akses,reimbursement',
            'attachment' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png',
                'max:5120' // 5MB
            ]
        ]);
    }

    // Simpan attachment
    // app/Http/Controllers/RequestController.php

    private function storeAttachment(UploadedFile $file): string
    {
        // Generate unique filename: timestamp_randomhash.ext
        $filename = time() . '_' . bin2hex(random_bytes(8)) . '.' . $file->extension();

        // Simpan file ke disk 'attachments'
        $file->storeAs('', $filename, 'attachments');

        return $filename;
    }


    public function downloadAttachment(Request $request)
    {
        // Authorization: Hanya pemilik/approver/admin yang bisa download
        $user = Auth::user();
        $isOwner = $request->created_by === $user->id;
        $isApprover = $user->role === 'approver';
        $isAdmin = $user->role === 'admin';

        if (!$isOwner && !$isApprover && !$isAdmin) {
            abort(403, 'Unauthorized access');
        }

        if (!$request->attachment) {
            abort(404, 'File not found');
        }

        $path = Storage::disk('attachments')->path($request->attachment);

        // Ambil original extension
        $extension = pathinfo($request->attachment, PATHINFO_EXTENSION);

        // Generate nama file yang user-friendly
        $displayName = Str::slug($request->title) . '_attachment.' . $extension;

        return response()->download($path, $displayName);
    }

    private function deleteAttachment(?string $filename): void
    {
        if ($filename) {
            Storage::disk('attachments')->delete($filename);
        }
    }

    public function trash()
    {
        $requests = Request::onlyTrashed()
            ->with('creator')
            ->latest('deleted_at')
            ->get();

        return view('requests.trash', compact('requests'));
    }

    public function restore($id)
    {
        $request = Request::onlyTrashed()->findOrFail($id);
        $request->restore();

        return redirect()->route('requests.trash')
            ->with('success', 'Request berhasil direstore');
    }

    public function forceDelete($id)
    {
        if (auth()->user()->role !== 'admin') {
            abort(403);
        }
        $request = Request::onlyTrashed()->findOrFail($id);
        $this->authorize('forceDelete', $request);
        // // Hapus file attachment jika ada
        // if ($request->attachment) {
        //     Storage::disk('attachments')->delete($request->attachment);
        // }

        $request->forceDelete();

        return redirect()->route('requests.trash')
            ->with('success', 'Request dihapus permanen');
    }

    public function adminIndex()
    {
        // Pastikan hanya admin yang bisa akses
        $this->authorize('admin-access');

        $requests = Request::with(['user', 'approver'])
            ->latest()
            ->paginate(15);

        return view('admin.requests.index', compact('requests'));
    }
}