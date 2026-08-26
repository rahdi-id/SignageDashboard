<?php

namespace App\Http\Controllers\Helpdesk;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Department;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class ConversationController extends Controller
{
    public function index()
    {
        $departments = Department::where('is_active', true)->get();
        return view('helpdesk.conversations.index', [
            'title'       => 'Conversations',
            'departments' => $departments,
        ]);
    }

    public function data(Request $request)
    {
        $query = Conversation::with('department');

        // Search by guest name
        if ($request->filled('guest_name')) {
            $query->where('guest_name', 'like', '%' . $request->guest_name . '%');
        }

        // Search by room number
        if ($request->filled('room_number')) {
            $query->where('room_number', 'like', '%' . $request->room_number . '%');
        }

        // Filter by department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $conversations = $query->latest()->get();

        // Hitung unread per conversation: pesan guest yang belum dibaca admin
        $conversations->loadCount([
            'messages as unread_count' => function ($q) {
                $q->where('sender_type', 'guest')->where('is_read', false);
            },
        ]);

        return response()->json(['data' => $conversations]);
    }

    public function show($id)
    {
        $conversation = Conversation::with(['department', 'messages'])->findOrFail($id);

        // Tandai semua pesan Guest pada conversation ini sebagai sudah dibaca
        Message::where('conversation_id', $id)
            ->where('sender_type', 'guest')
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('helpdesk.conversations.show', [
            'title'        => 'Conversation Detail',
            'conversation' => $conversation,
        ]);
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            // message boleh kosong jika ada attachment, tapi salah satu harus ada
            'message'    => 'nullable|string|max:5000',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx|max:5120',
        ]);

        // Pastikan minimal pesan teks atau file attachment ada
        if (empty($request->input('message')) && !$request->hasFile('attachment')) {
            return back()->withErrors(['message' => 'Please enter a message or attach a file.']);
        }

        $conversation = Conversation::findOrFail($id);

        $attachmentName = null;
        if ($request->hasFile('attachment')) {
            $file           = $request->file('attachment');
            $attachmentName = rand(1, 9999) . time() . '.' . $file->getClientOriginalExtension();
            File::ensureDirectoryExists(public_path('images/helpdesk'));
            $file->move(public_path('images/helpdesk'), $attachmentName);
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'admin',
            'message'         => $request->input('message') ?? '',
            'attachment'      => $attachmentName,
        ]);

        // If status is still open, move to in_progress
        if ($conversation->status === 'open') {
            $conversation->update(['status' => 'in_progress']);
        }

        return redirect()->route('helpdesk.conversations.show', $id)
            ->withSuccess('Reply sent successfully.');
    }

    public function close($id)
    {
        $conversation = Conversation::findOrFail($id);
        $conversation->update(['status' => 'closed']);

        return redirect()->route('helpdesk.conversations.show', $id)
            ->withSuccess('Conversation closed successfully.');
    }

    public function reopen($id)
    {
        $conversation = Conversation::findOrFail($id);
        $conversation->update(['status' => 'open']);

        return redirect()->route('helpdesk.conversations.show', $id)
            ->withSuccess('Conversation reopened successfully.');
    }

    public function messages($id)
    {
        $conversation = Conversation::with('messages')->findOrFail($id);

        // Heartbeat: catat waktu admin terakhir aktif
        $conversation->update(['admin_last_seen_at' => now()]);

        return response()->json([
            'status'              => $conversation->status,
            'messages'            => $conversation->messages->map(function ($message) {
                return [
                    'id'          => $message->id,
                    'sender_type' => $message->sender_type,
                    'message'     => $message->message,
                    'attachment'  => $message->attachment,
                    'created_at'  => $message->created_at->format('d M Y H:i'),
                ];
            }),
            // Kirim timestamp guest agar admin bisa tampilkan status guest
            'guest_last_seen_at'  => $conversation->guest_last_seen_at
                ? $conversation->guest_last_seen_at->toIso8601String()
                : null,
        ]);
    }
}
