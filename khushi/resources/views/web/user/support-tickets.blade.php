@extends('layouts.app')

@section('title','Support Tickets')

@push('styles')
<style>
 .modern-container { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.25rem 0 2rem; }
 .profile-card { background: rgba(255,255,255,.95); backdrop-filter: blur(20px); border: none; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,.1); overflow: hidden; max-width: 1000px; margin: 0 auto; }
 .profile-header { background: linear-gradient(135deg, #4285f4 0%, #34a853 100%); color:#fff; padding: 1.5rem 2rem; text-align:center; }
 .profile-name { margin:0; font-weight:700; font-size:1.4rem; }
 .profile-email { margin:.2rem 0 0; opacity:.85; }
 .profile-body { padding: 1.25rem 1.25rem 1.5rem; }
 .ticket-row { display:flex; gap:1rem; align-items:center; }
 .ticket-main { flex:1 1 auto; min-width:0; }
 .ticket-subject { font-weight:700; margin:0; }
 .ticket-meta { color:#6b7280; font-size:.9rem; }
 .badge-soft { background:#eef2ff; color:#3730a3; }
 .badge-progress { background:#fff7ed; color:#9a3412; }
 .badge-closed { background:#f3f4f6; color:#374151; }
 .actions { display:flex; gap:.5rem; flex-wrap:wrap; }
</style>
@endpush

@section('content')
<div class="modern-container">
  <div class="container">
    <div class="profile-card">
      <div class="profile-header">
        <h2 class="profile-name">Support Tickets</h2>
        <p class="profile-email">Track and manage your support requests</p>
      </div>
      <div class="profile-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <div class="d-flex gap-2">
            <form method="GET" class="d-flex gap-2">
              <input name="q" value="{{ request('q') }}" class="form-control form-control-sm" placeholder="Search subject or ID">
              <button class="btn btn-light btn-sm" type="submit"><i class="fas fa-search me-1"></i>Search</button>
            </form>
          </div>
          <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#newTicketModal"><i class="fas fa-plus me-2"></i>New Ticket</button>
        </div>

        @php
          $tickets = isset($tickets) ? $tickets : (optional(Auth::user())->tickets ?? collect());
          if(request('q')){
            $tickets = $tickets->filter(function($t){
              $q = strtolower(request('q'));
              return str_contains(strtolower((string)($t->subject ?? '')), $q) || str_contains((string)($t->id ?? ''), $q);
            });
          }
        @endphp

        @if($tickets && count($tickets))
          <div class="list-group">
            @foreach($tickets as $ticket)
              @php
                $status = strtolower($ticket->status ?? 'open');
                $badgeClass = $status === 'closed' ? 'badge-closed' : ($status === 'in_progress' || $status === 'in-progress' ? 'badge-progress' : 'badge-soft');
              @endphp
              <div class="list-group-item py-3">
                <div class="ticket-row">
                  <div class="ticket-main">
                    <p class="ticket-subject mb-1">#{{ $ticket->id ?? '—' }} — {{ $ticket->subject ?? 'No subject' }}</p>
                    <div class="ticket-meta">
                      <span class="badge {{ $badgeClass }} me-2 text-uppercase">{{ $ticket->status ?? 'Open' }}</span>
                      Priority: <strong>{{ ucfirst($ticket->priority ?? 'normal') }}</strong>
                      <span class="mx-2">•</span>
                      Updated {{ optional($ticket->updated_at)->diffForHumans() ?? '—' }}
                    </div>
                  </div>
                  <div class="actions">
                    <a href="{{ route('user.support-ticket-details', $ticket->id) }}" class="btn btn-light btn-sm">View</a>
                    @if(Route::has('user.support-tickets.close') && ($ticket->status ?? '') !== 'closed')
                      <a href="{{ route('user.support-tickets.close', $ticket->id) }}" class="btn btn-outline-secondary btn-sm">Close</a>
                    @endif
                  </div>
                </div>
              </div>
            @endforeach
          </div>
          @if(method_exists($tickets, 'links'))
            <div class="mt-3">{{ $tickets->links() }}</div>
          @endif
        @else
          <div class="text-center py-5">
            <i class="fas fa-life-ring fa-3x mb-3" style="opacity:.35;"></i>
            <h3 class="fw-semibold mb-2">No tickets yet</h3>
            <p class="text-muted mb-3">If you need help, create a ticket and our team will reach out.</p>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#newTicketModal"><i class="fas fa-plus me-2"></i>Create Ticket</button>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
<!-- New Ticket Modal -->
<div class="modal fade" id="newTicketModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Create Support Ticket</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('user.support-tickets.create') }}" method="POST">
        @csrf
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label small">Subject</label>
            <input name="subject" class="form-control" required>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small">Priority</label>
              <select name="priority" class="form-select" required>
                <option value="low">Low</option>
                <option value="medium">Medium</option>
                <option value="high">High</option>
                <option value="urgent">Urgent</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label small">Category</label>
              <input name="category" class="form-control" placeholder="General" required>
            </div>
          </div>
          <div class="mt-3">
            <label class="form-label small">Message</label>
            <textarea name="message" class="form-control" rows="4" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Create Ticket</button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
