@props(['group', 'balance' => 0])

<a href="{{ route('groups.show', $group) }}" class="card clickable"
    style="display: flex; align-items: center; gap: 12px; padding: 16px; margin-bottom: 12px; text-decoration: none;">
    <div class="list-item__icon">{{ $group->icon ?? '👥' }}</div>
    <div style="flex: 1;">
        <div class="list-item__title">{{ $group->name }}</div>
        <div class="list-item__subtitle">{{ $group->users->count() }} members</div>
    </div>
    <div style="text-align: right;">
        <div class="money {{ $balance > 0 ? 'money--owed' : ($balance < 0 ? 'money--owe' : 'money--settled') }}">
            {{ $balance > 0 ? '+' : '' }}${{ number_format(abs($balance), 2) }}
        </div>
    </div>
</a>