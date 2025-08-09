/**
 * DEER BAKERY & CAKE - Bootstrap Configuration
 *
 * Enhanced bootstrap configuration with security, error handling,
 * and inventory management optimizations.
 */

import axios from 'axios';

// Configure Axios for DEER BAKERY & CAKE
window.axios = axios;

// Default headers
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.headers.common['Accept'] = 'application/json';
window.axios.defaults.timeout = 30000; // 30 second timeout

// CSRF Token setup
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found');
}

// Request interceptor for enhanced security and logging
window.axios.interceptors.request.use(
    (config) => {
        // Add timestamp for performance tracking
        config.metadata = { startTime: new Date() };

        // Log API requests in development
        if (import.meta.env.DEV) {
            console.log(`🌐 API Request: ${config.method?.toUpperCase()} ${config.url}`);
        }

        return config;
    },
    (error) => {
        console.error('Request Error:', error);
        return Promise.reject(error);
    }
);

// Response interceptor for error handling and performance tracking
window.axios.interceptors.response.use(
    (response) => {
        // Calculate request duration
        const duration = new Date() - response.config.metadata.startTime;

        // Log slow requests
        if (duration > 2000) {
            console.warn(`🐌 Slow API Response: ${response.config.url} took ${duration}ms`);
        }

        // Log successful requests in development
        if (import.meta.env.DEV) {
            console.log(`✅ API Success: ${response.config.method?.toUpperCase()} ${response.config.url} (${duration}ms)`);
        }

        return response;
    },
    (error) => {
        const duration = error.config?.metadata ? new Date() - error.config.metadata.startTime : 0;

        // Enhanced error logging
        console.error(`❌ API Error: ${error.config?.method?.toUpperCase()} ${error.config?.url} (${duration}ms)`, {
            status: error.response?.status,
            statusText: error.response?.statusText,
            data: error.response?.data,
            duration
        });

        // Handle specific error cases
        if (error.response) {
            switch (error.response.status) {
                case 401:
                    console.warn('Unauthorized access - redirecting to login');
                    if (!window.location.pathname.includes('/login')) {
                        window.location.href = '/login';
                    }
                    break;

                case 403:
                    console.warn('Forbidden access');
                    break;

                case 419:
                    console.warn('CSRF token mismatch - page refresh required');
                    break;

                case 422:
                    console.warn('Validation errors:', error.response.data.errors);
                    break;

                case 429:
                    console.warn('Rate limit exceeded');
                    break;

                case 500:
                    console.error('Server error');
                    break;

                case 503:
                    console.warn('Service unavailable');
                    break;
            }
        } else if (error.request) {
            console.error('Network error - no response received');
        } else {
            console.error('Request setup error:', error.message);
        }

        return Promise.reject(error);
    }
);

// Bootstrap integration for inventory system
// Note: Bootstrap CSS/JS should be loaded via CDN or separate build process
// This is just a placeholder for future Bootstrap integration
if (typeof window.bootstrap === 'undefined') {
    console.log('📦 Bootstrap not found - using fallback utilities');
    // Create minimal bootstrap-like utilities
    window.bootstrap = {
        Modal: class {
            constructor() {}
            show() {}
            hide() {}
        },
        Tooltip: class {
            constructor() {}
        }
    };
}

// Global utilities for DEER BAKERY & CAKE
window.DeerUtils = {
    // Format currency for bakery pricing
    formatCurrency: (amount) => {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount || 0);
    },

    // Format date for inventory operations
    formatDate: (date, options = {}) => {
        const defaultOptions = {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        };
        return new Date(date).toLocaleDateString('en-US', { ...defaultOptions, ...options });
    },

    // Debounce function for search inputs
    debounce: (func, wait) => {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    },

    // Show loading state
    showLoading: (element) => {
        if (element) {
            element.classList.add('loading');
            element.setAttribute('disabled', 'disabled');
        }
    },

    // Hide loading state
    hideLoading: (element) => {
        if (element) {
            element.classList.remove('loading');
            element.removeAttribute('disabled');
        }
    }
};

console.log('🦌 DEER BAKERY & CAKE Bootstrap initialized');
