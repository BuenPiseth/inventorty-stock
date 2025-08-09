/**
 * DEER BAKERY & CAKE - Security Manager
 * 
 * Handles security features including CSRF protection,
 * permission checking, and secure API communications.
 * 
 * @version 1.0.0
 */

export class SecurityManager {
    static csrfToken = null;
    static userPermissions = new Set();
    static sessionTimeout = null;
    static lastActivity = Date.now();

    /**
     * Initialize security manager
     */
    static initialize() {
        this.setupCSRFToken();
        this.setupPermissions();
        this.setupSessionManagement();
        this.setupSecurityHeaders();
        console.log('🔒 Security Manager initialized');
    }

    /**
     * Setup CSRF token for all requests
     */
    static setupCSRFToken() {
        // Get CSRF token from meta tag
        const tokenMeta = document.querySelector('meta[name="csrf-token"]');
        if (tokenMeta) {
            this.csrfToken = tokenMeta.getAttribute('content');
            
            // Set default header for axios
            if (window.axios) {
                window.axios.defaults.headers.common['X-CSRF-TOKEN'] = this.csrfToken;
            }
        }

        // Setup CSRF for all forms
        this.setupFormCSRF();
    }

    /**
     * Setup CSRF tokens for forms
     */
    static setupFormCSRF() {
        document.addEventListener('submit', (event) => {
            const form = event.target;
            if (form.method.toLowerCase() === 'post' && !form.querySelector('input[name="_token"]')) {
                // Add CSRF token to forms that don't have it
                const csrfInput = document.createElement('input');
                csrfInput.type = 'hidden';
                csrfInput.name = '_token';
                csrfInput.value = this.csrfToken;
                form.appendChild(csrfInput);
            }
        });
    }

    /**
     * Setup user permissions
     */
    static setupPermissions() {
        // Get permissions from global variable or API
        if (window.userPermissions) {
            this.userPermissions = new Set(window.userPermissions);
        } else {
            this.loadPermissions();
        }
    }

    /**
     * Load user permissions from API
     */
    static async loadPermissions() {
        try {
            const response = await window.axios.get('/api/user/permissions');
            this.userPermissions = new Set(response.data.permissions || []);
        } catch (error) {
            console.warn('Failed to load user permissions:', error);
        }
    }

    /**
     * Check if user has permission
     */
    static hasPermission(permission) {
        return this.userPermissions.has(permission) || this.userPermissions.has('admin');
    }

    /**
     * Check multiple permissions (AND logic)
     */
    static hasAllPermissions(permissions) {
        return permissions.every(permission => this.hasPermission(permission));
    }

    /**
     * Check multiple permissions (OR logic)
     */
    static hasAnyPermission(permissions) {
        return permissions.some(permission => this.hasPermission(permission));
    }

    /**
     * Setup session management
     */
    static setupSessionManagement() {
        // Track user activity
        this.trackActivity();
        
        // Setup session timeout warning
        this.setupSessionTimeout();
        
        // Setup automatic logout
        this.setupAutoLogout();
    }

    /**
     * Track user activity
     */
    static trackActivity() {
        const events = ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart', 'click'];
        
        events.forEach(event => {
            document.addEventListener(event, () => {
                this.lastActivity = Date.now();
            }, { passive: true });
        });
    }

    /**
     * Setup session timeout warning
     */
    static setupSessionTimeout() {
        const sessionLifetime = 120 * 60 * 1000; // 2 hours in milliseconds
        const warningTime = 15 * 60 * 1000; // 15 minutes before expiry
        
        setInterval(() => {
            const timeSinceActivity = Date.now() - this.lastActivity;
            const timeUntilExpiry = sessionLifetime - timeSinceActivity;
            
            if (timeUntilExpiry <= warningTime && timeUntilExpiry > 0) {
                this.showSessionWarning(Math.ceil(timeUntilExpiry / 60000));
            }
        }, 60000); // Check every minute
    }

    /**
     * Show session timeout warning
     */
    static showSessionWarning(minutesLeft) {
        if (window.NotificationSystem) {
            window.NotificationSystem.warning('Session Expiring Soon', {
                message: `Your session will expire in ${minutesLeft} minutes. Click to extend.`,
                duration: 0, // Don't auto-hide
                actions: [
                    { label: 'Extend Session', action: 'extend-session', type: 'primary' },
                    { label: 'Logout Now', action: 'logout', type: 'secondary' }
                ]
            });
        }
    }

    /**
     * Setup automatic logout
     */
    static setupAutoLogout() {
        // Listen for session extension
        document.addEventListener('notification-action', (event) => {
            if (event.detail.action === 'extend-session') {
                this.extendSession();
            } else if (event.detail.action === 'logout') {
                this.logout();
            }
        });
    }

