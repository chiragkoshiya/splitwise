@props(['total' => 0, 'youOwe' => 0, 'youAreOwed' => 0])

<div class="balance-card">
    <div class="balance-card__label">Total Balance</div>
    <div class="balance-card__amount">
        {{ $total >= 0 ? '+' : '' }}${{ number_format(abs($total), 2) }}
    </div>

    <div class="balance-card__breakdown">
        <div class="balance-card__item">
            <div class="balance-card__item-label">You Owe</div>
            <div class="balance-card__item-value">${{ number_format($youOwe, 2) }}</div>
        </div>
        <div class="balance-card__item">
            <div class="balance-card__item-label">You're Owed</div>
            <div class="balance-card__item-value">${{ number_format($youAreOwed, 2) }}</div>
        </div>
    </div>
</div>