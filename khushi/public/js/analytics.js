// Analytics Tracking Script
class AnalyticsTracker {
    constructor() {
        this.sessionId = this.getSessionId();
        this.userId = this.getUserId();
        this.init();
    }

    init() {
        this.trackPageView();
        this.setupEventListeners();
        this.trackUserBehavior();
    }

    getSessionId() {
        let sessionId = sessionStorage.getItem('analytics_session_id');
        if (!sessionId) {
            sessionId = 'session_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            sessionStorage.setItem('analytics_session_id', sessionId);
        }
        return sessionId;
    }

    getUserId() {
        // Get user ID from meta tag or global variable
        const userMeta = document.querySelector('meta[name="user-id"]');
        return userMeta ? userMeta.getAttribute('content') : null;
    }

    trackEvent(eventType, data = {}) {
        const eventData = {
            event_type: eventType,
            data: {
                ...data,
                timestamp: new Date().toISOString(),
                page_url: window.location.href,
                page_title: document.title,
                user_agent: navigator.userAgent,
                screen_resolution: `${screen.width}x${screen.height}`,
                viewport_size: `${window.innerWidth}x${window.innerHeight}`,
                referrer: document.referrer
            }
        };

        // Send to server
        fetch('/analytics/track', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(eventData)
        }).catch(error => {
            console.warn('Analytics tracking failed:', error);
        });

