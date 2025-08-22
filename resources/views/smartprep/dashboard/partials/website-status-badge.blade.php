@php
    // Expect $website (Client model) or $client
    $site = $website ?? $client ?? null;
    if (!$site) return;
@endphp

<span class="{{ $site->status_badge_class }}" style="font-size:0.8rem;">{{ $site->status_label }}</span>
@if(isset($showDomain) && $showDomain)
    <small class="text-muted ms-2">{{ $site->domain }}</small>
@endif
