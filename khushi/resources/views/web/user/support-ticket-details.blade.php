@extends('layouts.app')

@section('title','Ticket Details')

@push('styles')
<style>
 .modern-container { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.25rem 0 2rem; }
 .profile-card { background: rgba(255,255,255,.95); backdrop-filter: blur(20px); border: none; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,.1); overflow: hidden; max-width: 900px; margin: 0 auto; }
 .profile-header { background: linear-gradient(135deg, #4285f4 0%, #34a853 100%); color:#fff; padding: 1.2rem 1.5rem; }
 .profile-name { margin:0; font-weight:700; font-size:1.1rem; }
 .profile-email { margin:.2rem 0 0; opacity:.85; }
 .profile-body { padding: 1rem 1.25rem 1.5rem; }
 .msg { display:flex; gap:.75rem; margin-bottom:1rem; }
 .msg .avatar { width:36px; height:36px; border-radius:50%; background:#e5e7eb; display:flex; align-items:center; justify-content:center; }
 .msg .bubble { background:#f8fafc; border:1px solid #eef2f7; border-radius:12px; padding:.75rem .9rem; flex:1; }
 .msg.you .bubble { background:#eef6ff; border-color:#dbeafe; }
 .meta { font-size:.8rem; color:#6b7280; margin-bottom:.25rem; }
</style>
@endpush

@section('content')
<div class="modern-container">
  <div class="container">
    <div class="profile-card">
      <div class="profile-header d-flex justify-content-between align-items-center">
        <div>
          <h2 class="profile-name mb-0">Ticket #{{ $ticket->id }} — {{ $ticket->subject }}</h2>
          <p class="profile-email mb-0">Status: <strong>{{ ucfirst($ticket->status) }}</strong> • Priority: <strong>{{ ucfirst($ticket->priority) }}</strong></p>
        </div>
      </div>
      <div class="profile-body">
        @if(session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <div class="mb-3">
          <div class="list-group">
            @foreach($ticket->messages as $message)
              @php $isYou = $message->user_id === auth()->id(); @endphp
              <div class="list-group-item">
                <div class="msg {{ $isYou ? 'you' : '' }}">
                  <div class="avatar">
                    <i class="fas {{ $isYou ? 'fa-user' : 'fa-headset' }} text-muted"></i>
                  </div>
                  <div class="flex-grow-1">
                    <div class="meta">
                      {{ $isYou ? 'You' : ($message->admin->name ?? 'Support') }} • {{ optional($message->created_at)->diffForHumans() }}
                    </div>
                    <div class="bubble">{!! nl2br(e($message->message)) !!}</div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <form action="{{ route('user.support-tickets.reply', $ticket->id) }}" method="POST" class="mt-3">
          @csrf
          <label class="form-label">Your Message</label>
          <textarea name="message" class="form-control" rows="4" required placeholder="Type your reply..."></textarea>
          <div class="d-flex justify-content-end gap-2 mt-2">
            <a href="{{ route('user.support-tickets') }}" class="btn btn-light">Back</a>
            <button type="submit" class="btn btn-primary">Send Reply</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection
