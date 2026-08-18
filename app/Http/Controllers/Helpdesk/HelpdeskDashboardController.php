<?php

namespace App\Http\Controllers\Helpdesk;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Department;
use Illuminate\Support\Facades\DB;

class HelpdeskDashboardController extends Controller
{
    public function index()
    {
        $totalOpen       = Conversation::where('status', 'open')->count();
        $totalInProgress = Conversation::where('status', 'in_progress')->count();
        $totalClosed     = Conversation::where('status', 'closed')->count();
        $totalToday      = Conversation::whereDate('created_at', today())->count();

        // Ticket count grouped by department
        $ticketByDepartment = Department::withCount('conversations')
            ->where('is_active', true)
            ->get();

        // 10 most recent conversations
        $recentConversations = Conversation::with('department')
            ->latest()
            ->take(10)
            ->get();

        return view('helpdesk.dashboard', [
            'title'              => 'Helpdesk Dashboard',
            'totalOpen'          => $totalOpen,
            'totalInProgress'    => $totalInProgress,
            'totalClosed'        => $totalClosed,
            'totalToday'         => $totalToday,
            'ticketByDepartment' => $ticketByDepartment,
            'recentConversations' => $recentConversations,
        ]);
    }
}
