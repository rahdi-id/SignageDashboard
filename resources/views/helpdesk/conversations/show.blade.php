@extends('layouts.main')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Conversation Detail</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item active"><a href="{{ route('helpdesk.dashboard') }}">Hotel Helpdesk</a></div>
            <div class="breadcrumb-item active"><a href="{{ route('helpdesk.conversations.index') }}">Conversations</a></div>
            <div class="breadcrumb-item">Detail</div>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
    @endif

    <div class="section-body">
        <div class="row">
            {{-- Conversation Info --}}
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4>Ticket Info</h4>
                    </div>
                    <div class="card-body">
                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="text-muted" width="40%">Ticket #</td>
                                <td><strong>#{{ $conversation->id }}</strong></td>
                            </tr>
                            <tr>
                                <td class="text-muted">Guest Name</td>
                                <td>{{ $conversation->guest_name }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Room Number</td>
                                <td>{{ $conversation->room_number }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Department</td>
                                <td>{{ $conversation->department->name ?? '-' }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted">Status</td>
                                <td>
                                    @php $sc = \App\Models\Conversation::STATUS_COLORS[$conversation->status] ?? 'secondary'; @endphp
                                    <span class="badge badge-{{ $sc }}">
                                        {{ \App\Models\Conversation::STATUS_LABELS[$conversation->status] ?? $conversation->status }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Priority</td>
                                <td>
                                    @php $pc = \App\Models\Conversation::PRIORITY_COLORS[$conversation->priority] ?? 'secondary'; @endphp
                                    <span class="badge badge-{{ $pc }}">
                                        {{ \App\Models\Conversation::PRIORITY_LABELS[$conversation->priority] ?? $conversation->priority }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted">Created At</td>
                                <td>{{ $conversation->created_at->format('d M Y H:i') }}</td>
                            </tr>
                        </table>
                    </div>
                    <div class="card-footer">
                        <div class="d-flex flex-column gap-2">
                            @if($conversation->status !== 'closed')
                            <form action="{{ route('helpdesk.conversations.close', $conversation->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-success btn-block"
                                    onclick="return confirm('Close this conversation?')">
                                    <i class="fas fa-check-circle"></i> Close Ticket
                                </button>
                            </form>
                            @else
                            <form action="{{ route('helpdesk.conversations.reopen', $conversation->id) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-warning btn-block"
                                    onclick="return confirm('Reopen this conversation?')">
                                    <i class="fas fa-redo"></i> Reopen Ticket
                                </button>
                            </form>
                            @endif
                            <a href="{{ route('helpdesk.conversations.index') }}" class="btn btn-secondary btn-block mt-2">
                                <i class="fas fa-arrow-left"></i> Back to List
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Messages Thread --}}
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Messages</h4>
                    </div>
                    <div class="card-body" style="max-height: 480px; overflow-y: auto;" id="messages-container">
                        @forelse($conversation->messages as $msg)
                        <div class="d-flex mb-3 {{ $msg->sender_type === 'admin' ? 'flex-row-reverse' : '' }}">
                            <div class="mr-3 ml-3">
                                <div class="rounded-circle d-flex align-items-center justify-content-center"
                                    style="width:40px;height:40px;background:{{ $msg->sender_type === 'admin' ? '#6777ef' : '#e0e0e0' }}">
                                    <i class="fas {{ $msg->sender_type === 'admin' ? 'fa-user-tie' : 'fa-user' }}"
                                        style="color:{{ $msg->sender_type === 'admin' ? '#fff' : '#555' }}"></i>
                                </div>
                            </div>
                            <div style="max-width:70%">
                                <div class="card mb-1"
                                    style="background:{{ $msg->sender_type === 'admin' ? '#6777ef' : '#f5f5f5' }};
                                           border:none;">
                                    <div class="card-body py-2 px-3"
                                        style="color:{{ $msg->sender_type === 'admin' ? '#fff' : '#333' }}">
                                        <small class="font-weight-bold d-block mb-1">
                                            {{ $msg->sender_type === 'admin' ? 'Admin' : $conversation->guest_name }}
                                        </small>
                                        <p class="mb-0" style="white-space:pre-wrap">{{ $msg->message }}</p>
                                        @if($msg->attachment)
                                        <div class="mt-2">
                                            <a href="{{ asset('images/helpdesk/' . $msg->attachment) }}"
                                                target="_blank"
                                                class="btn btn-sm {{ $msg->sender_type === 'admin' ? 'btn-light' : 'btn-secondary' }}">
                                                <i class="fas fa-paperclip"></i> Attachment
                                            </a>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                <small class="text-muted {{ $msg->sender_type === 'admin' ? 'd-block text-right' : '' }}">
                                    {{ $msg->created_at->format('d M Y H:i') }}
                                </small>
                            </div>
                        </div>
                        @empty
                        <p class="text-muted text-center">No messages yet.</p>
                        @endforelse
                    </div>

                    {{-- Reply Form --}}
                    @if($conversation->status !== 'closed')
                    <div class="card-footer">
                        <form action="{{ route('helpdesk.conversations.reply', $conversation->id) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="form-group">
                                <label class="font-weight-bold">Reply</label>
                                <textarea name="message" class="form-control @error('message') is-invalid @enderror"
                                    rows="3" placeholder="Type your reply here...">{{ old('message') }}</textarea>
                                @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label>Attachment <small class="text-muted">(jpg, png, pdf, doc — max 2MB)</small></label>
                                <input type="file" name="attachment"
                                    class="form-control-file @error('attachment') is-invalid @enderror"
                                    accept=".jpg,.jpeg,.png,.pdf,.doc,.docx">
                                @error('attachment')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-reply"></i> Send Reply
                            </button>
                        </form>
                    </div>
                    @else
                    <div class="card-footer text-center text-muted">
                        <i class="fas fa-lock"></i> This conversation is closed. Reopen to reply.
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('js')
<script>
    // Auto-scroll messages container to bottom
    const container = document.getElementById('messages-container');
    if (container) {
        container.scrollTop = container.scrollHeight;
    }
</script>
@endsection
