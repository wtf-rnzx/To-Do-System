<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>StagStack</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600,700,800&display=swap" rel="stylesheet" />

        <!-- Styles -->
        <style>
            * {
                box-sizing: border-box;
            }

            body {
                margin: 0;
                min-height: 100vh;
                font-family: 'Figtree', sans-serif;
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
                color: #f8fafc;
            }

            .landing {
                position: relative;
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 2rem 1.25rem;
                background-image: url("{{ asset('images/StagStack-landing.png') }}");
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                overflow: hidden;
            }

            .landing::before {
                content: '';
                position: absolute;
                inset: 0;
                background: rgba(0, 0, 0, 0.52);
            }

            .hero {
                position: relative;
                z-index: 1;
                width: 100%;
                max-width: 780px;
                display: grid;
                place-items: center;
                text-align: center;
                gap: 0.9rem;
            }

            .logo {
                width: min(52vw, 260px);
                height: auto;
                filter: drop-shadow(0 8px 22px rgba(0, 0, 0, 0.35));
            }

            .title {
                margin: 0;
                font-size: clamp(2.25rem, 8vw, 4.25rem);
                font-weight: 800;
                letter-spacing: 0.04em;
                color: #ffffff;
                text-shadow: 0 5px 20px rgba(0, 0, 0, 0.38);
            }

            .quote {
                margin: 0;
                max-width: 38ch;
                font-size: clamp(1rem, 2.6vw, 1.3rem);
                line-height: 1.65;
                font-weight: 500;
                color: rgba(255, 255, 255, 0.92);
                text-shadow: 0 4px 16px rgba(0, 0, 0, 0.35);
            }

            .auth-links {
                margin-top: 1.35rem;
                display: flex;
                flex-wrap: wrap;
                justify-content: center;
                gap: 0.65rem;
            }

            .auth-link {
                display: inline-flex;
                align-items: center;
                justify-content: center;
                padding: 0.58rem 1rem;
                border-radius: 999px;
                border: 1px solid rgba(255, 255, 255, 0.28);
                background: rgba(255, 255, 255, 0.08);
                color: #f8fafc;
                text-decoration: none;
                font-size: 0.95rem;
                font-weight: 700;
                backdrop-filter: blur(5px);
                transition: transform 180ms ease, background-color 180ms ease, border-color 180ms ease;
            }

            .auth-link:hover {
                transform: translateY(-1px);
                background: rgba(255, 255, 255, 0.16);
                border-color: rgba(255, 255, 255, 0.44);
            }

            .animated {
                opacity: 0;
                transform: translateY(-34px);
                animation: slideDownFade 850ms ease-out forwards;
            }

            .logo.animated {
                animation-delay: 0.15s;
            }

            .title.animated {
                animation-delay: 0.38s;
            }

            .quote.animated {
                animation-delay: 0.62s;
            }

            .auth-links.animated {
                animation-delay: 0.83s;
            }

            @keyframes slideDownFade {
                from {
                    opacity: 0;
                    transform: translateY(-34px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            @media (max-width: 640px) {
                .landing {
                    padding-inline: 1rem;
                }

                .hero {
                    gap: 0.75rem;
                }
            }

            @media (prefers-reduced-motion: reduce) {
                .animated {
                    opacity: 1;
                    transform: none;
                    animation: none;
                }
            }
        </style>
    </head>
    <body>
        <main class="landing" role="main">
            <section class="hero" aria-label="StagStack landing hero">
                <img class="logo animated" src="{{ asset('images/StagStack-lightTheme.png') }}" alt="StagStack logo">
                <h1 class="title animated">StagStack</h1>
                <p class="quote animated">From scattered tasks to stacked wins—build momentum one focused move at a time.</p>

                @if (\Illuminate\Support\Facades\Route::has('login'))
                    <nav class="auth-links animated" aria-label="Authentication links">
                        @auth
                            <a href="{{ url('/home') }}" class="auth-link">Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="auth-link">Log in</a>

                            @if (\Illuminate\Support\Facades\Route::has('register'))
                                <a href="{{ route('register') }}" class="auth-link">Register</a>
                            @endif
                        @endauth
                    </nav>
                @endif
            </section>
        </main>
        </div>
    </body>
</html>
