/**
 * DEER BAKERY & CAKE - Notification System
 * 
 * Professional notification system for inventory operations,
 * user feedback, and system alerts with beautiful UI.
 * 
 * @version 1.0.0
 */

export class NotificationSystem {
    static container = null;
    static notifications = new Map();
    static config = {
        position: 'top-right',
        duration: 5000,
        maxNotifications: 5,
        enableSound: false
    };

    /**
     * Initialize the notification system
     */
    static initialize() {
        this.createContainer();
        this.setupStyles();
        console.log('🔔 Notification System initialized');
    }

    /**
     * Create notification container
     */
    static createContainer() {
        if (this.container) return;

        this.container = document.createElement('div');
        this.container.id = 'deer-notifications';
        this.container.className = `notification-container position-fixed ${this.config.position}`;
        this.container.style.cssText = `
            top: 20px;
            right: 20px;
            z-index: 9999;
            max-width: 400px;
            pointer-events: none;
        `;
        document.body.appendChild(this.container);
    }

    /**
     * Setup notification styles
     */
    static setupStyles() {
        if (document.getElementById('deer-notification-styles')) return;

        const styles = document.createElement('style');
        styles.id = 'deer-notification-styles';
        styles.textContent = `
            .deer-notification {
                background: white;
                border-radius: 12px;
                box-shadow: 0 10px 30px rgba(0,0,0,0.15);
                margin-bottom: 12px;
                padding: 16px 20px;
                pointer-events: auto;
                transform: translateX(100%);
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                border-left: 4px solid #667eea;
                position: relative;
                overflow: hidden;
            }

            .deer-notification.show {
                transform: translateX(0);
            }

            .deer-notification.hide {
                transform: translateX(100%);
                opacity: 0;
            }

            .deer-notification::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                height: 2px;
                background: linear-gradient(90deg, #667eea, #764ba2);
                transform: scaleX(0);
                transform-origin: left;
                animation: progress var(--duration, 5000ms) linear forwards;
            }

            @keyframes progress {
                to { transform: scaleX(1); }
            }

            .deer-notification.success {
                border-left-color: #28a745;
            }

            .deer-notification.error {
                border-left-color: #dc3545;
            }

            .deer-notification.warning {
                border-left-color: #ffc107;
            }

            .deer-notification.info {
                border-left-color: #17a2b8;
            }

            .notification-header {
                display: flex;
                align-items: center;
                margin-bottom: 8px;
                font-weight: 600;
                font-size: 0.95rem;
            }

            .notification-icon {
                margin-right: 8px;
                font-size: 1.1rem;
            }

            .notification-body {
                color: #6c757d;
                font-size: 0.9rem;
                line-height: 1.4;
            }

            .notification-close {
                position: absolute;
                top: 8px;
                right: 8px;
                background: none;
                border: none;
                color: #adb5bd;
                cursor: pointer;
                font-size: 1.2rem;
                padding: 4px;
                border-radius: 4px;
                transition: all 0.2s ease;
            }

            .notification-close:hover {
                color: #6c757d;
                background: #f8f9fa;
            }

            .notification-actions {
                margin-top: 12px;
                display: flex;
                gap: 8px;
            }

            .notification-btn {
                padding: 6px 12px;
                border: none;
                border-radius: 6px;
                font-size: 0.8rem;
                cursor: pointer;
                transition: all 0.2s ease;
            }

            .notification-btn.primary {
                background: #667eea;
                color: white;
            }

            .notification-btn.secondary {
                background: #e9ecef;
                color: #495057;
            }

            .notification-btn:hover {
                transform: translateY(-1px);
                box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            }

            @media (max-width: 768px) {
                .notification-container {
                    left: 10px !important;
                    right: 10px !important;
                    top: 10px !important;
                    max-width: none !important;
                }
            }
        `;
        document.head.appendChild(styles);
    }

    /**
     * Show success notification
     */
    static success(message, options = {}) {
        return this.show('success', '✅', 'Success', message, {
            duration: 4000,
            ...options
        });
    }

    /**
     * Show error notification
     */
    static error(message, options = {}) {
        return this.show('error', '❌', 'Error', message, {
            duration: 8000,
            ...options
        });
    }

    /**
     * Show warning notification
     */
    static warning(message, options = {}) {
        return this.show('warning', '⚠️', 'Warning', message, {
            duration: 6000,
            ...options
        });
    }

