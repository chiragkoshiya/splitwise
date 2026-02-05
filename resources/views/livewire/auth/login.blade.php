<div>
    <div class="container" style="max-width: 480px; margin: 0 auto; padding-top: 40px;">
        <div class="text-center" style="margin-bottom: 32px;">
            <div style="font-size: 64px; margin-bottom: 12px;">💸</div>
            <h1 style="font-size: 24px; font-weight: 700;">Sign In</h1>
        </div>

        <div class="card" style="padding: 24px;">
            <form wire:submit="login">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" wire:model="email" class="input" placeholder="your@email.com" required
                        autofocus>
                    @error('email') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div class="form-group">
                    <label class="form-label">Password</label>
                    <input type="password" wire:model="password" class="input" placeholder="Enter password" required>
                    @error('password') <div class="form-error">{{ $message }}</div> @enderror
                </div>

                <div style="margin-top: 24px;">
                    <button type="submit" class="btn btn--primary btn--large btn--full">
                        Sign In
                    </button>
                </div>
            </form>
        </div>

        <div class="card card--compact"
            style="margin-top: 16px; background: #FFF9E6; border: 1px solid #FFD700; padding: 12px;">
            <p style="font-size: 12px; margin-bottom: 4px;"><strong>Demo Account:</strong></p>
            <p style="font-size: 12px; margin-bottom: 2px;">Email: demo@spliteasy.com</p>
            <p style="font-size: 12px;">Password: demo123</p>
        </div>

        <div class="text-center" style="margin-top: 24px;">
            <p style="font-size: 14px; color: var(--color-text-secondary);">
                Don't have an account?
                <a href="{{ route('register') }}"
                    style="color: var(--color-primary); font-weight: 600; text-decoration: none;">Sign up</a>
            </p>
        </div>
    </div>
</div>