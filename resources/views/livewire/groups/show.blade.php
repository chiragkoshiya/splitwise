<div>
    <x-slot:appBarActions>
        <a href="{{ route('groups.members', $group) }}" class="btn-icon">⚙️</a>
        </x-slot>

        {{-- Group Header --}}
        <div class="card" style="margin-bottom: 24px; padding: 20px;">
            <div style="display: flex; align-items: center; gap: 16px;">
                <div class="list-item__icon"
                    style="font-size: 32px; width: 64px; height: 64px; background: var(--color-bg);">👥</div>
                <div style="flex: 1;">
                    <h1 style="font-size: 24px; font-weight: 700; margin-bottom: 4px;">{{ $group->name }}</h1>
                    <div style="font-size: 14px; color: var(--color-text-secondary);">{{ $group->users->count() }}
                        members</div>
                </div>
            </div>
        </div>

        {{-- Group Balances Summary --}}
        <div class="section">
            <div class="section__header">
                <h2 class="section__title">Balances</h2>
                <a href="{{ route('balances.index', $group) }}" class="section__action">Details</a>
            </div>
            <div class="card">
                @forelse($balances as $balance)
                    @php
                        $u1 = $balance->from_user_id;
                        $u2 = $balance->to_user_id;
                        $currentUserId = auth()->id();
                        $otherUser = ($u1 === $currentUserId) ? $balance->toUser : $balance->fromUser;
                        $amount = ($u1 === $currentUserId) ? $balance->amount : -$balance->amount;
                    @endphp
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--color-border); last-child: border-bottom: none;">
                        <div>
                            <div style="font-size: 14px; font-weight: 600;">{{ $otherUser->name }}</div>
                            <div style="font-size: 12px; color: var(--color-text-secondary);">
                                {{ $amount > 0 ? 'You owe them' : ($amount < 0 ? 'They owe you' : 'Settled up') }}
                            </div>
                        </div>
                        <div
                            class="money {{ $amount > 0 ? 'money--owe' : ($amount < 0 ? 'money--owed' : 'money--settled') }}">
                            {{ $amount > 0 ? '' : ($amount < 0 ? '+' : '') }}{{ number_format(abs($amount), 2) }}
                        </div>
                    </div>
                @empty
                    <div class="text-center" style="padding: 12px 0; color: var(--color-text-secondary); font-size: 14px;">
                        Everyone is settled up! 🙌
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Group Expenses --}}
        <div class="section">
            <div class="section__header">
                <h2 class="section__title">Recent Activity</h2>
                <a href="{{ route('settlements.create', $group) }}" class="btn btn--outline btn--small">Settle Up</a>
            </div>
            <div>
                @forelse($expenses as $expense)
                    <x-expense-item :expense="$expense" />
                @empty
                    <div class="empty-state">
                        <div class="empty-state__icon">💸</div>
                        <div class="empty-state__title">No expenses yet</div>
                        <div class="empty-state__subtitle"
                            style="font-size: 14px; color: var(--color-text-secondary); margin-top: 8px;">
                            Tap the + button to add the first expense
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <x-slot:fab>
            <x-fab href="{{ route('expenses.create', $group) }}" />
            </x-slot>
</div>