    /**
     * Show info notification
     */
    static info(message, options = {}) {
        return this.show('info', 'ℹ️', 'Info', message, {
            duration: 5000,
            ...options
        });
    }

    /**
     * Show custom notification
     */
    static show(type, icon, title, message, options = {}) {
        const id = this.generateId();
        const config = { ...this.config, ...options };
        
        // Limit number of notifications
        if (this.notifications.size >= this.config.maxNotifications) {
            const oldestId = this.notifications.keys().next().value;
            this.hide(oldestId);
        }

        const notification = this.createNotification(id, type, icon, title, message, config);
        this.container.appendChild(notification);
        this.notifications.set(id, notification);

        // Show with animation
        requestAnimationFrame(() => {
            notification.classList.add('show');
        });

        // Auto-hide
        if (config.duration > 0) {
            setTimeout(() => this.hide(id), config.duration);
        }

        return id;
    }

    /**
     * Create notification element
     */
    static createNotification(id, type, icon, title, message, config) {
        const notification = document.createElement('div');
        notification.className = `deer-notification ${type}`;
        notification.dataset.id = id;
        notification.style.setProperty('--duration', `${config.duration}ms`);

        notification.innerHTML = `
            <button class="notification-close" onclick="NotificationSystem.hide('${id}')">&times;</button>
            <div class="notification-header">
                <span class="notification-icon">${icon}</span>
                <span>${title}</span>
            </div>
            <div class="notification-body">${message}</div>
            ${config.actions ? this.createActions(config.actions, id) : ''}
        `;

        // Add click handler for dismissal
        notification.addEventListener('click', (e) => {
            if (!e.target.closest('.notification-actions') && !e.target.closest('.notification-close')) {
                this.hide(id);
            }
        });

        return notification;
    }

    /**
     * Create action buttons
     */
    static createActions(actions, notificationId) {
        const actionsHtml = actions.map(action => `
            <button class="notification-btn ${action.type || 'secondary'}" 
                    onclick="NotificationSystem.handleAction('${notificationId}', '${action.action}')">
                ${action.label}
            </button>
        `).join('');

        return `<div class="notification-actions">${actionsHtml}</div>`;
    }

    /**
     * Handle action button clicks
     */
    static handleAction(notificationId, action) {
        const notification = this.notifications.get(notificationId);
        if (!notification) return;

        // Dispatch custom event
        const event = new CustomEvent('notification-action', {
            detail: { notificationId, action }
        });
        document.dispatchEvent(event);

        // Hide notification
        this.hide(notificationId);
    }

    /**
     * Hide notification
     */
    static hide(id) {
        const notification = this.notifications.get(id);
        if (!notification) return;

        notification.classList.add('hide');
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.parentNode.removeChild(notification);
            }
            this.notifications.delete(id);
        }, 400);
    }

    /**
     * Clear all notifications
     */
    static clear() {
        this.notifications.forEach((notification, id) => {
            this.hide(id);
        });
    }

    /**
     * Generate unique ID
     */
    static generateId() {
        return `notification_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`;
    }

    /**
     * Log error (for production)
     */
    static logError(error, context = {}) {
        // In production, send to logging service
        console.error('Application Error:', error, context);
        
        // Could integrate with services like Sentry, LogRocket, etc.
        if (window.Sentry) {
            window.Sentry.captureException(error, { extra: context });
        }
    }

    /**
     * Show inventory-specific notifications
     */
    static stockUpdated(productName, oldQuantity, newQuantity) {
        const change = newQuantity - oldQuantity;
        const changeText = change > 0 ? `+${change}` : change.toString();
        
        this.success(`Stock Updated: ${productName}`, {
            message: `Quantity changed by ${changeText} (now ${newQuantity})`,
            duration: 3000
        });
    }

    static lowStockAlert(productName, quantity, minStock) {
        this.warning(`Low Stock Alert: ${productName}`, {
            message: `Only ${quantity} remaining (minimum: ${minStock})`,
            actions: [
                { label: 'Add Stock', action: 'add-stock', type: 'primary' },
                { label: 'Dismiss', action: 'dismiss' }
            ]
        });
    }

    static exportComplete(filename, recordCount) {
        this.success('Export Complete', {
            message: `${recordCount} records exported to ${filename}`,
            duration: 4000
        });
    }
}

// Make available globally for onclick handlers
window.NotificationSystem = NotificationSystem;
