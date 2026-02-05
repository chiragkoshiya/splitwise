<div>
    <div class="card">
        <form wire:submit="createGroup">
            <div class="form-group">
                <label for="name" class="form-label">Group Name</label>
                <input type="text" id="name" wire:model="name" class="input" placeholder="Weekend Trip" required
                    autofocus>
                @error('name') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <div class="form-group">
                <label class="form-label">Choose Icon</label>
                <div class="grid--2col">
                    <button type="button" wire:click="$set('icon', '✈️')"
                        class="btn {{ $icon === '✈️' ? 'btn--primary' : 'btn--outline' }}">✈️ Trip</button>
                    <button type="button" wire:click="$set('icon', '🏠')"
                        class="btn {{ $icon === '🏠' ? 'btn--primary' : 'btn--outline' }}">🏠 Home</button>
                    <button type="button" wire:click="$set('icon', '👥')"
                        class="btn {{ $icon === '👥' ? 'btn--primary' : 'btn--outline' }}">👥 Friends</button>
                    <button type="button" wire:click="$set('icon', '🎉')"
                        class="btn {{ $icon === '🎉' ? 'btn--primary' : 'btn--outline' }}">🎉 Event</button>
                </div>
                @error('icon') <div class="form-error">{{ $message }}</div> @enderror
            </div>

            <button type="submit" class="btn btn--primary btn--full btn--large">
                Create Group
            </button>
        </form>
    </div>
</div>