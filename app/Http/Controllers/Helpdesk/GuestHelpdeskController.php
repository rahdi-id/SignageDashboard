<?php

namespace App\Http\Controllers\Helpdesk;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Department;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class GuestHelpdeskController extends Controller
{
    /**
     * Show the guest helpdesk form.
     * Accessible via QR code — no auth required.
     */
    public function form()
    {
        $departments = Department::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        return view('helpdesk.guest.form', [
            'departments' => $departments,
        ]);
    }

    /**
     * Handle guest form submission.
     * Creates a new Conversation and redirects to guest chat page.
     */
    public function start(Request $request)
    {
        $request->validate([
            'guest_name'    => 'required|string|max:100',
            'room_number'   => 'required|string|max:20',
            'department_id' => 'required|integer|exists:departments,id',
        ]);

        // Verify the selected department is active
        $department = Department::where('id', $request->department_id)
            ->where('is_active', true)
            ->firstOrFail();

        $conversation = Conversation::create([
            'guest_name'    => $request->guest_name,
            'room_number'   => $request->room_number,
            'department_id' => $department->id,
            'status'        => 'open',
            'priority'      => 'medium',
        ]);

        // Create the first message as an opening message from the guest
        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'guest',
            'message'         => 'Hi, I need assistance.',
            'attachment'      => null,
        ]);

        return redirect()->route('guest.helpdesk.chat', $conversation->id);
    }

    /**
     * Show the guest chat page.
     * Accessible without auth — guest identifies via room_number in session.
     */
    public function chat($id)
    {
        $conversation = Conversation::with(['department', 'messages'])
            ->findOrFail($id);

        return view('helpdesk.guest.chat', [
            'conversation' => $conversation,
        ]);
    }

    /**
     * Handle guest sending a new message in an existing conversation.
     * Supports both regular form POST and AJAX POST (returns JSON when requested).
     */
    public function send(Request $request, $id)
    {
        $request->validate([
            // message boleh kosong jika ada attachment
            'message'    => 'nullable|string|max:2000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp|max:5120',
        ]);

        // Minimal satu dari message atau attachment harus ada
        if (empty($request->input('message')) && !$request->hasFile('attachment')) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Please enter a message or attach an image.'], 422);
            }
            return back()->withErrors(['message' => 'Please enter a message or attach an image.']);
        }

        $conversation = Conversation::findOrFail($id);

        if ($conversation->status === 'closed') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'This conversation is closed.'], 422);
            }
            return redirect()->route('guest.helpdesk.chat', $id)
                ->withErrors(['message' => 'This conversation is closed.']);
        }

        // Handle file upload
        $attachmentName = null;
        if ($request->hasFile('attachment')) {
            $file           = $request->file('attachment');
            $attachmentName = uniqid('guest_', true) . '.' . $file->getClientOriginalExtension();
            File::ensureDirectoryExists(public_path('images/helpdesk'));
            $file->move(public_path('images/helpdesk'), $attachmentName);
        }

        $message = Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'guest',
            'message'         => $request->input('message') ?? '',
            'attachment'      => $attachmentName,
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'id'          => $message->id,
                'sender_type' => $message->sender_type,
                'message'     => $message->message,
                'attachment'  => $message->attachment,
                'created_at'  => $message->created_at->format('d M Y, H:i'),
            ], 201);
        }

        return redirect()->route('guest.helpdesk.chat', $id);
    }

    /**
     * Polling endpoint — returns all messages for a conversation as JSON.
     * Used by the guest chat page to check for new messages every few seconds.
     * Also updates guest_last_seen_at as a heartbeat and returns admin_last_seen_at.
     * GET /guest/helpdesk/chat/{id}/messages
     */
    public function messages($id)
    {
        $conversation = Conversation::with('messages')
            ->findOrFail($id);

        // Heartbeat: catat waktu guest terakhir aktif
        $conversation->update(['guest_last_seen_at' => now()]);

        $messages = $conversation->messages->map(function ($msg) {
            return [
                'id'          => $msg->id,
                'sender_type' => $msg->sender_type,
                'message'     => $msg->message,
                'attachment'  => $msg->attachment,
                'created_at'  => $msg->created_at->format('d M Y, H:i'),
            ];
        });

        return response()->json([
            'status'             => $conversation->status,
            'messages'           => $messages,
            // Kirim timestamp admin agar guest bisa tampilkan status admin
            'admin_last_seen_at' => $conversation->admin_last_seen_at
                ? $conversation->admin_last_seen_at->toIso8601String()
                : null,
        ]);
    }
}
