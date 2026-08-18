@extends('layouts.main')

@section('content')
<section class="section">
    <div class="section-header">
        <h1>Helpdesk Dashboard</h1>
        <div class="section-header-breadcrumb">
            <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item">Hotel Helpdesk</div>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row">
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-primary">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Open Tickets</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalOpen }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-warning">
                    <i class="fas fa-spinner"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>In Progress</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalInProgress }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Closed Tickets</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalClosed }}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6 col-sm-6 col-12">
            <div class="card card-statistic-1">
                <div class="card-icon bg-info">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="card-wrap">
                    <div class="card-header">
                        <h4>Tickets Today</h4>
                    </div>
                    <div class="card-body">
                        {{ $totalToday }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        {{-- Tickets by Department --}}
        <div class="col-lg-5 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Tickets by Department</h4>
                </div>
                <div class="card-body">
                    @forelse($ticketByDepartment as $dept)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>{{ $dept->name }}</span>
                            <span class="font-weight-bold">{{ $dept->conversations_count }}</span>
                        </div>
                        @php
                            $total = $totalOpen + $totalInProgress + $totalClosed;
                            $pct   = $total > 0 ? round(($dept->conversations_count / $total) * 100) : 0;
                        @endphp
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-primary" role="progressbar"
                                style="width: {{ $pct }}%"
                                aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                            </div>
                        </div>
                    </div>
                    @empty
                    <p class="text-muted text-center">No departments found.</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Recent Conversations --}}
        <div class="col-lg-7 col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Recent Conversations</h4>
                    <div class="card-header-action">
                        <a href="{{ route('helpdesk.conversations.index') }}" class="btn btn-primary btn-sm">
                            View All
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                                <tr>
                                    <th>Guest</th>
                                    <th>Room</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Priority</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentConversations as $conv)
                                <tr>
                                    <td>
                                        <a href="{{ route('helpdesk.conversations.show', $conv->id) }}">
                                            {{ $conv->guest_name }}
                                        </a>
                                    </td>
                                    <td>{{ $conv->room_number }}</td>
                                    <td>{{ $conv->department->name ?? '-' }}</td>
                                    <td>
                                        @php $sc = \App\Models\Conversation::STATUS_COLORS[$conv->status] ?? 'secondary'; @endphp
                                        <span class="badge badge-{{ $sc }}">
                                            {{ \App\Models\Conversation::STATUS_LABELS[$conv->status] ?? $conv->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @php $pc = \App\Models\Conversation::PRIORITY_COLORS[$conv->priority] ?? 'secondary'; @endphp
                                        <span class="badge badge-{{ $pc }}">
                                            {{ \App\Models\Conversation::PRIORITY_LABELS[$conv->priority] ?? $conv->priority }}
                                        </span>
                                    </td>
                                    <td>{{ $conv->created_at->format('d M Y H:i') }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="text-center text-muted">No conversations yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
