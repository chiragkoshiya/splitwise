<div>
    <div class="section">
        <div class="section__header">
            <h2 class="section__title">Add New Member</h2>
        </div>
        <div class="card">
            <form wire:submit="addMember">
                <div style="display: flex; gap: 8px;">
                    <div style="flex: 1;">
                        <input type="email" wire:model="email" class="input" placeholder="friend@example.com" required>
                    </div>
                    <button type="submit" class="btn btn--primary">Add</button>
                </div>
                @error('email') <div class="form-error" style="margin-top: 8px;">{{ $message }}</div> @enderror
            </form>
        </div>
    </div>

    <div class="section">
        <div class="section__header">
            <h2 class="section__title">Group Members ({{ $members->count() }})</h2>
        </div>
        <div class="card">
            @foreach($members as $member)
                <div
                    style="display: flex; align-items: center; justify-content: space-between; padding: 12px 0; border-bottom: 1px solid var(--color-border); last-child: border-bottom: none;">
                    <div style="display: flex; align-items: center; gap: 12px;">
                        <div class="list-item__icon" style="width: 40px; height: 40px; background: var(--color-bg);">👤
                        </div>
                        <div>
                            <div style="font-size: 14px; font-weight: 600;">{{ $member->name }}</div>
                            <div style="font-size: 12px; color: var(--color-text-secondary);">{{ $member->email }}</div>
                        </div>
                    </div>
                    @if($member->id != auth()->id() && $group->created_by == auth()->id())
                        <button wire:click="removeMember({{ $member->id }})"
                            wire:confirm="Are you sure you want to remove this member? This is only possible if they have a zero balance."
                            class="btn-icon" style="color: var(--color-danger); font-size: 18px;">🗑️</button>
                    @elseif($member->id == $group->created_by)
                        <span
                            style="font-size: 11px; background: var(--color-primary-light); color: var(--color-primary); padding: 2px 8px; border-radius: 12px; font-weight: 600;">Admin</span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</div>