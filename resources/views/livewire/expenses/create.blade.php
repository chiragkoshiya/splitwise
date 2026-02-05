<div>
    <form wire:submit="save">
        <div class="card" style="margin-bottom: 24px;">
            <div class="form-group">
                <label class="form-label">Title</label>
                <input wire:model="title" type="text" class="input" placeholder="e.g. Dinner, Movie tickets">
                @error('title') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Amount</label>
                <div style="position: relative;">
                    <span
                        style="position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: var(--color-text-tertiary); font-weight: 600;">$</span>
                    <input wire:model="amount" type="number" step="0.01" class="input" style="padding-left: 32px;"
                        placeholder="0.00">
                </div>
                @error('amount') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Paid By</label>
                <select wire:model="paid_by" class="input">
                    @foreach($members as $member)
                        <option value="{{ $member->id }}">{{ $member->id == auth()->id() ? 'You' : $member->name }}</option>
                    @endforeach
                </select>
                @error('paid_by') <div class="form-error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="section">
            <div class="section__header">
                <h2 class="section__title">Split Between Members</h2>
            </div>
            <div class="card">
                @foreach($members as $member)
                    <label class="clickable"
                        style="display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--color-border); cursor: pointer;">
                        <input type="checkbox" wire:model="selected_members" value="{{ $member->id }}"
                            style="width: 20px; height: 20px; accent-color: var(--color-primary);">
                        <div style="flex: 1;">
                            <div style="font-size: 14px; font-weight: 600;">{{ $member->name }}</div>
                            <div style="font-size: 12px; color: var(--color-text-secondary);">
                                @if(in_array($member->id, $selected_members) && $amount > 0)
                                    Shared portion: ${{ number_format($amount / max(1, count($selected_members)), 2) }}
                                @else
                                    Not involved
                                @endif
                            </div>
                        </div>
                    </label>
                @endforeach
                @error('selected_members') <div class="form-error" style="margin-top: 8px;">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="section" style="margin-top: 32px;">
            <button type="submit" class="btn btn--primary btn--large btn--full">
                Add Expense
            </button>
        </div>
    </form>
</div>