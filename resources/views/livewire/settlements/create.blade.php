<div>
    <div class="card" style="padding: 24px;">
        <form wire:submit="save">
            <div class="form-group">
                <label class="form-label">From</label>
                <select wire:model.live="paid_from" class="input">
                    @foreach($members as $member)
                        <option value="{{ $member->id }}">{{ $member->id == auth()->id() ? 'You' : $member->name }}</option>
                    @endforeach
                </select>
                @error('paid_from') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">To</label>
                <select wire:model.live="paid_to" class="input">
                    <option value="">Select recipient</option>
                    @foreach($members as $member)
                        @if($member->id != $paid_from)
                            <option value="{{ $member->id }}">{{ $member->id == auth()->id() ? 'You' : $member->name }}</option>
                        @endif
                    @endforeach
                </select>
                @error('paid_to') <div class="form-error">{{ $message }}</div> @enderror
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
                <label class="form-label">Payment Mode</label>
                <div class="grid--2col" style="gap: 12px; margin-top: 8px;">
                    <button type="button" wire:click="$set('payment_mode', 'cash')"
                        class="btn {{ $payment_mode === 'cash' ? 'btn--primary' : 'btn--outline' }}">💵 Cash</button>
                    <button type="button" wire:click="$set('payment_mode', 'online')"
                        class="btn {{ $payment_mode === 'online' ? 'btn--primary' : 'btn--outline' }}">📱
                        Online</button>
                </div>
                @error('payment_mode') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Note (Optional)</label>
                <input wire:model="note" type="text" class="input" placeholder="e.g. Paid via Venmo">
                @error('note') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="margin-top: 32px;">
                <button type="submit" class="btn btn--primary btn--full btn--large">
                    Record Payment
                </button>
            </div>
        </form>
    </div>
</div>