    /**
     * Extend user session
     */
    static async extendSession() {
        try {
            await window.axios.post('/api/session/extend');
            this.lastActivity = Date.now();
            
            if (window.NotificationSystem) {
                window.NotificationSystem.success('Session Extended', {
                    message: 'Your session has been extended successfully.',
                    duration: 3000
                });
            }
        } catch (error) {
            console.error('Failed to extend session:', error);
            this.logout();
        }
    }

    /**
     * Logout user
     */
    static logout() {
        window.location.href = '/logout';
    }

    /**
     * Setup security headers
     */
    static setupSecurityHeaders() {
        // Setup Content Security Policy
        if (!document.querySelector('meta[http-equiv="Content-Security-Policy"]')) {
            const cspMeta = document.createElement('meta');
            cspMeta.setAttribute('http-equiv', 'Content-Security-Policy');
            cspMeta.setAttribute('content', "default-src 'self'; script-src 'self' 'unsafe-inline'; style-src 'self' 'unsafe-inline';");
            document.head.appendChild(cspMeta);
        }
    }

    /**
     * Sanitize HTML input
     */
    static sanitizeHTML(html) {
        const temp = document.createElement('div');
        temp.textContent = html;
        return temp.innerHTML;
    }

    /**
     * Validate input against XSS
     */
    static validateInput(input) {
        const dangerous = /<script|javascript:|on\w+=/i;
        return !dangerous.test(input);
    }

    /**
     * Secure API request wrapper
     */
    static async secureRequest(url, options = {}) {
        // Add security headers
        const secureOptions = {
            ...options,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': this.csrfToken,
                ...options.headers
            }
        };

        try {
            const response = await window.axios(url, secureOptions);
            return response;
        } catch (error) {
            if (error.response?.status === 419) {
                // CSRF token mismatch
                this.handleCSRFError();
            } else if (error.response?.status === 401) {
                // Unauthorized
                this.handleUnauthorized();
            } else if (error.response?.status === 403) {
                // Forbidden
                this.handleForbidden();
            }
            throw error;
        }
    }

    /**
     * Handle CSRF token errors
     */
    static handleCSRFError() {
        if (window.NotificationSystem) {
            window.NotificationSystem.error('Security Error', {
                message: 'Your session has expired. Please refresh the page.',
                actions: [
                    { label: 'Refresh Page', action: 'refresh', type: 'primary' }
                ]
            });
        }

        // Listen for refresh action
        document.addEventListener('notification-action', (event) => {
            if (event.detail.action === 'refresh') {
                window.location.reload();
            }
        }, { once: true });
    }

    /**
     * Handle unauthorized access
     */
    static handleUnauthorized() {
        if (window.NotificationSystem) {
            window.NotificationSystem.error('Access Denied', {
                message: 'You are not authorized to perform this action.',
                duration: 5000
            });
        }
    }

    /**
     * Handle forbidden access
     */
    static handleForbidden() {
        if (window.NotificationSystem) {
            window.NotificationSystem.warning('Permission Required', {
                message: 'You do not have permission to access this resource.',
                duration: 5000
            });
        }
    }

    /**
     * Encrypt sensitive data (basic implementation)
     */
    static encryptData(data) {
        // Basic encoding - in production, use proper encryption
        return btoa(JSON.stringify(data));
    }

    /**
     * Decrypt sensitive data
     */
    static decryptData(encryptedData) {
        try {
            return JSON.parse(atob(encryptedData));
        } catch (error) {
            console.error('Failed to decrypt data:', error);
            return null;
        }
    }

    /**
     * Generate secure random string
     */
    static generateSecureRandom(length = 32) {
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let result = '';
        
        for (let i = 0; i < length; i++) {
            result += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        
        return result;
    }

    /**
     * Log security event
     */
    static logSecurityEvent(event, details = {}) {
        const logData = {
            event,
            timestamp: new Date().toISOString(),
            userAgent: navigator.userAgent,
            url: window.location.href,
            ...details
        };

        console.log('Security Event:', logData);

        // In production, send to security monitoring service
        if (window.securityLogger) {
            window.securityLogger.log(logData);
        }
    }

    /**
     * Check if current connection is secure
     */
    static isSecureConnection() {
        return window.location.protocol === 'https:' || window.location.hostname === 'localhost';
    }

    /**
     * Validate file upload security
     */
    static validateFileUpload(file) {
        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        const maxSize = 5 * 1024 * 1024; // 5MB

        if (!allowedTypes.includes(file.type)) {
            throw new Error('Invalid file type. Only images are allowed.');
        }

        if (file.size > maxSize) {
            throw new Error('File too large. Maximum size is 5MB.');
        }

        return true;
    }
}
