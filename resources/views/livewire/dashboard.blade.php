<div>
    {{-- Balance Card --}}
    <x-balance-card :total="$totalBalance" :you-owe="$youOwe" :you-are-owed="$youAreOwed" />

    {{-- Quick Actions --}}
    <div class="section">
        <div class="grid--2col">
            <a href="{{ route('groups.index') }}" class="card clickable text-center"
                style="padding: 20px 12px; text-decoration: none;">
                <div style="font-size: 32px; margin-bottom: 8px;">➕</div>
                <div style="font-size: 14px; font-weight: 600;">Add Expense</div>
            </a>
            <a href="{{ route('groups.index') }}" class="card clickable text-center"
                style="padding: 20px 12px; text-decoration: none;">
                <div style="font-size: 32px; margin-bottom: 8px;">💳</div>
                <div style="font-size: 14px; font-weight: 600;">Settle Up</div>
            </a>
        </div>
    </div>

    {{-- Recent Activity --}}
    <div class="section">
        <div class="section__header">
            <h2 class="section__title">Recent Activity</h2>
            <a href="{{ route('groups.index') }}" class="section__action">See All</a>
        </div>
        <div>
            @forelse($recentExpenses as $expense)
                <x-expense-item :expense="$expense" />
            @empty
                <div class="empty-state">
                    <div class="empty-state__icon">💸</div>
                    <div class="empty-state__title">No expenses yet</div>
                    <div class="empty-state__subtitle"
                        style="font-size: 14px; color: var(--color-text-secondary); margin-top: 8px;">Add your first expense
                        to get started</div>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Groups --}}
    <div class="section">
        <div class="section__header">
            <h2 class="section__title">Your Groups</h2>
            <a href="{{ route('groups.index') }}" class="section__action">See All</a>
        </div>
        <div>
            @forelse($quickGroups as $group)
                <x-group-item :group="$group" :balance="$group->user_balance ?? 0" />
            @empty
                <div class="empty-state">
                    <div class="empty-state__icon">👥</div>
                    <div class="empty-state__title">No groups yet</div>
                    <div class="empty-state__subtitle"
                        style="font-size: 14px; color: var(--color-text-secondary); margin-top: 8px;">Create a group to
                        start splitting expenses</div>
                </div>
            @endforelse
        </div>
    </div>
</div>