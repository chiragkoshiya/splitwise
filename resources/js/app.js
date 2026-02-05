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
