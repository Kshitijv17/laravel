@extends('layouts.app')

@section('title','My Addresses')

@push('styles')
<style>
 .modern-container { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 1.25rem 0 2rem; }
 .profile-card { background: rgba(255,255,255,.95); backdrop-filter: blur(20px); border: none; border-radius: 24px; box-shadow: 0 20px 40px rgba(0,0,0,.1); overflow: hidden; max-width: 960px; margin: 0 auto; }
 .profile-header { background: linear-gradient(135deg, #4285f4 0%, #34a853 100%); color:#fff; padding: 1.5rem 2rem; text-align:center; }
 .profile-name { margin:0; font-weight:700; font-size:1.4rem; }
 .profile-email { margin:.2rem 0 0; opacity:.85; }
 .profile-body { padding: 1.25rem 1.25rem 1.5rem; }
 .addr-badge { font-size:.75rem; }
 .addr-actions { display:flex; gap:.5rem; flex-wrap:wrap; }
 .empty { text-align:center; padding:3rem 1rem; }
 .empty i { opacity:.35; }
</style>
@endpush

@section('content')
<div class="modern-container">
  <div class="container">
    <div class="profile-card">
      <div class="profile-header">
        <h2 class="profile-name">My Addresses</h2>
        <p class="profile-email">Manage your delivery locations</p>
      </div>
      <div class="profile-body">
        <div class="d-flex justify-content-end mb-3">
          <button class="btn btn-primary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#addAddressForm"><i class="fas fa-plus me-2"></i>Add New Address</button>
        </div>

        <div id="addAddressForm" class="collapse mb-3">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <form action="{{ route('user.addresses.store') }}" method="POST" class="row g-3">
                @csrf
                <div class="col-md-4">
                  <label class="form-label small">Type</label>
                  <select name="type" class="form-select form-select-sm" required>
                    <option value="home">Home</option>
                    <option value="office">Office</option>
                    <option value="other">Other</option>
                  </select>
                </div>
                <div class="col-md-4">
                  <label class="form-label small">First Name</label>
                  <input name="first_name" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label small">Last Name</label>
                  <input name="last_name" class="form-control form-control-sm" required>
                </div>
                <div class="col-12">
                  <label class="form-label small">Address Line 1</label>
                  <input name="address_line_1" class="form-control form-control-sm" required>
                </div>
                <div class="col-12">
                  <label class="form-label small">Address Line 2</label>
                  <input name="address_line_2" class="form-control form-control-sm">
                </div>
                <div class="col-md-4">
                  <label class="form-label small">City</label>
                  <input name="city" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label small">State</label>
                  <input name="state" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label small">Postal Code</label>
                  <input name="postal_code" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-8">
                  <label class="form-label small">Country</label>
                  <input name="country" class="form-control form-control-sm" required>
                </div>
                <div class="col-md-4">
                  <label class="form-label small">Phone</label>
                  <input name="phone" class="form-control form-control-sm">
                </div>
                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_default" id="is_default">
                    <label class="form-check-label small" for="is_default">Set as default</label>
                  </div>
                </div>
                <div class="col-12 d-flex justify-content-end gap-2">
                  <button type="button" class="btn btn-light btn-sm" data-bs-toggle="collapse" data-bs-target="#addAddressForm">Cancel</button>
                  <button type="submit" class="btn btn-primary btn-sm">Save Address</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        @php
          $addresses = isset($addresses) ? $addresses : (optional(Auth::user())->addresses ?? collect());
        @endphp

        @if($addresses && count($addresses))
          <div class="row g-3">
            @foreach($addresses as $address)
              <div class="col-12">
                <div class="border rounded-3 p-3 bg-white list-group-item">
                  <div class="d-flex justify-content-between align-items-start gap-3">
                    <div class="flex-grow-1">
                      <div class="d-flex align-items-center gap-2 mb-1">
                        <strong>{{ $address->name ?? ($address->full_name ?? Auth::user()->name) }}</strong>
                        @if(($address->is_default ?? false))
                          <span class="badge bg-success addr-badge">Default</span>
                        @endif
                        @if(($address->type ?? '') === 'office')
                          <span class="badge bg-info text-dark addr-badge">Office</span>
                        @elseif(($address->type ?? '') === 'home')
                          <span class="badge bg-secondary addr-badge">Home</span>
                        @endif
                      </div>
                      <div class="text-muted small">
                        {{ $address->address_line_1 ?? $address->line1 ?? '' }}
                        @if(($address->address_line_2 ?? $address->line2 ?? null))<br>{{ $address->address_line_2 ?? $address->line2 }}@endif
                        @php
                          $parts = array_filter([
                            $address->city ?? null,
                            $address->state ?? null,
                            $address->postal_code ?? $address->zip ?? null,
                            $address->country ?? null,
                          ]);
                        @endphp
                        @if(count($parts))<br>{{ implode(', ', $parts) }}@endif
                        @if(($address->phone ?? null))<br>Phone: {{ $address->phone }}@endif
                      </div>
                    </div>
                    <div class="addr-actions">
                      @if(!($address->is_default ?? false))
                        <form action="{{ route('user.addresses.update', $address->id) }}" method="POST">
                          @csrf
                          @method('PUT')
                          <input type="hidden" name="type" value="{{ $address->type ?? 'home' }}">
                          <input type="hidden" name="first_name" value="{{ $address->first_name ?? ($address->name ?? '') }}">
                          <input type="hidden" name="last_name" value="{{ $address->last_name ?? '' }}">
                          <input type="hidden" name="address_line_1" value="{{ $address->address_line_1 ?? $address->line1 }}">
                          <input type="hidden" name="address_line_2" value="{{ $address->address_line_2 ?? $address->line2 }}">
                          <input type="hidden" name="city" value="{{ $address->city }}">
                          <input type="hidden" name="state" value="{{ $address->state }}">
                          <input type="hidden" name="postal_code" value="{{ $address->postal_code ?? $address->zip }}">
                          <input type="hidden" name="country" value="{{ $address->country }}">
                          <input type="hidden" name="phone" value="{{ $address->phone }}">
                          <input type="hidden" name="is_default" value="1">
                          <button type="submit" class="btn btn-outline-primary btn-sm">Set Default</button>
                        </form>
                      @endif
                      <form action="{{ route('user.addresses.delete', $address->id) }}" method="POST" onsubmit="return confirm('Delete this address?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">Delete</button>
                      </form>
                    </div>
                  </div>
                </div>
              </div>
            @endforeach
          </div>
        @else
          <div class="empty">
            <i class="fas fa-map-marker-alt fa-3x mb-3"></i>
            <h3 class="fw-semibold mb-2">No addresses yet</h3>
            <p class="text-muted mb-3">Save your delivery addresses for a faster checkout experience.</p>
            @if(Route::has('user.addresses.create'))
              <a class="btn btn-primary" href="{{ route('user.addresses.create') }}"><i class="fas fa-plus me-2"></i>Add Address</a>
            @endif
          </div>
        @endif
      </div>
    </div>
  </div>
</div>
@endsection
