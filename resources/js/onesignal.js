/**
 * OneSignal Push Notification Manager
 * Improved flow with better permission handling and user experience
 */

class OneSignalManager {
    constructor() {
        this.appId = "f2760196-0a7e-4680-a744-f415b7ba5901";
        this.safariWebId = "web.onesignal.auto.32f1a686-ea76-4ac6-93be-f9d8958aaa5a";
        this.oneSignal = null;
        this.isInitialized = false;
        this.permissionStatus = null;
        this.isSubscribed = false;
        this.promptShown = false;
        this.initDelay = 3000; // Wait 3 seconds before showing prompt
    }

    /**
     * Initialize OneSignal
     */
    async init() {
        try {
            // Wait for OneSignal SDK to load
            if (typeof OneSignal === 'undefined') {
                console.error('OneSignal SDK not loaded');
                return;
            }

            // Initialize OneSignal
            await OneSignal.init({
                appId: this.appId,
                safari_web_id: this.safariWebId,
                notifyButton: {
                    enable: true,
                    size: "medium",
                    position: "bottom-right",
                    showCredit: false,
                    text: {
                        "tip.state.unsubscribed": "Subscribe to notifications",
                        "tip.state.subscribed": "You're subscribed to notifications",
                        "tip.state.blocked": "You've blocked notifications"
                    }
                },
                serviceWorkerParam: {
                    scope: "/"
                },
                serviceWorkerPath: "OneSignalSDKWorker.js",
                welcomeNotification: {
                    title: "Welcome to Nazaarabox!",
                    message: "You'll now receive notifications about new movies and TV shows."
                },
                promptOptions: {
                    slidedown: {
                        enabled: true,
                        actionMessage: "Stay updated with the latest movies and TV shows!",
                        acceptButtonText: "Allow",
                        cancelButtonText: "Maybe Later",
                        categories: {
                            tags: [
                                {
                                    tag: "updates",
                                    label: "New Content Updates"
                                },
                                {
                                    tag: "releases",
                                    label: "New Releases"
                                }
                            ]
                        }
                    }
                }
            });

            this.oneSignal = OneSignal;
            this.isInitialized = true;

            // Check current status
            await this.checkStatus();

            // Setup event listeners
            this.setupEventListeners();

            // Show prompt if needed
            await this.handleSubscriptionPrompt();

        } catch (error) {
            console.error('OneSignal initialization error:', error);
            this.showError('Failed to initialize notifications. Please refresh the page.');
        }
    }

    /**
     * Check current subscription and permission status
     */
    async checkStatus() {
        try {
            if (!this.isInitialized) return;

            // Check permission status
            this.permissionStatus = await OneSignal.Notifications.permissionNative;
            
            // Check if subscribed
            const subscriptionId = await OneSignal.User.PushSubscription.id;
            this.isSubscribed = !!subscriptionId;

            console.log('OneSignal Status:', {
                permission: this.permissionStatus,
                subscribed: this.isSubscribed,
                subscriptionId: subscriptionId
            });

            return {
                permission: this.permissionStatus,
                subscribed: this.isSubscribed
            };
        } catch (error) {
            console.error('Error checking OneSignal status:', error);
            return null;
        }
    }

    /**
     * Setup event listeners for subscription changes
     */
    setupEventListeners() {
        if (!this.isInitialized) return;

        // Listen for subscription changes
        OneSignal.Notifications.addEventListener('subscriptionChange', (isSubscribed) => {
            console.log('Subscription changed:', isSubscribed);
            this.isSubscribed = isSubscribed;
            
            if (isSubscribed) {
                this.showSuccess('Successfully subscribed to notifications!');
                this.hideBlockedBanner();
            } else {
                this.showInfo('You have unsubscribed from notifications.');
            }
        });

        // Listen for permission changes
        OneSignal.Notifications.addEventListener('permissionPromptDisplay', () => {
            console.log('Permission prompt displayed');
            this.promptShown = true;
        });

        // Listen for click events
        OneSignal.Notifications.addEventListener('click', (event) => {
            console.log('Notification clicked:', event);
            // Handle notification click if needed
        });
    }

