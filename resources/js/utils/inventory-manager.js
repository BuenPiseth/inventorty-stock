/**
 * DEER BAKERY & CAKE - Inventory Manager
 * 
 * Centralized inventory management utilities for stock operations,
 * product management, and bakery-specific inventory features.
 * 
 * @version 1.0.0
 */

export class InventoryManager {
    static instance = null;
    static stockCache = new Map();
    static observers = new Set();

    /**
     * Initialize the inventory manager
     */
    static initialize() {
        if (this.instance) return this.instance;
        
        this.instance = new InventoryManager();
        this.setupEventListeners();
        this.loadInitialData();
        
        console.log('🦌 Inventory Manager initialized');
        return this.instance;
    }

    /**
     * Setup event listeners for inventory operations
     */
    static setupEventListeners() {
        // Listen for stock form submissions
        document.addEventListener('submit', (event) => {
            if (event.target.matches('[data-stock-form]')) {
                this.handleStockFormSubmission(event);
            }
        });

        // Listen for product updates
        document.addEventListener('product-updated', (event) => {
            this.handleProductUpdate(event.detail);
        });

        // Listen for stock movements
        document.addEventListener('stock-movement', (event) => {
            this.handleStockMovement(event.detail);
        });
    }

    /**
     * Load initial inventory data
     */
    static async loadInitialData() {
        try {
            // Load critical stock levels
            const response = await window.axios.get('/api/inventory/critical-levels');
            this.updateStockCache(response.data);
        } catch (error) {
            console.warn('Failed to load initial inventory data:', error);
        }
    }

    /**
     * Handle stock form submission with validation
     */
    static handleStockFormSubmission(event) {
        const form = event.target;
        const formData = new FormData(form);
        const productId = formData.get('product_id');
        const quantity = parseInt(formData.get('quantity'));
        const type = formData.get('type');

        // Validate stock operation
        if (!this.validateStockOperation(productId, quantity, type)) {
            event.preventDefault();
            return false;
        }

        // Add loading state
        this.setFormLoading(form, true);

        // Handle form completion
        form.addEventListener('ajax:complete', () => {
            this.setFormLoading(form, false);
        }, { once: true });
    }

    /**
     * Validate stock operation before submission
     */
    static validateStockOperation(productId, quantity, type) {
        if (!productId || !quantity || quantity <= 0) {
            this.showValidationError('Please enter a valid quantity');
            return false;
        }

        if (type === 'out') {
            const currentStock = this.getCurrentStock(productId);
            if (currentStock < quantity) {
                this.showValidationError(`Insufficient stock. Available: ${currentStock}`);
                return false;
            }
        }

        return true;
    }

    /**
     * Get current stock level for a product
     */
    static getCurrentStock(productId) {
        const cached = this.stockCache.get(productId);
        if (cached) return cached.quantity;

        // Try to get from DOM
        const stockElement = document.querySelector(`[data-product-stock="${productId}"]`);
        return stockElement ? parseInt(stockElement.textContent) : 0;
    }

    /**
     * Update stock cache
     */
    static updateStockCache(products) {
        if (Array.isArray(products)) {
            products.forEach(product => {
                this.stockCache.set(product.id, {
                    quantity: product.quantity,
                    min_stock: product.min_stock,
                    updated_at: new Date()
                });
            });
        }
    }

    /**
     * Handle real-time stock updates
     */
    static handleStockUpdate(event) {
        const { product_id, new_quantity, old_quantity } = event;
        
        // Update cache
        const cached = this.stockCache.get(product_id);
        if (cached) {
            cached.quantity = new_quantity;
            cached.updated_at = new Date();
        }

        // Update DOM elements
        this.updateStockDisplays(product_id, new_quantity);

        // Notify observers
        this.notifyObservers('stock-updated', {
            product_id,
            new_quantity,
            old_quantity
        });

        // Check for low stock
        this.checkLowStock(product_id, new_quantity);
    }

