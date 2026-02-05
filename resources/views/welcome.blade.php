<x-guest-layout>
    <x-slot:title>Welcome</x-slot>

        <div class="welcome">
            <div style="font-size: 80px; margin-bottom: 24px;">💸</div>
            <h1 class="welcome__title"
                style="font-size: 32px; font-weight: 700; margin-bottom: 12px; color: var(--color-text);">SplitEasy</h1>
            <p class="welcome__subtitle"
                style="font-size: 16px; color: var(--color-text-secondary); margin-bottom: 32px; max-width: 300px; margin-left: auto; margin-right: auto;">
                Share expenses with friends. Track costs and settle up easily.
            </p>

            <div class="welcome__actions"
                style="display: flex; flex-direction: column; gap: 12px; width: 100%; max-width: 360px; margin: 0 auto;">
                <a href="{{ route('register') }}" class="btn btn--primary btn--large btn--full">
                    Get Started
                </a>
                <a href="{{ route('login') }}" class="btn btn--outline btn--large btn--full">
                    Sign In
                </a>
            </div>

            <!-- Features -->
            <div style="margin-top: 60px; width: 100%; max-width: 360px; margin-left: auto; margin-right: auto;">
                <div class="grid--2col" style="gap: 12px; display: grid; grid-template-columns: repeat(2, 1fr);">
                    <div class="feature-item"
                        style="background: var(--color-surface); padding: 16px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
                        <div class="feature-item__icon" style="font-size: 32px; margin-bottom: 8px;">📱</div>
                        <div class="feature-item__title" style="font-size: 14px; font-weight: 600; margin-bottom: 4px;">
                            Mobile First</div>
                        <div class="feature-item__description"
                            style="font-size: 12px; color: var(--color-text-secondary);">Built for your phone</div>
                    </div>
                    <div class="feature-item"
                        style="background: var(--color-surface); padding: 16px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
                        <div class="feature-item__icon" style="font-size: 32px; margin-bottom: 8px;">⚡</div>
                        <div class="feature-item__title" style="font-size: 14px; font-weight: 600; margin-bottom: 4px;">
                            Instant Split</div>
                        <div class="feature-item__description"
                            style="font-size: 12px; color: var(--color-text-secondary);">Split in seconds</div>
                    </div>
                    <div class="feature-item"
                        style="background: var(--color-surface); padding: 16px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
                        <div class="feature-item__icon" style="font-size: 32px; margin-bottom: 8px;">👥</div>
                        <div class="feature-item__title" style="font-size: 14px; font-weight: 600; margin-bottom: 4px;">
                            Group Tracking</div>
                        <div class="feature-item__description"
                            style="font-size: 12px; color: var(--color-text-secondary);">Organize by groups</div>
                    </div>
                    <div class="feature-item"
                        style="background: var(--color-surface); padding: 16px; border-radius: var(--radius-lg); box-shadow: var(--shadow-sm);">
                        <div class="feature-item__icon" style="font-size: 32px; margin-bottom: 8px;">💰</div>
                        <div class="feature-item__title" style="font-size: 14px; font-weight: 600; margin-bottom: 4px;">
                            Clear Balances</div>
                        <div class="feature-item__description"
                            style="font-size: 12px; color: var(--color-text-secondary);">See who owes what</div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .welcome {
                min-height: 100vh;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: var(--space-xl);
                text-align: center;
            }
        </style>
</x-guest-layout>