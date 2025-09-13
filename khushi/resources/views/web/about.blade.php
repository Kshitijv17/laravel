@extends('layouts.app')

@section('title', 'About Us - E-Commerce Store')

@section('content')
<!-- Hero Section -->
<section class="hero-section bg-primary text-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">About Our Store</h1>
                <p class="lead mb-4">We're passionate about bringing you the best products at unbeatable prices, with exceptional customer service that goes above and beyond.</p>
            </div>
            <div class="col-lg-6 text-center">
                <img src="https://via.placeholder.com/500x400/ffffff/3b82f6?text=About+Us" alt="About Us" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Our Story Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold mb-4">Our Story</h2>
                <p class="lead text-muted mb-5">Founded in 2020, E-Store began as a small family business with a simple mission: to make quality products accessible to everyone, everywhere.</p>
            </div>
        </div>
        
        <div class="row g-5 align-items-center">
            <div class="col-lg-6">
                <h3 class="h2 mb-4">From Humble Beginnings</h3>
                <p class="mb-4">What started as a small online shop has grown into a trusted e-commerce platform serving thousands of customers worldwide. Our journey began with a commitment to quality, affordability, and exceptional customer service.</p>
                <p class="mb-4">Today, we continue to uphold these values while expanding our product range and improving our services. Every decision we make is guided by our customers' needs and feedback.</p>
                <div class="row g-3">
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Quality Products</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Fast Shipping</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>Great Prices</span>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            <span>24/7 Support</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <img src="https://via.placeholder.com/600x400/f8f9fa/6c757d?text=Our+Journey" alt="Our Journey" class="img-fluid rounded shadow">
            </div>
        </div>
    </div>
</section>

<!-- Values Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Our Values</h2>
                <p class="lead text-muted">The principles that guide everything we do</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-heart fa-3x text-danger"></i>
                        </div>
                        <h5 class="card-title">Customer First</h5>
                        <p class="card-text">Our customers are at the heart of everything we do. We listen, we care, and we deliver exceptional experiences.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-gem fa-3x text-primary"></i>
                        </div>
                        <h5 class="card-title">Quality Excellence</h5>
                        <p class="card-text">We carefully curate our products to ensure they meet our high standards for quality, durability, and value.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-handshake fa-3x text-success"></i>
                        </div>
                        <h5 class="card-title">Trust & Integrity</h5>
                        <p class="card-text">We build lasting relationships through honest communication, transparent pricing, and reliable service.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-rocket fa-3x text-warning"></i>
                        </div>
                        <h5 class="card-title">Innovation</h5>
                        <p class="card-text">We continuously improve our platform and services to provide the best shopping experience possible.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-leaf fa-3x text-info"></i>
                        </div>
                        <h5 class="card-title">Sustainability</h5>
                        <p class="card-text">We're committed to environmentally responsible practices and supporting sustainable products.</p>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card h-100 border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <div class="mb-3">
                            <i class="fas fa-users fa-3x text-secondary"></i>
                        </div>
                        <h5 class="card-title">Community</h5>
                        <p class="card-text">We believe in giving back to our community and supporting causes that matter to our customers.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Team Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-12 text-center mb-5">
                <h2 class="display-5 fw-bold mb-3">Meet Our Team</h2>
                <p class="lead text-muted">The passionate people behind E-Store</p>
            </div>
        </div>
        
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <img src="https://via.placeholder.com/150x150/3b82f6/ffffff?text=CEO" alt="CEO" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                        <h5 class="card-title">John Smith</h5>
                        <p class="text-muted mb-3">Chief Executive Officer</p>
                        <p class="card-text">With over 15 years of e-commerce experience, John leads our vision of making online shopping accessible and enjoyable for everyone.</p>
                        <div class="social-links">
                            <a href="#" class="text-primary me-2"><i class="fab fa-linkedin"></i></a>
                            <a href="#" class="text-info me-2"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-danger"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <img src="https://via.placeholder.com/150x150/28a745/ffffff?text=CTO" alt="CTO" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                        <h5 class="card-title">Sarah Johnson</h5>
                        <p class="text-muted mb-3">Chief Technology Officer</p>
                        <p class="card-text">Sarah ensures our platform runs smoothly and securely, constantly innovating to improve the user experience.</p>
                        <div class="social-links">
                            <a href="#" class="text-primary me-2"><i class="fab fa-linkedin"></i></a>
                            <a href="#" class="text-info me-2"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-secondary"><i class="fab fa-github"></i></a>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-4 col-md-6">
                <div class="card border-0 shadow-sm text-center">
                    <div class="card-body p-4">
                        <img src="https://via.placeholder.com/150x150/dc3545/ffffff?text=CMO" alt="CMO" class="rounded-circle mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                        <h5 class="card-title">Mike Davis</h5>
                        <p class="text-muted mb-3">Chief Marketing Officer</p>
                        <p class="card-text">Mike leads our marketing efforts, ensuring we connect with customers and build meaningful relationships with our community.</p>
                        <div class="social-links">
                            <a href="#" class="text-primary me-2"><i class="fab fa-linkedin"></i></a>
                            <a href="#" class="text-info me-2"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="text-danger"><i class="fab fa-instagram"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Stats Section -->
<section class="py-5 bg-primary text-white">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-lg-3 col-md-6">
                <div class="mb-3">
                    <i class="fas fa-users fa-3x"></i>
                </div>
                <h3 class="h2 fw-bold">50K+</h3>
                <p class="mb-0">Happy Customers</p>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="mb-3">
                    <i class="fas fa-box fa-3x"></i>
                </div>
                <h3 class="h2 fw-bold">10K+</h3>
                <p class="mb-0">Products Sold</p>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="mb-3">
                    <i class="fas fa-globe fa-3x"></i>
                </div>
                <h3 class="h2 fw-bold">25+</h3>
                <p class="mb-0">Countries Served</p>
            </div>
            <div class="col-lg-3 col-md-6">
                <div class="mb-3">
                    <i class="fas fa-star fa-3x"></i>
                </div>
                <h3 class="h2 fw-bold">4.8</h3>
                <p class="mb-0">Average Rating</p>
            </div>
        </div>
    </div>
</section>

<!-- CTA Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="display-5 fw-bold mb-4">Ready to Start Shopping?</h2>
                <p class="lead text-muted mb-4">Join thousands of satisfied customers and discover amazing products at great prices.</p>
                <div class="d-flex justify-content-center gap-3">
                    <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-shopping-bag me-2"></i>Shop Now
                    </a>
                    <a href="{{ route('contact') }}" class="btn btn-outline-primary btn-lg">
                        <i class="fas fa-envelope me-2"></i>Contact Us
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
