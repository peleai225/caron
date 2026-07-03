/**
 * Toast Notification System
 * Minimal, auto-dismissing notifications with slide-in animation
 */
const Toast = {
    container: null,

    init() {
        this.container = document.getElementById('toast-container');
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.id = 'toast-container';
            this.container.className = 'fixed top-4 right-4 z-[60] flex flex-col gap-2';
            document.body.appendChild(this.container);
        }
    },

    show(message, type = 'success') {
        if (!this.container) this.init();

        const config = {
            success: {
                border: 'border-l-emerald-500',
                bg: 'bg-white',
                icon: '<svg class="w-4 h-4 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                timeout: 3000,
            },
            error: {
                border: 'border-l-red-500',
                bg: 'bg-white',
                icon: '<svg class="w-4 h-4 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                timeout: 5000,
            },
            warning: {
                border: 'border-l-amber-500',
                bg: 'bg-white',
                icon: '<svg class="w-4 h-4 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>',
                timeout: 4000,
            },
            info: {
                border: 'border-l-primary-500',
                bg: 'bg-white',
                icon: '<svg class="w-4 h-4 text-primary-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>',
                timeout: 3000,
            },
        };

        const cfg = config[type] || config.success;

        const toast = document.createElement('div');
        toast.className = `max-w-xs w-80 ${cfg.bg} rounded-lg shadow-md border border-slate-200 border-l-4 ${cfg.border} flex items-center gap-2 px-4 py-3 transform translate-x-full opacity-0 transition-all duration-300 ease-out`;

        toast.innerHTML = `
            ${cfg.icon}
            <p class="flex-1 text-xs text-slate-700 leading-snug">${this.escapeHtml(message)}</p>
            <button class="toast-close p-0.5 rounded text-slate-400 hover:text-slate-600 transition-colors flex-shrink-0">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        `;

        this.container.appendChild(toast);

        // Slide in
        requestAnimationFrame(() => {
            toast.classList.remove('translate-x-full', 'opacity-0');
            toast.classList.add('translate-x-0', 'opacity-100');
        });

        // Close button
        toast.querySelector('.toast-close').addEventListener('click', () => {
            this.dismiss(toast);
        });

        // Auto dismiss
        let timer = setTimeout(() => this.dismiss(toast), cfg.timeout);

        // Pause on hover
        toast.addEventListener('mouseenter', () => clearTimeout(timer));
        toast.addEventListener('mouseleave', () => {
            clearTimeout(timer);
            timer = setTimeout(() => this.dismiss(toast), 1500);
        });
    },

    dismiss(toast) {
        if (!toast || toast.dataset.dismissing) return;
        toast.dataset.dismissing = 'true';
        toast.classList.add('translate-x-full', 'opacity-0');
        setTimeout(() => {
            toast.remove();
        }, 300);
    },

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    },

    success(message) {
        this.show(message, 'success');
    },

    error(message) {
        this.show(message, 'error');
    },

    warning(message) {
        this.show(message, 'warning');
    },

    info(message) {
        this.show(message, 'info');
    },
};

// Make globally available
window.Toast = Toast;

// Initialize on DOM ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => Toast.init());
} else {
    Toast.init();
}

export default Toast;
