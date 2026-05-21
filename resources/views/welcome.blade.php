<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>RentWise</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

        <style>
            :root {
                color-scheme: dark;
                --bg: #0a0a0a;
                --text: #fafafa;
                --muted: #8b8b8e;
                --dim: #5a5a5e;
                --line: rgba(255, 255, 255, 0.08);
                --line-strong: rgba(255, 255, 255, 0.14);
            }

            * { box-sizing: border-box; }

            html, body {
                margin: 0;
                min-height: 100%;
                font-family: "Instrument Sans", ui-sans-serif, system-ui, sans-serif;
                background: var(--bg);
                color: var(--text);
                font-size: 15px;
                line-height: 1.5;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }

            body::before {
                content: "";
                position: fixed;
                inset: 0;
                background:
                    radial-gradient(60% 50% at 50% 0%, rgba(255, 255, 255, 0.045), transparent 70%),
                    radial-gradient(40% 40% at 100% 100%, rgba(255, 255, 255, 0.025), transparent 70%);
                pointer-events: none;
                z-index: 0;
            }

            a { color: inherit; text-decoration: none; }

            .shell {
                position: relative;
                z-index: 1;
                min-height: 100vh;
                display: flex;
                flex-direction: column;
            }

            .nav {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 22px 32px;
                border-bottom: 1px solid var(--line);
            }

            .brand {
                display: inline-flex;
                align-items: center;
                gap: 10px;
                font-size: 14px;
                font-weight: 500;
                letter-spacing: -0.01em;
            }

            .brand-mark {
                width: 22px;
                height: 22px;
                border-radius: 6px;
                display: grid;
                place-items: center;
                background: var(--text);
                color: var(--bg);
                font-size: 11px;
                font-weight: 600;
            }

            .nav-links {
                display: flex;
                align-items: center;
                gap: 4px;
            }

            .nav-link {
                padding: 8px 12px;
                font-size: 13px;
                color: var(--muted);
                border-radius: 6px;
                transition: color 120ms ease, background 120ms ease;
            }

            .nav-link:hover { color: var(--text); background: rgba(255, 255, 255, 0.04); }

            .nav-cta {
                margin-left: 8px;
                padding: 8px 14px;
                font-size: 13px;
                font-weight: 500;
                background: var(--text);
                color: var(--bg);
                border-radius: 6px;
                transition: opacity 120ms ease;
            }

            .nav-cta:hover { opacity: 0.88; }

            .hero {
                flex: 1;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 80px 32px;
            }

            .hero-inner {
                width: 100%;
                max-width: 640px;
                text-align: center;
            }

            .eyebrow {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 5px 11px 5px 7px;
                font-size: 12px;
                color: var(--muted);
                background: rgba(255, 255, 255, 0.03);
                border: 1px solid var(--line);
                border-radius: 999px;
                margin-bottom: 28px;
            }

            .eyebrow-dot {
                width: 6px;
                height: 6px;
                border-radius: 999px;
                background: #4ade80;
                box-shadow: 0 0 0 3px rgba(74, 222, 128, 0.15);
            }

            h1 {
                margin: 0 0 20px;
                font-size: clamp(2rem, 4.5vw, 3.25rem);
                line-height: 1.05;
                letter-spacing: -0.035em;
                font-weight: 500;
            }

            h1 .accent {
                color: var(--muted);
            }

            .lead {
                margin: 0 auto 36px;
                max-width: 46ch;
                color: var(--muted);
                font-size: 16px;
                line-height: 1.6;
            }

            .actions {
                display: inline-flex;
                gap: 8px;
                justify-content: center;
            }

            .button {
                height: 40px;
                padding: 0 18px;
                border-radius: 8px;
                border: 1px solid var(--line-strong);
                display: inline-flex;
                align-items: center;
                gap: 6px;
                font-size: 14px;
                font-weight: 500;
                background: transparent;
                color: var(--text);
                transition: border-color 140ms ease, background 140ms ease, transform 140ms ease;
            }

            .button:hover { border-color: rgba(255, 255, 255, 0.22); background: rgba(255, 255, 255, 0.03); }

            .button.primary {
                background: var(--text);
                color: var(--bg);
                border-color: var(--text);
            }

            .button.primary:hover { background: #fff; border-color: #fff; }

            .button .arrow {
                transition: transform 140ms ease;
            }

            .button:hover .arrow { transform: translateX(2px); }

            .footer {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px 32px;
                border-top: 1px solid var(--line);
                color: var(--dim);
                font-size: 12px;
            }

            .footer a { color: var(--muted); }
            .footer a:hover { color: var(--text); }

            @media (max-width: 640px) {
                .nav { padding: 18px 20px; }
                .nav-links .nav-link { display: none; }
                .hero { padding: 56px 20px; }
                .footer { padding: 16px 20px; }
            }
        </style>
    </head>
    <body>
        <div class="shell">
            <nav class="nav">
                <a href="{{ route('home') }}" class="brand">
                    <span class="brand-mark">R</span>
                    <span>RentWise</span>
                </a>

                <div class="nav-links">
                    @auth
                        <a href="{{ route('dashboard') }}" class="nav-cta">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="nav-link">Log in</a>
                        <a href="{{ route('register') }}" class="nav-cta">Get started</a>
                    @endauth
                </div>
            </nav>

            <section class="hero">
                <div class="hero-inner">
                    <span class="eyebrow">
                        <span class="eyebrow-dot"></span>
                        Property management, simplified
                    </span>

                    <h1>
                        Run your rentals <span class="accent">without the noise.</span>
                    </h1>

                    <p class="lead">
                        Properties, tenants, invoices, and utilities — one quiet workspace built for landlords who'd rather not think about software.
                    </p>

                    <div class="actions">
                        @auth
                            <a href="{{ route('dashboard') }}" class="button primary">
                                Open dashboard
                                <span class="arrow">→</span>
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="button primary">
                                Get started
                                <span class="arrow">→</span>
                            </a>
                            <a href="{{ route('login') }}" class="button">Log in</a>
                        @endauth
                    </div>
                </div>
            </section>

            <footer class="footer">
                <span>© {{ date('Y') }} RentWise</span>
                <span>v1.0</span>
            </footer>
        </div>
    </body>
</html>