    /**
     * Handle subscription prompt logic
     */
    async handleSubscriptionPrompt() {
        if (!this.isInitialized) return;

        const status = await this.checkStatus();
        if (!status) return;

        // If already subscribed, do nothing
        if (status.subscribed) {
            console.log('User is already subscribed');
            return;
        }

        // If permission is denied/blocked, show help message
        if (status.permission === 'denied') {
            console.log('Notifications are blocked');
            this.showBlockedBanner();
            return;
        }

        // If permission is default (not asked yet), show prompt after delay
        if (status.permission === 'default') {
            console.log('Permission not yet requested, will show prompt');
            setTimeout(() => {
                this.showSubscriptionPrompt();
            }, this.initDelay);
            return;
        }

        // If permission is granted but not subscribed, try to subscribe
        if (status.permission === 'granted' && !status.subscribed) {
            console.log('Permission granted but not subscribed, attempting to subscribe');
            await this.subscribe();
        }
    }

    /**
     * Show subscription prompt
     */
    async showSubscriptionPrompt() {
        if (!this.isInitialized || this.promptShown) return;

        try {
            // Check status again before showing
            const status = await this.checkStatus();
            if (status && status.subscribed) {
                return; // Already subscribed
            }

            // Show slidedown prompt
            await OneSignal.Slidedown.promptPush();
            this.promptShown = true;
        } catch (error) {
            console.error('Error showing subscription prompt:', error);
            
            // Fallback: Try direct permission request
            try {
                await this.requestPermission();
            } catch (fallbackError) {
                console.error('Fallback permission request failed:', fallbackError);
                this.showError('Unable to request notification permission. Please check your browser settings.');
            }
        }
    }

    /**
     * Request notification permission directly
     */
    async requestPermission() {
        if (!this.isInitialized) {
            this.showError('Notifications not initialized. Please refresh the page.');
            return false;
        }

        try {
            const permission = await OneSignal.Notifications.requestPermission();
            
            if (permission === 'granted') {
                // Try to subscribe after permission is granted
                await this.subscribe();
                this.showSuccess('Notifications enabled successfully!');
                this.hideBlockedBanner();
                return true;
            } else if (permission === 'denied') {
                this.showBlockedBanner();
                this.showError('Notifications were blocked. Please enable them in your browser settings.');
                return false;
            } else {
                this.showInfo('Notification permission was not granted.');
                return false;
            }
        } catch (error) {
            console.error('Error requesting permission:', error);
            this.showError('Failed to request notification permission.');
            return false;
        }
    }

    /**
     * Subscribe to notifications
     */
    async subscribe() {
        if (!this.isInitialized) return false;

        try {
            const status = await this.checkStatus();
            
            if (status && status.subscribed) {
                console.log('Already subscribed');
                return true;
            }

            if (status && status.permission !== 'granted') {
                console.log('Permission not granted, requesting...');
                const granted = await this.requestPermission();
                if (!granted) return false;
            }

            // OneSignal should auto-subscribe when permission is granted
            // But we can check and ensure subscription
            const subscriptionId = await OneSignal.User.PushSubscription.id;
            if (subscriptionId) {
                this.isSubscribed = true;
                return true;
            }

            return false;
        } catch (error) {
            console.error('Error subscribing:', error);
            return false;
        }
    }

    /**
     * Unsubscribe from notifications
     */
    async unsubscribe() {
        if (!this.isInitialized) return false;

        try {
            await OneSignal.User.PushSubscription.optOut();
            this.isSubscribed = false;
            this.showInfo('You have been unsubscribed from notifications.');
            return true;
        } catch (error) {
            console.error('Error unsubscribing:', error);
            return false;
        }
    }

