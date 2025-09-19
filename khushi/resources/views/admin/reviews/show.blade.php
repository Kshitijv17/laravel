@extends('layouts.admin')

@section('title', 'Review Details')
@section('subtitle', 'View and manage customer review')

@section('content')
<div class="d-flex justify-content-end align-items-center mb-3">
    @if($review->status === 'pending')
        <button class="btn btn-success me-2" onclick="approveReview({{ $review->id }})">
            <i class="fas fa-check me-2"></i>Approve Review
        </button>
        <button class="btn btn-warning me-2" onclick="rejectReview({{ $review->id }})">
            <i class="fas fa-times me-2"></i>Reject Review
        </button>
    @endif
    <button class="btn btn-danger me-2" onclick="deleteReview({{ $review->id }})">
        <i class="fas fa-trash me-2"></i>Delete Review
    </button>
    <a href="{{ route('admin.reviews.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left me-2"></i>Back to Reviews
    </a>
</div>
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item"><a href="{{ route('admin.reviews.index') }}">Reviews</a></li>
        <li class="breadcrumb-item active">Review #{{ $review->id }}</li>
    </ol>
</nav>

<div class="row">
    <div class="col-lg-8">
        <!-- Review Content -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Review Content</h5>
            </div>
            <div class="card-body">
                <!-- Rating -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Rating</h6>
                    <div class="d-flex align-items-center">
                        <div class="rating me-3">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= $review->rating ? 'text-warning' : 'text-muted' }}" style="font-size: 1.5rem;"></i>
                            @endfor
                        </div>
                        <span class="h5 mb-0">{{ $review->rating }}/5</span>
                    </div>
                </div>

                <!-- Review Text -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Review Comment</h6>
                    @if($review->comment)
                        <div class="bg-light p-3 rounded">
                            <p class="mb-0">{{ $review->comment }}</p>
                        </div>
                    @else
                        <p class="text-muted fst-italic">No comment provided</p>
                    @endif
                </div>

                <!-- Review Images (if any) -->
                @if($review->images && count($review->images) > 0)
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Review Images</h6>
                    <div class="row">
                        @foreach($review->images as $image)
                        <div class="col-md-3 mb-3">
                            <img src="{{ asset('storage/' . $image) }}" 
                                 alt="Review Image" 
                                 class="img-fluid rounded cursor-pointer"
                                 onclick="showImageModal('{{ asset('storage/' . $image) }}')">
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Admin Response -->
                <div class="mb-4">
                    <h6 class="text-muted mb-2">Admin Response</h6>
                    @if($review->admin_response)
                        <div class="bg-primary bg-opacity-10 p-3 rounded border-start border-primary border-3">
                            <p class="mb-2">{{ $review->admin_response }}</p>
                            <small class="text-muted">
                                Responded by {{ $review->admin_responder->name ?? 'Admin' }} 
                                on {{ $review->admin_response_date ? $review->admin_response_date->format('M d, Y g:i A') : 'N/A' }}
                            </small>
                        </div>
                    @else
                        <form id="adminResponseForm">
                            <div class="mb-3">
                                <textarea class="form-control" name="admin_response" rows="4" 
                                          placeholder="Write a response to this review..."></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-reply me-2"></i>Send Response
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>

        <!-- Review History -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Review History</h5>
            </div>
            <div class="card-body">
                <div class="timeline">
                    <div class="timeline-item">
                        <div class="timeline-marker bg-primary"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Review Submitted</h6>
                            <p class="timeline-text">{{ $review->created_at->format('M d, Y \a\t g:i A') }}</p>
                        </div>
                    </div>
                    
                    @if($review->status === 'approved')
                    <div class="timeline-item">
                        <div class="timeline-marker bg-success"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Review Approved</h6>
                            <p class="timeline-text">
                                {{ $review->updated_at->format('M d, Y \a\t g:i A') }}
                                @if($review->approved_by)
                                    <br><small>by {{ $review->approved_by->name }}</small>
                                @endif
                            </p>
                        </div>
                    </div>
                    @elseif($review->status === 'rejected')
                    <div class="timeline-item">
                        <div class="timeline-marker bg-danger"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Review Rejected</h6>
                            <p class="timeline-text">
                                {{ $review->updated_at->format('M d, Y \a\t g:i A') }}
                                @if($review->rejected_by)
                                    <br><small>by {{ $review->rejected_by->name }}</small>
                                @endif
                            </p>
                        </div>
                    </div>
                    @endif
                    
                    @if($review->admin_response)
                    <div class="timeline-item">
                        <div class="timeline-marker bg-info"></div>
                        <div class="timeline-content">
                            <h6 class="timeline-title">Admin Response Added</h6>
                            <p class="timeline-text">{{ $review->admin_response_date ? $review->admin_response_date->format('M d, Y \a\t g:i A') : 'N/A' }}</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Review Status -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Review Status</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Status:</strong></div>
                    <div class="col-sm-7">
                        @php
                            $badgeClass = match($review->status) {
                                'approved' => 'bg-success',
                                'rejected' => 'bg-danger',
                                default => 'bg-warning'
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ ucfirst($review->status) }}</span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Rating:</strong></div>
                    <div class="col-sm-7">{{ $review->rating }}/5 stars</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Date:</strong></div>
                    <div class="col-sm-7">{{ $review->created_at->format('M d, Y') }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Helpful Votes:</strong></div>
                    <div class="col-sm-7">{{ $review->helpful_count ?? 0 }}</div>
                </div>
            </div>
        </div>

        <!-- Product Information -->
        @if($review->product)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Product Information</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    @if($review->product->image)
                        <img src="{{ asset('storage/' . $review->product->image) }}" 
                             alt="{{ $review->product->name }}" 
                             class="rounded me-3" 
                             style="width: 60px; height: 60px; object-fit: cover;">
                    @endif
                    <div>
                        <h6 class="mb-1">{{ $review->product->name }}</h6>
                        <small class="text-muted">SKU: {{ $review->product->sku ?? 'N/A' }}</small>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-5"><strong>Price:</strong></div>
                    <div class="col-sm-7">${{ number_format($review->product->price, 2) }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-5"><strong>Category:</strong></div>
                    <div class="col-sm-7">{{ $review->product->category->name ?? 'N/A' }}</div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-5"><strong>Stock:</strong></div>
                    <div class="col-sm-7">{{ $review->product->stock ?? 0 }} units</div>
                </div>
                <a href="{{ route('admin.products.show', $review->product->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-eye me-1"></i>View Product
                </a>
            </div>
        </div>
        @endif

        <!-- Customer Information -->
        @if($review->user)
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Customer Information</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Name:</strong></div>
                    <div class="col-sm-7">{{ $review->user->name }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Email:</strong></div>
                    <div class="col-sm-7">{{ $review->user->email }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Member Since:</strong></div>
                    <div class="col-sm-7">{{ $review->user->created_at->format('M Y') }}</div>
                </div>
                <div class="row mb-3">
                    <div class="col-sm-5"><strong>Total Reviews:</strong></div>
                    <div class="col-sm-7">{{ $review->user->reviews_count ?? 0 }}</div>
                </div>
                <a href="{{ route('admin.users.show', $review->user->id) }}" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-user me-1"></i>View Customer
                </a>
            </div>
        </div>
        @endif

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    @if($review->status === 'pending')
                        <button class="btn btn-success btn-sm" onclick="approveReview({{ $review->id }})">
                            <i class="fas fa-check me-2"></i>Approve Review
                        </button>
                        <button class="btn btn-warning btn-sm" onclick="rejectReview({{ $review->id }})">
                            <i class="fas fa-times me-2"></i>Reject Review
                        </button>
                    @endif
                    
                    <button class="btn btn-info btn-sm" onclick="flagReview({{ $review->id }})">
                        <i class="fas fa-flag me-2"></i>Flag Review
                    </button>
                    
                    <button class="btn btn-secondary btn-sm" onclick="emailCustomer({{ $review->user->id ?? 0 }})">
                        <i class="fas fa-envelope me-2"></i>Email Customer
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="Review Image" class="img-fluid">
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -22px;
    top: 5px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    border: 2px solid #fff;
}

.timeline-title {
    font-size: 14px;
    font-weight: 600;
    margin-bottom: 5px;
}

.timeline-text {
    font-size: 13px;
    color: #6c757d;
    margin-bottom: 0;
}

.cursor-pointer {
    cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script>
function approveReview(reviewId) {
    updateReviewStatus(reviewId, 'approved');
}

function rejectReview(reviewId) {
    updateReviewStatus(reviewId, 'rejected');
}

function updateReviewStatus(reviewId, status) {
    fetch(`/admin/reviews/${reviewId}/status`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ status: status })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating review status');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating review status');
    });
}

function deleteReview(reviewId) {
    if (confirm('Are you sure you want to delete this review?')) {
        fetch(`/admin/reviews/${reviewId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.href = '/admin/reviews';
            } else {
                alert('Error deleting review');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting review');
        });
    }
}

function showImageModal(imageSrc) {
    document.getElementById('modalImage').src = imageSrc;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

function flagReview(reviewId) {
    alert('Flag review functionality would be implemented here');
}

function emailCustomer(userId) {
    alert('Email customer functionality would be implemented here');
}

// Admin response form
document.getElementById('adminResponseForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const reviewId = {{ $review->id }};
    
    fetch(`/admin/reviews/${reviewId}/response`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error sending response');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error sending response');
    });
});
</script>
@endpush
