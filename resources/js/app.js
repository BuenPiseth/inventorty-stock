/**
 * DEER BAKERY & CAKE - Inventory Management System
 * Main Application Entry Point
 *
 * This file initializes the Vue.js/Inertia.js application with enhanced
 * features for inventory management, real-time updates, and professional
 * bakery operations.
 *
 * @version 2.0.0
 * @author DEER BAKERY & CAKE Development Team
 */

// Core Styles and Bootstrap
import '../css/app.css';
import './bootstrap';

// Vue.js and Inertia.js Core
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from 'ziggy-js';

// Inventory Management Utilities
import { InventoryManager } from './utils/inventory-manager';
import { NotificationSystem } from './utils/notification-system';
import { SecurityManager } from './utils/security-manager';
import { PerformanceMonitor } from './utils/performance-monitor';

// Global Configuration
const appName = import.meta.env.VITE_APP_NAME || 'DEER BAKERY & CAKE';
const isDevelopment = import.meta.env.DEV;
const isProduction = import.meta.env.PROD;

// Global Error Handler
window.addEventListener('error', (event) => {
    console.error('Global Error:', event.error);
    if (isProduction) {
        // Send error to logging service in production
        NotificationSystem.logError(event.error);
    }
});

// Unhandled Promise Rejection Handler
window.addEventListener('unhandledrejection', (event) => {
    console.error('Unhandled Promise Rejection:', event.reason);
    if (isProduction) {
        NotificationSystem.logError(event.reason);
    }
});

/**
 * Initialize DEER BAKERY & CAKE Inventory Management Application
 */
createInertiaApp({
    // Dynamic title with bakery branding
    title: (title) => title ? `${title} - ${appName}` : appName,

    // Enhanced component resolution with error handling
    resolve: async (name) => {
        try {
            return await resolvePageComponent(
                `./Pages/${name}.vue`,
                import.meta.glob('./Pages/**/*.vue'),
            );
        } catch (error) {
            console.error(`Failed to resolve component: ${name}`, error);
            // Fallback to error page
            return await resolvePageComponent(
                './Pages/Error.vue',
                import.meta.glob('./Pages/**/*.vue'),
            ).catch(() => {
                // Ultimate fallback
                return { default: { template: '<div>Application Error</div>' } };
            });
        }
    },

    // Enhanced app setup with inventory management features
    setup({ el, App, props, plugin }) {
        // Create Vue application instance
        const app = createApp({
            render: () => h(App, props),

            // Global error handler for Vue components
            errorCaptured(err, _instance, info) {
                console.error('Vue Error:', err, info);
                if (isProduction) {
                    NotificationSystem.logError(err, { component: info });
                }
                return false; // Prevent error from propagating
            }
        });

        // Install plugins
        app.use(plugin);
        app.use(ZiggyVue);

        // Global Properties for Inventory Management
        app.config.globalProperties.$inventory = InventoryManager;
        app.config.globalProperties.$notify = NotificationSystem;
        app.config.globalProperties.$security = SecurityManager;
        app.config.globalProperties.$performance = PerformanceMonitor;

        // Global Mixins for Common Functionality
        app.mixin({
            methods: {
                // Format currency for bakery pricing
                formatCurrency(amount) {
                    return new Intl.NumberFormat('en-US', {
                        style: 'currency',
                        currency: 'USD'
                    }).format(amount || 0);
                },

                // Format date for inventory operations
                formatDate(date, options = {}) {
                    const defaultOptions = {
                        year: 'numeric',
                        month: 'short',
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    };
                    return new Date(date).toLocaleDateString('en-US', { ...defaultOptions, ...options });
                },

                // Check user permissions for inventory operations
                can(permission) {
                    return SecurityManager.hasPermission(permission);
                },

                // Show success notification
                showSuccess(message) {
                    NotificationSystem.success(message);
                },

                // Show error notification
                showError(message) {
                    NotificationSystem.error(message);
                },

                // Show warning notification
                showWarning(message) {
                    NotificationSystem.warning(message);
                }
            }
        });

        // Development Tools
        if (isDevelopment) {
            app.config.devtools = true;
            app.config.debug = true;
            window.__VUE_APP__ = app;
        }

        // Mount application
        return app.mount(el);
    },

    // Enhanced progress bar for inventory operations
    progress: {
        color: '#667eea', // DEER BAKERY & CAKE primary color
        showSpinner: true,
        includeCSS: true,
        delay: 250
    },
})
.then(() => {
    // Post-initialization setup
    console.log(`🦌 DEER BAKERY & CAKE Inventory System initialized successfully`);

    // Initialize inventory management features
    InventoryManager.initialize();
    NotificationSystem.initialize();
    SecurityManager.initialize();
    PerformanceMonitor.start();

    // Setup real-time features if available
    if (window.Echo) {
        setupRealtimeFeatures();
    }
})
.catch((error) => {
    console.error('Failed to initialize DEER BAKERY & CAKE application:', error);

    // Show fallback error message
    document.body.innerHTML = `
        <div style="
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            text-align: center;
        ">
            <div>
                <h1>🦌 DEER BAKERY & CAKE</h1>
                <p>Inventory System Loading Error</p>
                <p style="font-size: 0.9rem; opacity: 0.8;">Please refresh the page or contact support</p>
            </div>
        </div>
    `;
});

/**
 * Setup real-time features for inventory management
 */
function setupRealtimeFeatures() {
    try {
        // Listen for stock level changes
        window.Echo.channel('inventory-updates')
            .listen('StockLevelChanged', (event) => {
                InventoryManager.handleStockUpdate(event);
                NotificationSystem.info(`Stock updated: ${event.product_name}`);
            });

        // Listen for low stock alerts
        window.Echo.channel('inventory-alerts')
            .listen('LowStockAlert', (event) => {
                NotificationSystem.warning(`Low stock alert: ${event.product_name} (${event.quantity} remaining)`);
            });

        console.log('Real-time inventory features enabled');
    } catch (error) {
        console.warn('Real-time features not available:', error);
    }
}
