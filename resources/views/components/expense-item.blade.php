@props(['expense'])

<a href="{{ route('expenses.show', $expense) }}" class="card clickable"
    style="padding: 16px; margin-bottom: 12px; text-decoration: none;">
    <div style="display: flex; align-items: flex-start; gap: 12px;">
        <div class="list-item__icon" style="background-color: var(--color-secondary);">💳</div>
        <div style="flex: 1; min-width: 0;">
            <div class="list-item__title">{{ $expense->description }}</div>
            <div class="list-item__subtitle">
                {{ $expense->group->name }} • {{ $expense->created_at->format('M d') }}
            </div>
        </div>
        <div style="text-align: right;">
            <div style="font-size: 16px; font-weight: 700; font-family: var(--font-mono); color: var(--color-text);">
                ${{ number_format($expense->total_amount, 2) }}
            </div>
            <div style="font-size: 13px; color: var(--color-text-secondary);">
                {{ $expense->paidByUser->name }} paid
            </div>
        </div>
    </div>
</a>