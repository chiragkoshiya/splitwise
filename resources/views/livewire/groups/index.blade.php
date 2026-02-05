<div>
    <div>
        @forelse($groups as $group)
            <x-group-item :group="$group" :balance="0" />
        @empty
            <div class="empty-state">
                <div class="empty-state__icon">👥</div>
                <div class="empty-state__title">No Groups Yet</div>
                <div class="empty-state__description"
                    style="font-size: 14px; color: var(--color-text-secondary); margin-top: 8px;">Create a group to start
                    tracking shared expenses</div>
            </div>
        @endforelse
    </div>
</div>