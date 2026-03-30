<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>StagStack</title>
        <script>
            (function () {
                const savedTheme = localStorage.getItem('stagstack-theme') || 'light';
                document.documentElement.setAttribute('data-theme', savedTheme);
            })();
        </script>

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
                transition: background-image 0.3s ease;
            }

            [data-theme='dark'] .landing {
                background-image: url("{{ asset('images/StagStack-landing-dark.png') }}");
            }

            .landing::before {
                content: '';
                position: absolute;
                inset: 0;
                background: rgba(0, 0, 0, 0.52);
                transition: background 0.3s ease;
            }

            [data-theme='dark'] .landing::before {
                background: rgba(1, 8, 24, 0.72);
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
                transition: opacity 0.3s ease;
            }

            [data-theme='dark'] .logo-light {
                display: none;
            }

            [data-theme='light'] .logo-dark {
                display: none;
            }

            .theme-toggle {
                position: absolute;
                top: 1.5rem;
                right: 1.5rem;
                z-index: 10;
                background: rgba(255, 255, 255, 0.15);
                border: 1px solid rgba(255, 255, 255, 0.3);
                border-radius: 50%;
                width: 44px;
                height: 44px;
                display: flex;
                align-items: center;
                justify-content: center;
                cursor: pointer;
                backdrop-filter: blur(8px);
                color: #fff;
                transition: all 0.2s ease;
            }

            .theme-toggle:hover {
                background: rgba(255, 255, 255, 0.25);
                transform: scale(1.05);
            }

            .theme-toggle svg {
                width: 20px;
                height: 20px;
                fill: currentColor;
                transition: all 0.3s ease;
            }
            
            [data-theme='dark'] .theme-toggle {
                background: rgba(0, 0, 0, 0.4);
                border-color: rgba(255, 255, 255, 0.1);
            }
            
            /* Show correct icon */
            [data-theme='light'] .icon-sun { display: none; }
            [data-theme='dark'] .icon-moon { display: none; }

            .title {
                margin: 0;
                font-size: clamp(2.25rem, 8vw, 4.25rem);
                font-weight: 800;
                letter-spacing: 0.04em;
                color: #ffffff;
                text-shadow: 0 5px 20px rgba(0, 0, 0, 0.38);
            }

            [data-theme='dark'] .title {
                text-shadow: 0 5px 20px rgba(1, 8, 24, 0.8);
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

            [data-theme='dark'] .quote {
                color: rgba(255, 255, 255, 0.85);
                text-shadow: 0 4px 16px rgba(1, 8, 24, 0.9);
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
            
            [data-theme='dark'] .auth-link {
                border-color: rgba(255, 255, 255, 0.15);
                background: rgba(0, 0, 0, 0.4);
            }
            
            [data-theme='dark'] .auth-link:hover {
                background: rgba(0, 0, 0, 0.6);
                border-color: rgba(255, 255, 255, 0.3);
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
            <!-- Theme Toggle Button -->
            <button class="theme-toggle" id="themeToggle" aria-label="Toggle Dark Mode">
                <!-- Moon icon for light mode (click to go dark) -->
                <svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M21.64 13a1 1 0 01-1.05-.14 8.05 8.05 0 01-3.37-7.37 1 1 0 011.64-.81 10 10 0 102.78 8.32 1 1 0 010 0z"/></svg>
                <!-- Sun icon for dark mode (click to go light) -->
                <svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24"><path d="M12 17a5 5 0 110-10 5 5 0 010 10zm0-2a3 3 0 100-6 3 3 0 000 6zm0-13a1 1 0 011 1v2a1 1 0 11-2 0V3a1 1 0 011-1zm0 19a1 1 0 011 1v2a1 1 0 11-2 0v-2a1 1 0 011-1zM5 12a1 1 0 01-1-1H2a1 1 0 110-2h2a1 1 0 011 1zm19 0a1 1 0 01-1 1h-2a1 1 0 110-2h2a1 1 0 011 1zM7.05 7.05a1 1 0 011.41-1.41l1.42 1.41a1 1 0 11-1.42 1.42L7.05 7.05zm12.73 12.73a1 1 0 01-1.41 1.41l-1.42-1.41a1 1 0 111.42-1.42l1.41 1.42zM7.05 16.95a1 1 0 011.41 1.41l1.42-1.41a1 1 0 11-1.42-1.42l-1.42 1.41zm12.73-12.73a1 1 0 01-1.41-1.41l-1.42 1.41a1 1 0 111.42-1.42l1.41 1.42z"/></svg>
            </button>

            <section class="hero" aria-label="StagStack landing hero">
                <img class="logo animated logo-light" src="{{ asset('images/StagStack-lightTheme.png') }}" alt="StagStack logo">
                <img class="logo animated logo-dark" src="{{ asset('images/StagStack-darkTheme.png') }}" alt="StagStack logo">
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
        
        <script>
            const themeToggleBtn = document.getElementById('themeToggle');
            const htmlElement = document.documentElement;

            themeToggleBtn.addEventListener('click', () => {
                const currentTheme = htmlElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                
                htmlElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('stagstack-theme', newTheme);
            });
        </script>
    </body>
</html>
