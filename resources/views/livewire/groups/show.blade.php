<div>
    {{-- Group Header --}}
    <div class="card">
        <div style="display: flex; align-items: center; gap: 12px; margin-bottom: 16px;">
            <div class="list-item__icon" style="font-size: 24px;">{{ $group->icon ?? '👥' }}</div>
            <div style="flex: 1;">
                <h1 style="font-size: 20px; font-weight: 700; margin-bottom: 4px;">{{ $group->name }}</h1>
                <div style="font-size: 14px; color: var(--color-text-secondary);">{{ $group->users->count() }} members
                </div>
            </div>
        </div>
    </div>

    {{-- Group Balances --}}
    <div class="section">
        <div class="section__header">
            <h2 class="section__title">Balances</h2>
            <a href="{{ route('groups.show', ['group' => $group->id]) }}/balances" class="section__action">View All</a>
        </div>
        <div class="card">
            <p style="color: var(--color-text-secondary); font-size: 14px;">Balance details will appear here</p>
        </div>
    </div>

    {{-- Group Expenses --}}
    <div class="section">
        <div class="section__header">
            <h2 class="section__title">Expenses</h2>
            <a href="{{ route('groups.show', ['group' => $group->id]) }}/expenses/create"
                class="section__action">Add</a>
        </div>
        <div>
            @forelse($expenses as $expense)
                <x-expense-item :expense="$expense" />
            @empty
                <div class="empty-state">
                    <div class="empty-state__icon">💸</div>
                    <div class="empty-state__title">No expenses yet</div>
                    <div class="empty-state__subtitle"
                        style="font-size: 14px; color: var(--color-text-secondary); margin-top: 8px;">Add the first expense
                        for this group</div>
                </div>
            @endforelse
        </div>
    </div>
</div>