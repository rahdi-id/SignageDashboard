<?php

namespace App\Http\Controllers\Helpdesk;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use App\Models\Department;
use App\Models\Message;
use Illuminate\Http\Request;

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

        return response()->json(['data' => $conversations]);
    }

    public function show($id)
    {
        $conversation = Conversation::with(['department', 'messages'])->findOrFail($id);

        return view('helpdesk.conversations.show', [
            'title'        => 'Conversation Detail',
            'conversation' => $conversation,
        ]);
    }

    public function reply(Request $request, $id)
    {
        $request->validate([
            'message'    => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        $conversation = Conversation::findOrFail($id);

        $attachmentName = null;
        if ($request->hasFile('attachment')) {
            $file           = $request->file('attachment');
            $attachmentName = rand(1, 9999) . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('images/helpdesk'), $attachmentName);
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_type'     => 'admin',
            'message'         => $request->message,
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
}
