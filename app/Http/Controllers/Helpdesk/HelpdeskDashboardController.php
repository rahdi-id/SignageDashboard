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

        // Chat count grouped by department
        $chatByDepartment = Department::withCount('conversations')
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
            'chatByDepartment'   => $chatByDepartment,
            'recentConversations' => $recentConversations,
        ]);
    }

    /**
     * GET /helpdesk/stats
     * JSON endpoint untuk polling dashboard.
     * Mengembalikan stat cards + 10 recent conversations + unread_count per conversation.
     */
    public function stats()
    {
        $totalOpen       = Conversation::where('status', 'open')->count();
        $totalInProgress = Conversation::where('status', 'in_progress')->count();
        $totalClosed     = Conversation::where('status', 'closed')->count();
        $totalToday      = Conversation::whereDate('created_at', today())->count();

        $recentConversations = Conversation::with('department')
            ->withCount([
                'messages as unread_count' => function ($q) {
                    $q->where('sender_type', 'guest')->where('is_read', false);
                },
            ])
            ->latest()
            ->take(10)
            ->get()
            ->map(function ($conv) {
                return [
                    'id'           => $conv->id,
                    'guest_name'   => $conv->guest_name,
                    'room_number'  => $conv->room_number,
                    'department'   => $conv->department->name ?? '-',
                    'status'       => $conv->status,
                    'priority'     => $conv->priority,
                    'created_at'   => $conv->created_at->format('d M Y H:i'),
                    'unread_count' => $conv->unread_count,
                ];
            });

        return response()->json([
            'totalOpen'           => $totalOpen,
            'totalInProgress'     => $totalInProgress,
            'totalClosed'         => $totalClosed,
            'totalToday'          => $totalToday,
            'recentConversations' => $recentConversations,
        ]);
    }
}
