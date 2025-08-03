{{-- resources/views/components/professor-header.blade.php --}}
<header class="main-header">
    <div class="header-left">
        <!-- Brand Logo and Text -->
        <div class="brand-container d-flex align-items-center gap-3">
            <img src="{{ asset('images/ARTC_logo.png') }}" alt="A.R.T.C" class="brand-logo">
            <div class="brand-text-area d-flex flex-column justify-content-center">
                <span class="brand-text fw-bold">Ascendo Review &amp; Training Center</span>
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
  