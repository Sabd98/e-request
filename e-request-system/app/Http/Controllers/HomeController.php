<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Request as RequestModel; 
use App\Models\User;

class HomeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $data = [];

        if ($user->role === 'admin') {
            $data['recentRequests'] = RequestModel::with('creator')
                ->latest()
                ->take(5)
                ->get();
        } elseif ($user->role === 'approver') {
            $data['pendingApprovals'] = RequestModel::where('status', 'submitted')
                ->with('creator')
                ->latest()
                ->get();
        } else {
            $status = request('status', 'all');

            $query = RequestModel::where('created_by', $user->id);

            if ($status !== 'all') {
                $query->where('status', $status);
            }

            $data['userRequests'] = $query->latest()->paginate(10);
        }

        return view('home', $data);
    }
}
