<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Offline - {{ config('app.name') }}</title>
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
        .offline-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .offline-content {
            text-align: center;
            color: white;
            max-width: 500px;
            padding: 2rem;
        }
        .offline-icon {
            width: 120px;
            height: 120px;
            margin: 0 auto 2rem;
            opacity: 0.8;
        }
        .retry-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 2px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .retry-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            border-color: rgba(255, 255, 255, 0.5);
        }
        .offline-features {
            margin-top: 2rem;
            text-align: left;
        }
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
            opacity: 0.9;
        }
        .feature-icon {
            width: 20px;
            height: 20px;
            margin-right: 12px;
            fill: currentColor;
        }
    </style>
</head>
<body>
    <div class="offline-container">
        <div class="offline-content">
            <div class="offline-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/>
                    <path d="M22 18H2"/>
                </svg>
            </div>
            
            <h1 class="text-4xl font-bold mb-4">You're Offline</h1>
            <p class="text-xl mb-6 opacity-90">
                It looks like you've lost your internet connection. Don't worry, you can still browse some content!
            </p>
            
            <button onclick="retryConnection()" class="retry-btn">
                <span id="retry-text">Try Again</span>
                <span id="retry-loading" class="hidden">Checking...</span>
            </button>
            
            <div class="offline-features">
                <h3 class="text-lg font-semibold mb-3">What you can still do:</h3>
                
                <div class="feature-item">
                    <svg class="feature-icon" viewBox="0 0 24 24">
                        <path d="M19 7H5a2 2 0 0 0-2 2v6a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2Z"/>
                        <path d="M8 12h8"/>
                        <path d="M12 8v8"/>
                    </svg>
                    <span>Browse cached products and pages</span>
                </div>
                
                <div class="feature-item">
                    <svg class="feature-icon" viewBox="0 0 24 24">
                        <circle cx="9" cy="21" r="1"/>
                        <circle cx="20" cy="21" r="1"/>
                        <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                    </svg>
                    <span>Add items to cart (will sync when online)</span>
                </div>
                
                <div class="feature-item">
                    <svg class="feature-icon" viewBox="0 0 24 24">
                        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.29 1.51 4.04 3 5.5l7 7Z"/>
                    </svg>
                    <span>Manage your wishlist</span>
                </div>
                
                <div class="feature-item">
                    <svg class="feature-icon" viewBox="0 0 24 24">
                        <path d="M14.5 4h-5L7 7H4a2 2 0 0 0-2 2v9a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2h-3l-2.5-3Z"/>
                        <circle cx="12" cy="13" r="3"/>
                    </svg>
                    <span>View previously loaded content</span>
                </div>
            </div>
            
            <div class="mt-8 text-sm opacity-75">
                <p>Your data will automatically sync when you're back online.</p>
            </div>
        </div>
    </div>

    <script>
        let isRetrying = false;

        async function retryConnection() {
            if (isRetrying) return;
            
            isRetrying = true;
            const retryText = document.getElementById('retry-text');
            const retryLoading = document.getElementById('retry-loading');
            
            retryText.classList.add('hidden');
            retryLoading.classList.remove('hidden');
            
            try {
                // Try to fetch a small resource to test connectivity
                const response = await fetch('/ping', { 
                    method: 'HEAD',
                    cache: 'no-cache'
                });
                
                if (response.ok) {
                    // Connection restored, redirect to home
                    window.location.href = '/';
                } else {
                    throw new Error('Still offline');
                }
            } catch (error) {
                // Still offline
                setTimeout(() => {
                    retryText.classList.remove('hidden');
                    retryLoading.classList.add('hidden');
                    isRetrying = false;
                }, 2000);
            }
        }

        // Auto-retry connection every 30 seconds
        setInterval(() => {
            if (!isRetrying && navigator.onLine) {
                retryConnection();
            }
        }, 30000);

        // Listen for online event
        window.addEventListener('online', () => {
            retryConnection();
        });

        // Show connection status
        window.addEventListener('offline', () => {
            console.log('Connection lost');
        });

        window.addEventListener('online', () => {
            console.log('Connection restored');
        });
    </script>
</body>
</html>
