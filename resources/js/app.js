import "./bootstrap";

// Livewire is already handled via CDN or npm, no need to import manually if using @livewireScripts

// Utility functions adapted from theme
window.App = {
    formatCurrency(amount, signed = false) {
        const formatted = new Intl.NumberFormat("en-US", {
            style: "currency",
            currency: "USD",
        }).format(Math.abs(amount));

        if (signed && amount !== 0) {
            return amount > 0 ? `+${formatted}` : `-${formatted}`;
        }
        return formatted;
    },

    formatDate(date) {
        return new Intl.DateTimeFormat("en-US", {
            month: "short",
            day: "numeric",
        }).format(new Date(date));
    },

    showToast(message, type = "info") {
        // Toast notifications can be handled by Livewire toast packages
        console.log(`[${type.toUpperCase()}] ${message}`);
    },
};
// Connectivity Watcher
window.Connectivity = {
    isOnline: navigator.onLine,
    
    init() {
        window.addEventListener('online', () => this.updateStatus(true));
        window.addEventListener('offline', () => this.updateStatus(false));
        
        // Initial check
        this.updateStatus(navigator.onLine);
    },

    updateStatus(status) {
        this.isOnline = status;
        document.body.classList.toggle('is-offline', !status);
        
        if (status) {
            this.onOnline();
        } else {
            this.onOffline();
        }

        // Dispatch global event for Livewire components
        window.dispatchEvent(new CustomEvent('connectivity-changed', { 
            detail: { online: status } 
        }));
    },

    onOnline() {
        console.log("Connection restored.");
        window.App.showToast("Connection restored. Syncing...", "success");
        
        // Refresh Livewire components
        if (window.Livewire) {
            window.Livewire.dispatch('refresh-all');
        }
    },

    onOffline() {
        console.log("Connection lost.");
        window.App.showToast("You are currently offline.", "warning");
    }
};

window.Connectivity.init();
