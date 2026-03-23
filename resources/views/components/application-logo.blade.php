@props(['alt' => config('app.name', 'StagStack') . ' logo'])

@php
    $logoThemes = config('branding.logo.themes', []);
    $lightPath = $logoThemes['light'] ?? 'images/StagStack-lightTheme.png';
    $darkPath = $logoThemes['dark'] ?? $lightPath;
    $fallbackPath = config('branding.logo.fallback', $lightPath);

    $lightLogo = asset($lightPath);
    $darkLogo = asset($darkPath);
    $fallbackLogo = asset($fallbackPath);
@endphp

{{-- Default to light logo if theme detection fails; dark logo appears only when .dark is active. --}}
<img
    src="{{ $lightLogo }}"
    alt="{{ $alt }}"
    loading="eager"
    decoding="async"
    onerror="this.onerror=null;this.src='{{ $fallbackLogo }}';"
    data-theme-logo="light"
    {{ $attributes->merge(['class' => 'block dark:hidden']) }}
/>

<img
    src="{{ $darkLogo }}"
    alt="{{ $alt }}"
    loading="eager"
    decoding="async"
    onerror="this.onerror=null;this.src='{{ $fallbackLogo }}';"
    data-theme-logo="dark"
    {{ $attributes->merge(['class' => 'hidden dark:block']) }}
/>
