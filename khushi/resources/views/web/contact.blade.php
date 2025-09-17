@extends('layouts.app')

@section('title', 'Contact Us')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <!-- Page Header -->
        <div class="col-12 text-center mb-4">
            <h1 class="h2 mb-1">Contact Us</h1>
            <p class="text-muted mb-0">We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p>
        </div>

        <div class="row g-4">
            <!-- Contact Form -->
            <div class="col-lg-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Send us a Message</h5>

                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('contact.submit') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject</label>
                                <input type="text" id="subject" name="subject" value="{{ old('subject') }}" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Message</label>
                                <textarea id="message" name="message" rows="6" class="form-control" required>{{ old('message') }}</textarea>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-paper-plane me-1"></i> Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Contact Information and Quick Help -->
            <div class="col-lg-6">
                <div class="card mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Get in Touch</h5>

                        <div class="d-flex align-items-start mb-3">
                            <i class="fas fa-map-marker-alt text-primary me-3 mt-1"></i>
                            <div>
                                <div class="fw-semibold">Address</div>
                                <div class="text-muted">123 Business Street<br>City, State 12345<br>Country</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start mb-3">
                            <i class="fas fa-phone text-primary me-3 mt-1"></i>
                            <div>
                                <div class="fw-semibold">Phone</div>
                                <div class="text-muted">+1 (555) 123-4567</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start mb-3">
                            <i class="fas fa-envelope text-primary me-3 mt-1"></i>
                            <div>
                                <div class="fw-semibold">Email</div>
                                <div class="text-muted">contact@yourstore.com</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-start">
                            <i class="fas fa-clock text-primary me-3 mt-1"></i>
                            <div>
                                <div class="fw-semibold">Business Hours</div>
                                <div class="text-muted">Mon-Fri: 9:00 AM - 6:00 PM<br>Sat: 10:00 AM - 4:00 PM<br>Sun: Closed</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Quick Help</h5>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <div class="fw-semibold">Need support with an order?</div>
                                <small class="text-muted">Check our <a href="{{ route('faq') }}">FAQ section</a> for common questions.</small>
                            </li>
                            <li class="mb-2">
                                <div class="fw-semibold">Want to return an item?</div>
                                <small class="text-muted">Visit your account dashboard to initiate a return request.</small>
                            </li>
                            <li>
                                <div class="fw-semibold">Track your order</div>
                                <small class="text-muted">Log in to your account to see real-time order updates.</small>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
