# Quick Reference: Theme UI Components

## 🎨 Available CSS Classes

### Layout Classes

```html
<div class="page">
    <!-- Basic page wrapper -->
    <div class="page page-with-appbar">
        <!-- Page with top app bar -->
        <div class="page-content"><!-- Main content area --></div>
    </div>
</div>
```

### Navigation

```html
<!-- App Bar (Top) -->
<div class="app-bar">
    <div class="app-bar__left">...</div>
    <div class="app-bar__title">Title</div>
    <div class="app-bar__right">...</div>
</div>

<!-- Bottom Nav -->
<nav class="bottom-nav">
    <a class="bottom-nav__item active">
        <span class="bottom-nav__icon">🏠</span>
        <span class="bottom-nav__label">Home</span>
    </a>
</nav>

<!-- FAB -->
<a href="#" class="fab">+</a>
```

### Cards & Lists

```html
<!-- Card -->
<div class="card">Content</div>
<div class="card clickable">Clickable card</div>

<!-- List -->
<div class="list">
    <div class="list-item">
        <div class="list-item__icon">💸</div>
        <div class="list-item__content">
            <div class="list-item__title">Title</div>
            <div class="list-item__subtitle">Subtitle</div>
        </div>
    </div>
</div>
```

### Forms

```html
<div class="form-group">
    <label class="form-label">Label</label>
    <input type="text" class="input" placeholder="Placeholder" />
    <div class="form-error">Error message</div>
</div>

<!-- Buttons -->
<button class="btn btn--primary">Button</button>
<button class="btn btn--outline">Outline</button>
<button class="btn btn--large">Large</button>
<button class="btn btn--full">Full Width</button>
<button class="btn-icon">Icon</button>
```

### Balance Card

```html
<div class="balance-card">
    <div class="balance-card__label">Total Balance</div>
    <div class="balance-card__amount">$100.00</div>
    <div class="balance-card__breakdown">
        <div class="balance-card__item">
            <div class="balance-card__item-label">You Owe</div>
            <div class="balance-card__item-value">$50.00</div>
        </div>
    </div>
</div>
```

### Money Display

```html
<div class="money money--owe">-$50.00</div>
<!-- Red -->
<div class="money money--owed">+$50.00</div>
<!-- Green -->
<div class="money money--settled">$0.00</div>
<!-- Gray -->
```

### Sections

```html
<div class="section">
    <div class="section__header">
        <h2 class="section__title">Section Title</h2>
        <a href="#" class="section__action">Action</a>
    </div>
    <!-- Content -->
</div>
```

### Empty States

```html
<div class="empty-state">
    <div class="empty-state__icon">💸</div>
    <div class="empty-state__title">No Data</div>
    <div class="empty-state__subtitle">Description</div>
</div>
```

### Grid

```html
<div class="grid--2col">
    <div>Item 1</div>
    <div>Item 2</div>
</div>
```

### Skeleton Loader

```html
<div class="skeleton"></div>
```

---

## 🎯 Blade Components Usage

### In Blade Views:

```blade
<!-- Layout -->
<x-app-bar title="Page Title" :back="route('dashboard')">
    <button class="btn-icon">⚙️</button>
</x-app-bar>

<x-bottom-nav active="dashboard" />

<x-fab href="{{ route('expenses.create') }}" />

<!-- Components -->
<x-balance-card :total="100" :you-owe="50" :you-are-owed="150" />

<x-expense-item :expense="$expense" />

<x-group-item :group="$group" :balance="25.50" />
```

---

## 🎨 CSS Variables (Customization)

Located in `resources/css/theme.css`:

```css
:root {
    /* Colors */
    --color-primary: #6366f1; /* Indigo */
    --color-secondary: #14b8a6; /* Teal */
    --color-success: #10b981; /* Green */
    --color-error: #ef4444; /* Red */

    /* Backgrounds */
    --color-bg: #f5f5f5;
    --color-surface: #ffffff;
    --color-border: #e0e0e0;

    /* Text */
    --color-text: #1a1a1a;
    --color-text-secondary: #666666;
    --color-text-tertiary: #999999;

    /* Spacing */
    --space-xs: 4px;
    --space-sm: 8px;
    --space-md: 12px;
    --space-lg: 16px;
    --space-xl: 24px;

    /* Border Radius */
    --radius-sm: 6px;
    --radius-md: 8px;
    --radius-lg: 12px;
    --radius-full: 9999px;
}
```

To customize, edit these values in `theme.css`.

---

## 📱 Mobile-First Responsive Design

All components are optimized for:

- iPhone/Android phones (320px+)
- Tablets (768px+)
- Desktop (1024px+)

Max content width: 480px (centered)

Bottom nav height: 64px
App bar height: 48px

---

## 🔧 JavaScript Utilities

Available globally via `window.App`:

```js
// Format currency
App.formatCurrency(150.5); // "$150.50"
App.formatCurrency(150.5, true); // "+$150.50"

// Format date
App.formatDate("2024-02-05"); // "Feb 5"
```

---

## 💡 Common Patterns

### Page with App Bar & Bottom Nav

```blade
<x-app-layout>
    <x-slot:title>Page Title</x-slot>

    <div class="section">
        <!-- Content -->
    </div>
</x-app-layout>
```

### Form Page

```blade
<div class="card">
    <form wire:submit="save">
        <div class="form-group">
            <label class="form-label">Field</label>
            <input wire:model="field" class="input">
            @error('field')
                <div class="form-error">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn--primary btn--full">
            Save
        </button>
    </form>
</div>
```

### List Page

```blade
<div>
    @forelse($items as $item)
        <x-item-component :item="$item" />
    @empty
        <div class="empty-state">
            <div class="empty-state__icon">📭</div>
            <div class="empty-state__title">No items</div>
        </div>
    @endforelse
</div>
```

---

**END OF QUICK REFERENCE**