    /**
     * Update stock displays in the DOM
     */
    static updateStockDisplays(productId, quantity) {
        const elements = document.querySelectorAll(`[data-product-stock="${productId}"]`);
        elements.forEach(element => {
            element.textContent = quantity;
            
            // Add visual feedback
            element.classList.add('stock-updated');
            setTimeout(() => {
                element.classList.remove('stock-updated');
            }, 2000);
        });
    }

    /**
     * Check for low stock conditions
     */
    static checkLowStock(productId, quantity) {
        const cached = this.stockCache.get(productId);
        const minStock = cached?.min_stock || 5;

        if (quantity <= minStock) {
            this.triggerLowStockAlert(productId, quantity, minStock);
        }
    }

    /**
     * Trigger low stock alert
     */
    static triggerLowStockAlert(productId, quantity, minStock) {
        const event = new CustomEvent('low-stock-alert', {
            detail: { productId, quantity, minStock }
        });
        document.dispatchEvent(event);
    }

    /**
     * Set form loading state
     */
    static setFormLoading(form, loading) {
        const submitBtn = form.querySelector('[type="submit"]');
        const spinner = form.querySelector('.loading-spinner');

        if (loading) {
            submitBtn?.setAttribute('disabled', 'disabled');
            spinner?.classList.remove('d-none');
        } else {
            submitBtn?.removeAttribute('disabled');
            spinner?.classList.add('d-none');
        }
    }

    /**
     * Show validation error
     */
    static showValidationError(message) {
        // Create or update error alert
        let alert = document.querySelector('.inventory-validation-error');
        if (!alert) {
            alert = document.createElement('div');
            alert.className = 'alert alert-danger alert-dismissible fade show inventory-validation-error';
            alert.innerHTML = `
                <i class="bi bi-exclamation-triangle me-2"></i>
                <span class="error-message"></span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.main-content')?.prepend(alert);
        }

        alert.querySelector('.error-message').textContent = message;
        alert.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    }

    /**
     * Add observer for inventory events
     */
    static addObserver(callback) {
        this.observers.add(callback);
    }

    /**
     * Remove observer
     */
    static removeObserver(callback) {
        this.observers.delete(callback);
    }

    /**
     * Notify all observers
     */
    static notifyObservers(event, data) {
        this.observers.forEach(callback => {
            try {
                callback(event, data);
            } catch (error) {
                console.error('Observer error:', error);
            }
        });
    }

    /**
     * Handle product update
     */
    static handleProductUpdate(product) {
        this.updateStockCache([product]);
        this.updateStockDisplays(product.id, product.quantity);
    }

    /**
     * Handle stock movement
     */
    static handleStockMovement(movement) {
        const { product_id, type, quantity, new_stock } = movement;
        
        this.handleStockUpdate({
            product_id,
            new_quantity: new_stock,
            old_quantity: type === 'in' ? new_stock - quantity : new_stock + quantity
        });
    }

    /**
     * Get inventory statistics
     */
    static getInventoryStats() {
        const stats = {
            total_products: this.stockCache.size,
            low_stock_count: 0,
            out_of_stock_count: 0,
            total_value: 0
        };

        this.stockCache.forEach(item => {
            if (item.quantity <= 0) {
                stats.out_of_stock_count++;
            } else if (item.quantity <= (item.min_stock || 5)) {
                stats.low_stock_count++;
            }
        });

        return stats;
    }

    /**
     * Export inventory data
     */
    static async exportInventory(format = 'csv', options = {}) {
        try {
            const response = await window.axios.post('/export/products', {
                format,
                ...options
            }, {
                responseType: 'blob'
            });

            // Create download link
            const url = window.URL.createObjectURL(new Blob([response.data]));
            const link = document.createElement('a');
            link.href = url;
            link.setAttribute('download', `inventory_export_${new Date().toISOString().split('T')[0]}.${format}`);
            document.body.appendChild(link);
            link.click();
            link.remove();
            window.URL.revokeObjectURL(url);

            return true;
        } catch (error) {
            console.error('Export failed:', error);
            return false;
        }
    }
}
