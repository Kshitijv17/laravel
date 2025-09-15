// PWA Installation and Management
class PWAManager {
    constructor() {
        this.deferredPrompt = null;
        this.isInstalled = false;
        this.init();
    }

    init() {
        // Register service worker
        this.registerServiceWorker();
        
        // Handle install prompt
        this.handleInstallPrompt();
        
        // Check if already installed
        this.checkInstallStatus();
        
        // Handle app updates
        this.handleAppUpdates();
        
        // Setup offline detection
        this.setupOfflineDetection();
        
        // Setup background sync
        this.setupBackgroundSync();
    }

    async registerServiceWorker() {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/sw.js');
                console.log('Service Worker registered:', registration);
                
                // Handle updates
                registration.addEventListener('updatefound', () => {
                    const newWorker = registration.installing;
                    newWorker.addEventListener('statechange', () => {
                        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
                            this.showUpdateNotification();
                        }
                    });
                });
                
                return registration;
            } catch (error) {
                console.error('Service Worker registration failed:', error);
            }
        }
    }

    handleInstallPrompt() {
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            this.deferredPrompt = e;
            this.showInstallBanner();
            
            // Log that install prompt was shown
            this.logInstallPrompt('shown');
        });

        // Handle successful installation
        window.addEventListener('appinstalled', () => {
            console.log('PWA installed successfully');
            this.isInstalled = true;
            this.hideInstallBanner();
            this.logInstallPrompt('accepted');
        });
    }

    showInstallBanner() {
        // Create install banner if it doesn't exist
        if (!document.getElementById('pwa-install-banner')) {
            const banner = document.createElement('div');
            banner.id = 'pwa-install-banner';
            banner.className = 'fixed bottom-4 left-4 right-4 bg-blue-600 text-white p-4 rounded-lg shadow-lg z-50 flex items-center justify-between';
            banner.innerHTML = `
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <div>
                        <div class="font-medium">Install App</div>
                        <div class="text-sm opacity-90">Add to home screen for better experience</div>
                    </div>
                </div>
                <div class="flex space-x-2">
                    <button id="pwa-install-btn" class="bg-white text-blue-600 px-4 py-2 rounded font-medium hover:bg-gray-100">
                        Install
                    </button>
                    <button id="pwa-dismiss-btn" class="text-white hover:text-gray-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            `;
            
            document.body.appendChild(banner);
            
            // Handle install button click
            document.getElementById('pwa-install-btn').addEventListener('click', () => {
                this.promptInstall();
            });
            
            // Handle dismiss button click
            document.getElementById('pwa-dismiss-btn').addEventListener('click', () => {
                this.hideInstallBanner();
                this.logInstallPrompt('dismissed');
            });
        }
    }

    hideInstallBanner() {
        const banner = document.getElementById('pwa-install-banner');
        if (banner) {
            banner.remove();
        }
    }

    async promptInstall() {
        if (this.deferredPrompt) {
            this.deferredPrompt.prompt();
            const { outcome } = await this.deferredPrompt.userChoice;
            
            if (outcome === 'accepted') {
                console.log('User accepted the install prompt');
            } else {
                console.log('User dismissed the install prompt');
                this.logInstallPrompt('dismissed');
            }
            
            this.deferredPrompt = null;
            this.hideInstallBanner();
        }
    }

    checkInstallStatus() {
        // Check if running as PWA
        if (window.matchMedia('(display-mode: standalone)').matches || 
            window.navigator.standalone === true) {
            this.isInstalled = true;
            console.log('Running as installed PWA');
        }
    }

    showUpdateNotification() {
        // Show update notification
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-600 text-white p-4 rounded-lg shadow-lg z-50';
        notification.innerHTML = `
            <div class="flex items-center justify-between">
                <div>
                    <div class="font-medium">Update Available</div>
                    <div class="text-sm opacity-90">A new version is ready</div>
                </div>
                <button id="update-btn" class="ml-4 bg-white text-green-600 px-3 py-1 rounded text-sm font-medium">
                    Update
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        document.getElementById('update-btn').addEventListener('click', () => {
            window.location.reload();
        });
        
        // Auto-remove after 10 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 10000);
    }

    setupOfflineDetection() {
        window.addEventListener('online', () => {
            this.showConnectionStatus('online');
            this.syncPendingData();
        });

        window.addEventListener('offline', () => {
            this.showConnectionStatus('offline');
        });
    }

    showConnectionStatus(status) {
        const existing = document.getElementById('connection-status');
        if (existing) existing.remove();
        
        const statusBar = document.createElement('div');
        statusBar.id = 'connection-status';
        statusBar.className = `fixed top-0 left-0 right-0 z-50 text-center py-2 text-sm font-medium ${
            status === 'online' ? 'bg-green-600 text-white' : 'bg-red-600 text-white'
        }`;
        statusBar.textContent = status === 'online' ? 'Back online' : 'You are offline';
        
        document.body.appendChild(statusBar);
        
        if (status === 'online') {
            setTimeout(() => statusBar.remove(), 3000);
        }
    }

    setupBackgroundSync() {
        if ('serviceWorker' in navigator && 'sync' in window.ServiceWorkerRegistration.prototype) {
            // Register for background sync when cart is updated
            window.addEventListener('cart-updated', () => {
                navigator.serviceWorker.ready.then(registration => {
                    return registration.sync.register('cart-sync');
                });
            });
            
            // Register for background sync when order is placed
            window.addEventListener('order-placed', () => {
                navigator.serviceWorker.ready.then(registration => {
                    return registration.sync.register('order-sync');
                });
            });
        }
    }

    async syncPendingData() {
        if ('serviceWorker' in navigator) {
            const registration = await navigator.serviceWorker.ready;
            
            // Trigger sync events
            if ('sync' in registration) {
                registration.sync.register('cart-sync');
                registration.sync.register('order-sync');
            }
        }
    }

    async logInstallPrompt(action) {
        try {
            await fetch('/pwa/install-prompt', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                },
                body: JSON.stringify({ action })
            });
        } catch (error) {
            console.error('Failed to log install prompt:', error);
        }
    }

    // Public methods for integration
    async requestNotificationPermission() {
        if ('Notification' in window) {
            const permission = await Notification.requestPermission();
            return permission === 'granted';
        }
        return false;
    }

    async subscribeToPushNotifications() {
        if ('serviceWorker' in navigator && 'PushManager' in window) {
            try {
                const registration = await navigator.serviceWorker.ready;
                const subscription = await registration.pushManager.subscribe({
                    userVisibleOnly: true,
                    applicationServerKey: this.urlBase64ToUint8Array(window.vapidPublicKey)
                });
                
                // Send subscription to server
                await fetch('/api/push-subscriptions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
                    },
                    body: JSON.stringify(subscription)
                });
                
                return subscription;
            } catch (error) {
                console.error('Failed to subscribe to push notifications:', error);
                return null;
            }
        }
    }

    urlBase64ToUint8Array(base64String) {
        const padding = '='.repeat((4 - base64String.length % 4) % 4);
        const base64 = (base64String + padding)
            .replace(/-/g, '+')
            .replace(/_/g, '/');

        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let i = 0; i < rawData.length; ++i) {
            outputArray[i] = rawData.charCodeAt(i);
        }
        return outputArray;
    }

    // Offline storage helpers
    async storeOfflineData(key, data) {
        if ('caches' in window) {
            try {
                const cache = await caches.open('dynamic-v1');
                const response = new Response(JSON.stringify(data));
                await cache.put(`/offline-data/${key}`, response);
            } catch (error) {
                console.error('Failed to store offline data:', error);
            }
        }
    }

    async getOfflineData(key) {
        if ('caches' in window) {
            try {
                const cache = await caches.open('dynamic-v1');
                const response = await cache.match(`/offline-data/${key}`);
                if (response) {
                    return await response.json();
                }
            } catch (error) {
                console.error('Failed to get offline data:', error);
            }
        }
        return null;
    }
}

// Initialize PWA Manager
const pwaManager = new PWAManager();

// Export for global use
window.pwaManager = pwaManager;

// Integration with existing cart functionality
document.addEventListener('DOMContentLoaded', function() {
    // Override cart update functions to trigger sync
    const originalAddToCart = window.addToCart;
    if (originalAddToCart) {
        window.addToCart = function(...args) {
            const result = originalAddToCart.apply(this, args);
            window.dispatchEvent(new CustomEvent('cart-updated'));
            return result;
        };
    }
    
    // Setup notification permission request
    const notificationBtn = document.getElementById('enable-notifications');
    if (notificationBtn) {
        notificationBtn.addEventListener('click', async () => {
            const granted = await pwaManager.requestNotificationPermission();
            if (granted) {
                await pwaManager.subscribeToPushNotifications();
                notificationBtn.textContent = 'Notifications Enabled';
                notificationBtn.disabled = true;
            }
        });
    }
});
