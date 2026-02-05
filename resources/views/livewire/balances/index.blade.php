<div>
    <div class="section">
        <div class="section__header">
            <h2 class="section__title">Debt Summary</h2>
        </div>
        <div class="card">
            @forelse($balances as $balance)
                @php
                    $amount = (float) $balance->amount;
                @endphp
                @if(abs($amount) > 0.01)
                    <div
                        style="display: flex; justify-content: space-between; align-items: center; padding: 16px 0; border-bottom: 1px solid var(--color-border); last-child: border-bottom: none;">
                        <div style="display: flex; align-items: center; gap: 12px;">
                            <div class="list-item__icon" style="width: 40px; height: 40px; background: var(--color-bg);">👤
                            </div>
                            <div style="font-size: 14px;">
                                <span style="font-weight: 600;">{{ $balance->fromUser->name }}</span>
                                <span style="color: var(--color-text-secondary); margin: 0 4px;">owes</span>
                                <span style="font-weight: 600;">{{ $balance->toUser->name }}</span>
                            </div>
                        </div>
                        <div class="money money--owe" style="font-weight: 700;">
                            ${{ number_format(abs($amount), 2) }}
                        </div>
                    </div>
                @endif
            @empty
                <div class="empty-state">
                    <div class="empty-state__icon">🎉</div>
                    <div class="empty-state__title">Everything settled!</div>
                    <div class="empty-state__subtitle"
                        style="font-size: 14px; color: var(--color-text-secondary); margin-top: 8px;">
                        No one owes anyone anything in this group.
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div style="margin-top: 32px;">
        <a href="{{ route('settlements.create', $group) }}" class="btn btn--primary btn--large btn--full">
            Settle a Debt
        </a>
    </div>
</div>