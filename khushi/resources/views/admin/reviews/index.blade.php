@extends('layouts.admin')

@section('title', 'Reviews Management')
@section('subtitle', 'Manage customer product reviews and ratings')

@section('content')
<div class="d-flex justify-content-end align-items-center mb-3">
    <div class="btn-group">
        <button class="btn btn-success" onclick="bulkApprove()">
            <i class="fas fa-check me-2"></i>Bulk Approve
        </button>
        <button class="btn btn-warning" onclick="bulkReject()">
            <i class="fas fa-times me-2"></i>Bulk Reject
        </button>
        <button class="btn btn-primary" onclick="exportReviews()">
            <i class="fas fa-download me-2"></i>Export Reviews
        </button>
    </div>
</div>
<nav aria-label="breadcrumb" class="mb-3">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
        <li class="breadcrumb-item active">Reviews</li>
    </ol>
</nav>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">Total Reviews</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_reviews'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-star fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">Pending Reviews</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_reviews'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">Average Rating</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['average_rating'] ?? 0, 1) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-thumbs-up fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">This Month</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['monthly_reviews'] ?? 0 }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h5 class="card-title mb-0">Customer Reviews</h5>
    </div>
    <div class="card-body">
        <!-- Filters -->
        <div class="row mb-3">
            <div class="col-md-3">
                <select class="form-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="approved">Approved</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
            <div class="col-md-3">
                <select class="form-select" id="ratingFilter">
                    <option value="">All Ratings</option>
                    <option value="5">5 Stars</option>
                    <option value="4">4 Stars</option>
                    <option value="3">3 Stars</option>
                    <option value="2">2 Stars</option>
                    <option value="1">1 Star</option>
                </select>
            </div>
            <div class="col-md-4">
                <input type="text" class="form-control" id="searchReviews" placeholder="Search reviews, products, or customers...">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-secondary" onclick="resetFilters()">
                    <i class="fas fa-refresh"></i> Reset
                </button>
            </div>
        </div>

        <!-- Reviews Table -->
        <div class="table-responsive">
            <table class="table table-hover" id="reviewsTable">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="selectAll" onchange="toggleSelectAll()">
                        </th>
                        <th>Product</th>
                        <th>Customer</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews ?? [] as $review)
                    <tr>
                        <td>
                            <input type="checkbox" class="review-checkbox" value="{{ $review->id }}">
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                @if($review->product && $review->product->image)
                                    <img src="{{ asset('storage/' . $review->product->image) }}" 
                                         alt="{{ $review->product->name }}" 
                                         class="rounded me-2" 
                                         style="width: 40px; height: 40px; object-fit: cover;">
                                @endif
                                <div>
                                    <strong>{{ $review->product->name ?? 'Product Deleted' }}</strong>
                                    <br><small class="text-muted">SKU: {{ $review->product->sku ?? 'N/A' }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong>{{ $review->user->name ?? 'Anonymous' }}</strong>
                                <br><small class="text-muted">{{ $review->user->email ?? 'No email' }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="rating">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="fas fa-star {{ $i <= ($review->rating ?? 0) ? 'text-warning' : 'text-muted' }}"></i>
                                @endfor
                                <br><small class="text-muted">{{ $review->rating ?? 0 }}/5</small>
                            </div>
                        </td>
                        <td>
                            <div style="max-width: 300px;">
                                <p class="mb-1">{{ Str::limit($review->comment ?? 'No comment', 100) }}</p>
                                @if($review->comment && strlen($review->comment) > 100)
                                    <button class="btn btn-link btn-sm p-0" onclick="showFullReview({{ $review->id }})">
                                        Read more...
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td>
                            @php
                                $status = $review->status ?? 'pending';
                                $badgeClass = match($status) {
                                    'approved' => 'bg-success',
                                    'rejected' => 'bg-danger',
                                    default => 'bg-warning'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ ucfirst($status) }}</span>
                        </td>
                        <td>
                            <small>{{ $review->created_at ? $review->created_at->format('M d, Y') : 'N/A' }}</small>
                            <br><small class="text-muted">{{ $review->created_at ? $review->created_at->format('g:i A') : '' }}</small>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <button class="btn btn-outline-primary" onclick="viewReview({{ $review->id }})" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </button>
                                @if(($review->status ?? 'pending') === 'pending')
                                    <button class="btn btn-outline-success" onclick="approveReview({{ $review->id }})" title="Approve">
                                        <i class="fas fa-check"></i>
                                    </button>
                                    <button class="btn btn-outline-danger" onclick="rejectReview({{ $review->id }})" title="Reject">
                                        <i class="fas fa-times"></i>
                                    </button>
                                @endif
                                <button class="btn btn-outline-danger" onclick="deleteReview({{ $review->id }})" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="fas fa-star fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No reviews found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if(isset($reviews) && method_exists($reviews, 'links'))
            <div class="d-flex justify-content-center">
                {{ $reviews->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Review Details Modal -->
<div class="modal fade" id="reviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Review Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="reviewModalContent">
                <!-- Content loaded via AJAX -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Select all functionality
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.review-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAll.checked;
    });
}

// Get selected review IDs
function getSelectedReviews() {
    const checkboxes = document.querySelectorAll('.review-checkbox:checked');
    return Array.from(checkboxes).map(cb => cb.value);
}

// Bulk approve reviews
function bulkApprove() {
    const selected = getSelectedReviews();
    if (selected.length === 0) {
        alert('Please select reviews to approve');
        return;
    }
    
    if (confirm(`Approve ${selected.length} selected reviews?`)) {
        bulkUpdateStatus(selected, 'approved');
    }
}

// Bulk reject reviews
function bulkReject() {
    const selected = getSelectedReviews();
    if (selected.length === 0) {
        alert('Please select reviews to reject');
        return;
    }
    
    if (confirm(`Reject ${selected.length} selected reviews?`)) {
        bulkUpdateStatus(selected, 'rejected');
    }
}

// Bulk update status
function bulkUpdateStatus(reviewIds, status) {
    fetch('/admin/reviews/bulk-update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            review_ids: reviewIds,
            status: status
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('Error updating reviews: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error updating reviews');
    });
}

// Individual review actions
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

// View review details
function viewReview(reviewId) {
    fetch(`/admin/reviews/${reviewId}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('reviewModalContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('reviewModal')).show();
        });
}

// Show full review text
function showFullReview(reviewId) {
    viewReview(reviewId);
}

// Delete review
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
                location.reload();
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

// Export reviews
function exportReviews() {
    window.open('/admin/reviews/export', '_blank');
}

// Filter functionality
function filterReviews() {
    const status = document.getElementById('statusFilter').value;
    const rating = document.getElementById('ratingFilter').value;
    const search = document.getElementById('searchReviews').value;
    
    const params = new URLSearchParams();
    if (status) params.append('status', status);
    if (rating) params.append('rating', rating);
    if (search) params.append('search', search);
    
    window.location.href = '/admin/reviews?' + params.toString();
}

// Reset filters
function resetFilters() {
    document.getElementById('statusFilter').value = '';
    document.getElementById('ratingFilter').value = '';
    document.getElementById('searchReviews').value = '';
    window.location.href = '/admin/reviews';
}

// Add event listeners
document.getElementById('statusFilter').addEventListener('change', filterReviews);
document.getElementById('ratingFilter').addEventListener('change', filterReviews);
document.getElementById('searchReviews').addEventListener('keyup', function(e) {
    if (e.key === 'Enter') {
        filterReviews();
    }
});
</script>
@endpush
