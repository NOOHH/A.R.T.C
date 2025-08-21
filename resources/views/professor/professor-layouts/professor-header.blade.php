{{-- resources/views/components/professor-header.blade.php --}}
@php
    // Get brand name from NavbarComposer data (tenant-specific) or fallback
    $brandName = $navbar['brand_name'] ?? 
                 $settings['navbar']['brand_name'] ?? 
                 $navbarBrandName ?? 
                 'Ascendo Review & Training Center';
    
    // Get brand logo from NavbarComposer data (tenant-specific) or fallback
    $brandLogo = $navbar['brand_logo'] ?? $settings['navbar']['brand_logo'] ?? null;
    $defaultLogo = asset('images/ARTC_logo.png');
@endphp

<header class="main-header">
    <div class="header-left">
        <!-- Brand Logo and Text -->
        <div class="brand-container d-flex align-items-center gap-3">
            @if($brandLogo)
                <img src="{{ \Illuminate\Support\Facades\Storage::url($brandLogo) }}" 
                     alt="{{ $brandName }}" 
                     class="brand-logo"
                     onerror="this.src='{{ $defaultLogo }}'">
            @else
                <img src="{{ $defaultLogo }}" alt="{{ $brandName }}" class="brand-logo">
            @endif
            <div class="brand-text-area d-flex flex-column justify-content-center">
                <span class="brand-text fw-bold">{{ $brandName }}</span>
                <span class="brand-subtext text-muted">Professor Portal</span>
            </div>
        </div>
    </div>

    <div class="header-center">
        <!-- Universal Search -->
        <div class="search-container">
            @include('components.universal-search')
        </div>
    </div>

    <div class="header-right">
        <!-- Chat Icon Button -->
        <button class="btn btn-link p-0 ms-2" id="chatTriggerBtn" title="Open Chat" style="font-size: 1.5rem; color: #764ba2;">
            <i class="bi bi-chat-dots"></i>
        </button>
        
        <!-- Mobile Profile Icon -->
    </div>
</header>
  