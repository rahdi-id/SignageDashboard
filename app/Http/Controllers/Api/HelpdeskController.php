<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Department;
use App\Models\Message;
use Illuminate\Http\Request;

class HelpdeskController extends Controller
{
    /**
     * GET /api/helpdesk/departments
     * List all active departments.
     * Flutter uses this to populate department picker.
     */
    public function departments()
    {
        $departments = Department::where('is_active', true)
            ->select('id', 'name', 'slug', 'description')
            ->get();

        return response()->json([
            'status'  => true,
            'message' => 'Success get departments',
            'data'    => $departments,
        ]);
    }

    /**
     * POST /api/helpdesk/conversations
     * Guest creates a new helpdesk chat.
     *
     * Request body:
     *   - guest_name    : string (required)
     *   - room_number   : string (required)
     *   - department_id : integer (required)
     *   - priority      : string low|medium|high (optional, default: medium)
     *   - message       : string (required) — first message in the conversation
     */
    public function createConversation(Request $request)
    {
        $validated = $request->validate([
            'guest_name'    => 'required|string|max:100',
            'room_number'   => 'required|string|max:20',
            'department_id' => 'required|integer|exists:departments,id',
            'priority'      => 'nullable|in:low,medium,high',
            'message'       => 'required|string',
        ]);

        $conversation = Conversation::create([
            'guest_name'    => $validated['guest_name'],
            'room_number'   => $validated['room_number'],
            'department_id' => $validated['department_id'],
            'priority'      => $validated['priority'] ?? 'medium',
            'status'        => 'open',
        ]);

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'guest',
            'message'         => $validated['message'],
            'attachment'      => null,
        ]);

        $conversation->load('department');

        return response()->json([
            'status'  => true,
            'message' => 'Chat created successfully',
            'data'    => [
                'conversation_id' => $conversation->id,
                'guest_name'      => $conversation->guest_name,
                'room_number'     => $conversation->room_number,
                'department'      => $conversation->department->name,
                'status'          => $conversation->status,
                'priority'        => $conversation->priority,
                'created_at'      => $conversation->created_at,
            ],
        ], 201);
    }

    /**
     * GET /api/helpdesk/conversations/{id}
     * Guest views their conversation detail with all messages.
     *
     * Query params:
     *   - room_number : string (required for basic identity verification)
     */
    public function showConversation(Request $request, $id)
    {
        $request->validate([
            'room_number' => 'required|string',
        ]);

        $conversation = Conversation::with(['department', 'messages'])
            ->where('id', $id)
            ->where('room_number', $request->room_number)
            ->first();

        if (!$conversation) {
            return response()->json([
                'status'  => false,
                'message' => 'Conversation not found or room number does not match',
            ], 404);
        }

        $messages = $conversation->messages->map(function ($msg) {
            return [
                'id'          => $msg->id,
                'sender_type' => $msg->sender_type,
                'message'     => $msg->message,
                'attachment'  => $msg->attachment
                    ? url('/api/image/helpdesk/' . $msg->attachment)
                    : null,
                'created_at'  => $msg->created_at,
            ];
        });

        return response()->json([
            'status'  => true,
            'message' => 'Success get conversation',
            'data'    => [
                'id'          => $conversation->id,
                'guest_name'  => $conversation->guest_name,
                'room_number' => $conversation->room_number,
                'department'  => $conversation->department->name,
                'status'      => $conversation->status,
                'priority'    => $conversation->priority,
                'created_at'  => $conversation->created_at,
                'messages'    => $messages,
            ],
        ]);
    }

    /**
     * POST /api/helpdesk/conversations/{id}/messages
     * Guest sends a follow-up message in an existing conversation.
     *
     * Request body:
     *   - room_number : string (required for basic identity verification)
     *   - message     : string (required)
     */
    public function sendMessage(Request $request, $id)
    {
        $request->validate([
            'room_number' => 'required|string',
            'message'     => 'required|string',
        ]);

        $conversation = Conversation::where('id', $id)
            ->where('room_number', $request->room_number)
            ->first();

        if (!$conversation) {
            return response()->json([
                'status'  => false,
                'message' => 'Conversation not found or room number does not match',
            ], 404);
        }

        if ($conversation->status === 'closed') {
            return response()->json([
                'status'  => false,
                'message' => 'This conversation is already closed',
            ], 422);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'guest',
            'message'         => $request->message,
            'attachment'      => null,
        ]);

        // Reopen if it was closed or update to in_progress if still open
        if ($conversation->status === 'open') {
            $conversation->update(['status' => 'in_progress']);
        }

        return response()->json([
            'status'  => true,
            'message' => 'Message sent successfully',
            'data'    => [
                'id'          => $message->id,
                'sender_type' => $message->sender_type,
                'message'     => $message->message,
                'created_at'  => $message->created_at,
            ],
        ], 201);
    }
}