    /**
     * Show blocked notification banner
     */
    showBlockedBanner() {
        // Remove existing banner if any
        const existing = document.getElementById('onesignal-blocked-banner');
        if (existing) return;

        const banner = document.createElement('div');
        banner.id = 'onesignal-blocked-banner';
        banner.className = 'fixed bottom-4 left-4 right-4 md:left-auto md:right-4 md:w-96 bg-yellow-500 text-white p-4 rounded-lg shadow-lg z-50 animate-slide-up';
        banner.style.fontFamily = "'Poppins', sans-serif";
        banner.innerHTML = `
            <div class="flex items-start gap-3">
                <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                <div class="flex-1">
                    <h3 class="font-bold text-sm mb-1">Notifications Blocked</h3>
                    <p class="text-xs mb-3">To receive updates, please enable notifications in your browser settings.</p>
                    <div class="flex flex-col gap-2">
                        <button onclick="window.oneSignalManager.openBrowserSettings()" class="text-xs bg-white text-yellow-600 px-4 py-2 rounded hover:bg-yellow-50 font-semibold transition-colors">
                            How to Enable
                        </button>
                        <button onclick="window.oneSignalManager.hideBlockedBanner()" class="text-xs underline hover:no-underline text-right">
                            Dismiss
                        </button>
                    </div>
                </div>
            </div>
        `;
        document.body.appendChild(banner);
    }

    /**
     * Hide blocked notification banner
     */
    hideBlockedBanner() {
        const banner = document.getElementById('onesignal-blocked-banner');
        if (banner) {
            banner.style.opacity = '0';
            banner.style.transform = 'translateY(20px)';
            setTimeout(() => banner.remove(), 300);
        }
    }

    /**
     * Open browser settings help
     */
    openBrowserSettings() {
        const userAgent = navigator.userAgent.toLowerCase();
        let instructions = '';

        if (userAgent.includes('chrome') || userAgent.includes('edge')) {
            instructions = 'Chrome/Edge: Click the lock icon in the address bar → Site settings → Notifications → Allow';
        } else if (userAgent.includes('firefox')) {
            instructions = 'Firefox: Click the lock icon in the address bar → Permissions → Notifications → Allow';
        } else if (userAgent.includes('safari')) {
            instructions = 'Safari: Safari menu → Preferences → Websites → Notifications → Allow';
        } else {
            instructions = 'Go to your browser settings and enable notifications for this site.';
        }

        this.showInfo(instructions, 8000);
    }

    /**
     * Show success message
     */
    showSuccess(message) {
        this.showToast(message, 'success');
    }

    /**
     * Show error message
     */
    showError(message) {
        this.showToast(message, 'error');
    }

    /**
     * Show info message
     */
    showInfo(message, duration = 5000) {
        this.showToast(message, 'info', duration);
    }

    /**
     * Show toast notification
     */
    showToast(message, type = 'info', duration = 5000) {
        const colors = {
            success: 'bg-green-500',
            error: 'bg-red-500',
            info: 'bg-blue-500'
        };

        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 ${colors[type]} text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-slide-down`;
        toast.style.fontFamily = "'Poppins', sans-serif";
        toast.style.fontWeight = '500';
        toast.textContent = message;
        
        document.body.appendChild(toast);
        
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(-20px)';
            setTimeout(() => toast.remove(), 300);
        }, duration);
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    // Wait for OneSignal SDK to load
    if (window.OneSignalDeferred) {
        window.OneSignalDeferred.push(async function(OneSignal) {
            window.oneSignalManager = new OneSignalManager();
            await window.oneSignalManager.init();
        });
    } else {
        // Fallback: Initialize after a delay
        setTimeout(() => {
            if (typeof OneSignal !== 'undefined') {
                window.oneSignalManager = new OneSignalManager();
                window.oneSignalManager.init();
            }
        }, 1000);
    }
});

// Make manager globally available
window.OneSignalManager = OneSignalManager;