        // Store in local storage as backup
        this.storeEventLocally(eventData);
    }

    trackPageView() {
        this.trackEvent('page_view', {
            page_path: window.location.pathname,
            page_search: window.location.search,
            page_hash: window.location.hash
        });
    }

    trackProductView(productId, productName, category, price) {
        this.trackEvent('product_view', {
            product_id: productId,
            product_name: productName,
            category: category,
            price: price
        });
    }

    trackAddToCart(productId, productName, quantity, price) {
        this.trackEvent('add_to_cart', {
            product_id: productId,
            product_name: productName,
            quantity: quantity,
            price: price,
            total_value: quantity * price
        });
    }

    trackPurchase(orderId, totalAmount, items) {
        this.trackEvent('purchase', {
            order_id: orderId,
            total_amount: totalAmount,
            items: items,
            currency: 'USD'
        });
    }

    trackSearch(query, resultsCount, filters = {}) {
        this.trackEvent('search', {
            query: query,
            results_count: resultsCount,
            filters: filters
        });
    }

    trackFormSubmission(formName, formData = {}) {
        this.trackEvent('form_submit', {
            form_name: formName,
            form_data: formData
        });
    }

    trackDownload(fileName, fileType, fileSize) {
        this.trackEvent('download', {
            file_name: fileName,
            file_type: fileType,
            file_size: fileSize
        });
    }

    trackVideoPlay(videoTitle, videoDuration) {
        this.trackEvent('video_play', {
            video_title: videoTitle,
            video_duration: videoDuration
        });
    }

    setupEventListeners() {
        // Track clicks on important elements
        document.addEventListener('click', (e) => {
            const target = e.target.closest('[data-analytics]');
            if (target) {
                const eventType = target.getAttribute('data-analytics');
                const eventData = JSON.parse(target.getAttribute('data-analytics-data') || '{}');
                this.trackEvent(eventType, eventData);
            }

            // Track external links
            const link = e.target.closest('a[href^="http"]');
            if (link && !link.href.includes(window.location.hostname)) {
                this.trackEvent('external_link_click', {
                    url: link.href,
                    text: link.textContent.trim()
                });
            }

            // Track download links
            const downloadLink = e.target.closest('a[download], a[href$=".pdf"], a[href$=".zip"], a[href$=".doc"], a[href$=".docx"]');
            if (downloadLink) {
                const fileName = downloadLink.getAttribute('download') || downloadLink.href.split('/').pop();
                const fileType = fileName.split('.').pop();
                this.trackDownload(fileName, fileType, null);
            }
        });

        // Track form submissions
        document.addEventListener('submit', (e) => {
            const form = e.target;
            if (form.tagName === 'FORM') {
                const formName = form.getAttribute('name') || form.getAttribute('id') || 'unnamed_form';
                const formData = new FormData(form);
                const formObject = {};
                
                for (let [key, value] of formData.entries()) {
                    // Don't track sensitive data
                    if (!['password', 'credit_card', 'ssn', 'cvv'].some(sensitive => key.toLowerCase().includes(sensitive))) {
                        formObject[key] = value;
                    }
                }
                
                this.trackFormSubmission(formName, formObject);
            }
        });

        // Track scroll depth
        let maxScrollDepth = 0;
        let scrollTimer;
        
        window.addEventListener('scroll', () => {
            clearTimeout(scrollTimer);
            scrollTimer = setTimeout(() => {
                const scrollDepth = Math.round((window.scrollY / (document.body.scrollHeight - window.innerHeight)) * 100);
                if (scrollDepth > maxScrollDepth) {
                    maxScrollDepth = scrollDepth;
                    
                    // Track milestone scroll depths
                    if ([25, 50, 75, 90].includes(scrollDepth)) {
                        this.trackEvent('scroll_depth', {
                            depth_percentage: scrollDepth,
                            page_height: document.body.scrollHeight,
                            viewport_height: window.innerHeight
                        });
                    }
                }
            }, 100);
        });

        // Track time on page
        let startTime = Date.now();
        let isActive = true;
        
        // Track when user becomes inactive
        ['blur', 'visibilitychange'].forEach(event => {
            document.addEventListener(event, () => {
                if (document.hidden || !document.hasFocus()) {
                    isActive = false;
                    this.trackTimeOnPage(startTime, Date.now());
                }
            });
        });
        
        // Track when user becomes active again
        ['focus', 'visibilitychange'].forEach(event => {
            document.addEventListener(event, () => {
                if (!document.hidden && document.hasFocus()) {
                    isActive = true;
                    startTime = Date.now();
                }
            });
        });
        
        // Track time on page when leaving
        window.addEventListener('beforeunload', () => {
            if (isActive) {
                this.trackTimeOnPage(startTime, Date.now());
            }
        });
    }

    trackTimeOnPage(startTime, endTime) {
        const timeSpent = Math.round((endTime - startTime) / 1000); // in seconds
        
        if (timeSpent > 5) { // Only track if more than 5 seconds
            this.trackEvent('time_on_page', {
                time_spent: timeSpent,
                page_url: window.location.href
            });
        }
    }

    trackUserBehavior() {
        // Track device and browser info
        this.trackEvent('user_info', {
            device_type: this.getDeviceType(),
            browser: this.getBrowserInfo(),
            os: this.getOSInfo(),
            language: navigator.language,
            timezone: Intl.DateTimeFormat().resolvedOptions().timeZone,
            connection_type: navigator.connection ? navigator.connection.effectiveType : 'unknown'
        });
    }

    getDeviceType() {
        const userAgent = navigator.userAgent;
        if (/tablet|ipad|playbook|silk/i.test(userAgent)) {
            return 'tablet';
        }
        if (/mobile|iphone|ipod|android|blackberry|opera|mini|windows\sce|palm|smartphone|iemobile/i.test(userAgent)) {
            return 'mobile';
        }
        return 'desktop';
    }

    getBrowserInfo() {
        const userAgent = navigator.userAgent;
        let browser = 'Unknown';
        
        if (userAgent.includes('Chrome')) browser = 'Chrome';
        else if (userAgent.includes('Firefox')) browser = 'Firefox';
        else if (userAgent.includes('Safari')) browser = 'Safari';
        else if (userAgent.includes('Edge')) browser = 'Edge';
        else if (userAgent.includes('Opera')) browser = 'Opera';
        
        return browser;
    }

    getOSInfo() {
        const userAgent = navigator.userAgent;
        let os = 'Unknown';
        
        if (userAgent.includes('Windows')) os = 'Windows';
        else if (userAgent.includes('Mac')) os = 'macOS';
        else if (userAgent.includes('Linux')) os = 'Linux';
        else if (userAgent.includes('Android')) os = 'Android';
        else if (userAgent.includes('iOS')) os = 'iOS';
        
        return os;
    }

    storeEventLocally(eventData) {
        try {
            let storedEvents = JSON.parse(localStorage.getItem('analytics_events') || '[]');
            storedEvents.push(eventData);
            
            // Keep only last 100 events
            if (storedEvents.length > 100) {
                storedEvents = storedEvents.slice(-100);
            }
            
            localStorage.setItem('analytics_events', JSON.stringify(storedEvents));
        } catch (error) {
            console.warn('Failed to store analytics event locally:', error);
        }
    }

    // Method to sync offline events when connection is restored
    syncOfflineEvents() {
        try {
            const storedEvents = JSON.parse(localStorage.getItem('analytics_events') || '[]');
            
            if (storedEvents.length > 0) {
                fetch('/analytics/batch-track', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ events: storedEvents })
                }).then(() => {
                    localStorage.removeItem('analytics_events');
                }).catch(error => {
                    console.warn('Failed to sync offline analytics events:', error);
                });
            }
        } catch (error) {
            console.warn('Failed to sync offline events:', error);
        }
    }
}

// Initialize analytics when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.analytics = new AnalyticsTracker();
    
    // Sync offline events when online
    window.addEventListener('online', () => {
        window.analytics.syncOfflineEvents();
    });
});

// Expose tracking methods globally for easy access
window.trackProductView = (productId, productName, category, price) => {
    if (window.analytics) {
        window.analytics.trackProductView(productId, productName, category, price);
    }
};

window.trackAddToCart = (productId, productName, quantity, price) => {
    if (window.analytics) {
        window.analytics.trackAddToCart(productId, productName, quantity, price);
    }
};

window.trackPurchase = (orderId, totalAmount, items) => {
    if (window.analytics) {
        window.analytics.trackPurchase(orderId, totalAmount, items);
    }
};

window.trackSearch = (query, resultsCount, filters = {}) => {
    if (window.analytics) {
        window.analytics.trackSearch(query, resultsCount, filters);
    }
};
