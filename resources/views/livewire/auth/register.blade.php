<div>
    <div class="container" style="max-width: 480px; margin: 0 auto; padding-top: 40px;">
        <div class="text-center" style="margin-bottom: 32px;">
            <div style="font-size: 64px; margin-bottom: 12px;">💸</div>
            <h1 style="font-size: 24px; font-weight: 700;">Create Account</h1>
        </div>

        <div class="card" style="padding: 24px;">
            <form wire:submit="register">
                <div class="form-group">
                    <label class="form-label">Full Name</label>
                    <input type="text" wire:model="name" class="input" placeholder="John Doe" required>
                    @error('name') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" wire:model="email" class="input" placeholder="your@email.com" required>
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" wire:model="password" class="input" placeholder="Min 8 characters" required>
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Confirm Password</label>
                    <input type="password" wire:model="password_confirmation" class="input"
                        placeholder="Confirm password" required>
                </div>

                <div style="margin-top: 24px;">
                    <button type="submit" class="btn btn--primary btn--large btn--full">
                        Get Started
                    </button>
                </div>
            </form>
        </div>

        <div class="text-center" style="margin-top: 24px;">
            <p style="font-size: 14px; color: var(--color-text-secondary);">
                Already have an account?
                <a href="{{ route('login') }}"
                    style="color: var(--color-primary); font-weight: 600; text-decoration: none;">Sign in</a>
            </p>
        </div>
    </div>
</div>