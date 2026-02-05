<div>
    <div class="card" style="padding: 24px;">
        <form wire:submit="save">
            <div class="form-group">
                <label for="name" class="form-label">Group Name</label>
                <input type="text" id="name" wire:model="name" class="input" placeholder="Weekend Trip" required
                    autofocus>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Choose Icon</label>
                <div class="grid--2col" style="gap: 12px; margin-top: 8px;">
                    @php
                        $icons = ['✈️' => 'Trip', '🏠' => 'Home', '👥' => 'Friends', '🎉' => 'Event', '🍔' => 'Food', '🚗' => 'Travel'];
                    @endphp
                    @foreach($icons as $emoji => $label)
                        <button type="button" wire:click="$set('icon', '{{ $emoji }}')"
                            class="btn {{ $icon === $emoji ? 'btn--primary' : 'btn--outline' }}"
                            style="display: flex; align-items: center; justify-content: center; gap: 8px; padding: 12px;">
                            <span>{{ $emoji }}</span>
                            <span style="font-size: 13px;">{{ $label }}</span>
                        </button>
                    @endforeach
                </div>
                @error('icon') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div style="margin-top: 32px;">
                <button type="submit" class="btn btn--primary btn--full btn--large">
                    Create Group
                </button>
            </div>
        </form>
    </div>
</div>