/**
 * Optimized Theme Manager
 * High-performance theme switching with minimal overhead
 * Version: 2.0 - Production optimized
 */

class OptimizedThemeManager {
    constructor() {
        this.themes = ['light', 'dark', 'auto'];
        this.currentTheme = this.getStoredTheme() || 'auto';
        this.mediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
        this.isInitialized = false;
        
        // Performance: Use requestAnimationFrame for smooth initialization
        requestAnimationFrame(() => this.init());
    }

    init() {
        if (this.isInitialized) return;
        
        // Apply initial theme without transitions
        document.documentElement.classList.add('preload');
        this.applyTheme(this.currentTheme, false);
        
        // Set up event listeners with passive option for better performance
        this.setupEventListeners();
        
        // Listen for system theme changes
        this.mediaQuery.addEventListener('change', this.handleSystemThemeChange.bind(this), { passive: true });
        
        // Remove preload class after initialization
        setTimeout(() => {
            document.documentElement.classList.remove('preload');
            this.isInitialized = true;
        }, 100);
    }

    setupEventListeners() {
        // Use event delegation for better performance
        document.addEventListener('change', (e) => {
            if (e.target.matches('input[name="theme"]') && e.target.checked) {
                this.setTheme(e.target.value);
            }
        }, { passive: true });
    }

    getStoredTheme() {
        try {
            return localStorage.getItem('theme');
        } catch (e) {
            console.warn('Theme Manager: localStorage not available');
            return null;
        }
    }

    setStoredTheme(theme) {
        try {
            localStorage.setItem('theme', theme);
        } catch (e) {
            console.warn('Theme Manager: Could not save theme preference');
        }
    }

    setTheme(theme) {
        if (!this.themes.includes(theme)) {
            console.warn(`Theme Manager: Invalid theme "${theme}"`);
            return;
        }

        this.currentTheme = theme;
        this.setStoredTheme(theme);
        this.applyTheme(theme);
        this.updateToggleState();
        
        // Dispatch custom event for other components
        this.dispatchThemeEvent(theme);
    }

    applyTheme(theme, withTransition = true) {
        const html = document.documentElement;
        
        // Performance: Batch DOM operations
        const effectiveTheme = this.getEffectiveTheme(theme);
        
        // Remove existing theme classes
        html.classList.remove('theme-light', 'theme-dark');
        
        // Apply new theme class
        html.classList.add(`theme-${effectiveTheme}`);
        
        // Set data attribute for CSS targeting
        html.setAttribute('data-theme', effectiveTheme);
        
        // Update meta theme-color for mobile browsers
        this.updateMetaThemeColor(effectiveTheme);
    }

    getEffectiveTheme(theme = this.currentTheme) {
        if (theme === 'auto') {
            return this.mediaQuery.matches ? 'dark' : 'light';
        }
        return theme;
    }

    handleSystemThemeChange() {
        if (this.currentTheme === 'auto') {
            this.applyTheme('auto');
            this.dispatchThemeEvent('auto');
        }
    }

    updateMetaThemeColor(theme) {
        let metaThemeColor = document.querySelector('meta[name="theme-color"]');
        if (!metaThemeColor) {
            metaThemeColor = document.createElement('meta');
            metaThemeColor.name = 'theme-color';
            document.head.appendChild(metaThemeColor);
        }
        
        const colors = {
            light: '#ffffff',
            dark: '#111827'
        };
        
        metaThemeColor.content = colors[theme] || colors.light;
    }

    updateToggleState() {
        const themeButton = document.querySelector(`input[name="theme"][value="${this.currentTheme}"]`);
        if (themeButton && !themeButton.checked) {
            themeButton.checked = true;
        }
    }

    dispatchThemeEvent(theme) {
        const effectiveTheme = this.getEffectiveTheme(theme);
        window.dispatchEvent(new CustomEvent('themeChanged', {
            detail: { 
                theme: effectiveTheme, 
                userChoice: theme,
                timestamp: Date.now()
            }
        }));
    }

    // Public API methods
    getCurrentTheme() {
        return this.currentTheme;
    }

    getEffectiveThemePublic() {
        return this.getEffectiveTheme();
    }

    // Performance monitoring
    getPerformanceMetrics() {
        return {
            isInitialized: this.isInitialized,
            currentTheme: this.currentTheme,
            effectiveTheme: this.getEffectiveTheme(),
            supportsLocalStorage: this.supportsLocalStorage(),
            mediaQueryMatches: this.mediaQuery.matches
        };
    }

    supportsLocalStorage() {
        try {
            const test = '__theme_test__';
            localStorage.setItem(test, test);
            localStorage.removeItem(test);
            return true;
        } catch (e) {
            return false;
        }
    }
}

/**
 * Optimized UI Enhancement Functions
 * Lightweight utilities for better user experience
 */
class OptimizedUIEnhancements {
    constructor() {
        this.isInitialized = false;
        this.init();
    }

    init() {
        if (this.isInitialized) return;
        
        // Use passive event listeners for better performance
        document.addEventListener('DOMContentLoaded', () => {
            this.setupEnhancements();
            this.isInitialized = true;
        }, { passive: true });
    }

    setupEnhancements() {
        // Enhanced tooltips with performance optimization
        this.initTooltips();
        
        // Auto-hide alerts
        this.setupAutoHideAlerts();
        
        // Enhanced form validation
        this.setupFormValidation();
        
        // Smooth scrolling
        this.setupSmoothScrolling();
    }

    initTooltips() {
        // Only initialize tooltips if Bootstrap is available
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach(tooltipTriggerEl => {
                new bootstrap.Tooltip(tooltipTriggerEl, {
                    animation: true,
                    delay: { show: 500, hide: 100 }
                });
            });
        }
    }

    setupAutoHideAlerts() {
        const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
        alerts.forEach(alert => {
            setTimeout(() => {
                if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }
            }, 5000);
        });
    }

    setupFormValidation() {
        const forms = document.querySelectorAll('form[novalidate]');
        forms.forEach(form => {
            form.addEventListener('submit', (event) => {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                    
                    // Focus on first invalid field
                    const firstInvalid = form.querySelector(':invalid');
                    if (firstInvalid) {
                        firstInvalid.focus();
                        firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }
                form.classList.add('was-validated');
            }, { passive: false });
        });
    }

    setupSmoothScrolling() {
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href^="#"]');
            if (link) {
                const target = document.querySelector(link.getAttribute('href'));
                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({ behavior: 'smooth' });
                }
            }
        }, { passive: false });
    }
}

// Initialize optimized systems
let themeManager, uiEnhancements;

// Performance: Initialize only when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeOptimizedSystems);
} else {
    initializeOptimizedSystems();
}

function initializeOptimizedSystems() {
    try {
        themeManager = new OptimizedThemeManager();
        uiEnhancements = new OptimizedUIEnhancements();
        
        // Make theme manager globally available
        window.themeManager = themeManager;
        
        // Performance monitoring in development
        if (window.location.hostname === 'localhost' || window.location.hostname === '127.0.0.1') {
            console.log('Theme Manager Performance Metrics:', themeManager.getPerformanceMetrics());
        }
    } catch (error) {
        console.error('Failed to initialize optimized systems:', error);
    }
}

// Export for module systems
if (typeof module !== 'undefined' && module.exports) {
    module.exports = { OptimizedThemeManager, OptimizedUIEnhancements };
}
