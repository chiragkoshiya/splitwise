@props(['active' => 'dashboard'])

<nav class="bottom-nav">
    <a href="{{ route('dashboard') }}" class="bottom-nav__item {{ $active === 'dashboard' ? 'active' : '' }}">
        <span class="bottom-nav__icon">🏠</span>
        <span class="bottom-nav__label">Home</span>
    </a>
    <a href="{{ route('groups.index') }}" class="bottom-nav__item {{ $active === 'expenses' ? 'active' : '' }}">
        <span class="bottom-nav__icon">💸</span>
        <span class="bottom-nav__label">Expenses</span>
    </a>
    <a href="{{ route('groups.index') }}" class="bottom-nav__item {{ $active === 'groups' ? 'active' : '' }}">
        <span class="bottom-nav__icon">👥</span>
        <span class="bottom-nav__label">Groups</span>
    </a>
    <a href="/profile" class="bottom-nav__item {{ $active === 'profile' ? 'active' : '' }}">
        <span class="bottom-nav__icon">👤</span>
        <span class="bottom-nav__label">Profile</span>
    </a>
</nav>