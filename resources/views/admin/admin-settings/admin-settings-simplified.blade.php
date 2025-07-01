@extends('admin.admin-dashboard-layout')

@section('title', 'Settings')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/admin-settings/admin-settings.css') }}">
<style>
/* Simplified UI Styles */
.settings-main-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.settings-tabs {
    display: flex;
    border-bottom: 2px solid #e1e5e9;
    margin-bottom: 30px;
    gap: 0;
}

.tab-button {
    padding: 12px 24px;
    background: #f8f9fa;
    border: none;
    border-bottom: 3px solid transparent;
    cursor: pointer;
    font-weight: 500;
    color: #6c757d;
    transition: all 0.3s ease;
    border-radius: 8px 8px 0 0;
}

.tab-button.active {
    background: #ffffff;
    color: #495057;
    border-bottom-color: #667eea;
}

.tab-button:hover {
    background: #e9ecef;
    color: #495057;
}

.tab-content {
    display: none;
}

.tab-content.active {
    display: block;
}

.simplified-form-group {
    margin-bottom: 20px;
}

.simplified-form-group label {
    display: block;
    font-weight: 600;
    color: #495057;
    margin-bottom: 8px;
}

.color-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}

.color-field {
    display: flex;
    align-items: center;
    gap: 10px;
}

.color-field input[type="color"] {
    width: 40px;
    height: 40px;
    border: none;
    border-radius: 8px;
    cursor: pointer;
}

.color-field .color-info {
    flex: 1;
}

.color-field .color-info small {
    color: #6c757d;
    font-size: 0.85rem;
}

.gradient-toggle {
    margin-top: 10px;
}

.gradient-toggle input[type="checkbox"] {
    margin-right: 8px;
}

.gradient-controls {
    margin-top: 15px;
    padding: 15px;
    background: #f8f9fa;
    border-radius: 8px;
    display: none;
}

.gradient-controls.show {
    display: block;
}

.preview-container {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 20px;
    margin-top: 20px;
}

.preview-title {
    font-weight: 600;
    color: #495057;
    margin-bottom: 15px;
    text-align: center;
}

.image-upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 20px;
    text-align: center;
    margin: 15px 0;
    transition: all 0.3s ease;
}

.image-upload-area:hover {
    border-color: #667eea;
    background: #f8f9ff;
}

.image-upload-area.has-image {
    border-style: solid;
    border-color: #28a745;
    background: #f8fff9;
}

.current-image-preview {
    max-width: 200px;
    max-height: 150px;
    border-radius: 8px;
    margin: 10px 0;
}

.remove-image-btn {
    background: #dc3545;
    color: white;
    border: none;
    padding: 8px 16px;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.85rem;
    margin-top: 10px;
}

.remove-image-btn:hover {
    background: #c82333;
}

.section-divider {
    border: none;
    height: 1px;
    background: linear-gradient(90deg, transparent, #dee2e6, transparent);
    margin: 30px 0;
}
</style>
@endpush

@section('content')
<div class="settings-main-container">
    <h1 style="margin-bottom: 30px; color: #495057;">
        <i class="fas fa-cog"></i> Website Customization
    </h1>

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Simplified Tab Navigation --}}
    <div class="settings-tabs">
        <button class="tab-button active" data-tab="homepage">
            <i class="fas fa-home"></i> Homepage
        </button>
        <button class="tab-button" data-tab="navigation">
            <i class="fas fa-bars"></i> Navigation
        </button>
        <button class="tab-button" data-tab="pages">
            <i class="fas fa-file-alt"></i> Pages
        </button>
        <button class="tab-button" data-tab="login">
            <i class="fas fa-sign-in-alt"></i> Login
        </button>
        <button class="tab-button" data-tab="branding">
            <i class="fas fa-image"></i> Branding
        </button>
    </div>

    {{-- Homepage Tab --}}
    <div class="tab-content active" id="homepage">
        <form action="{{ route('admin.settings.update.homepage') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="simplified-form-group">
                <label>Homepage Title</label>
                <input type="text" 
                       name="homepage_title" 
                       value="{{ old('homepage_title', $settings['homepage']['title'] ?? 'ENROLL NOW') }}"
                       style="width: 100%; padding: 12px; border: 2px solid #e1e5e9; border-radius: 8px; font-size: 1rem;">
            </div>

            <div class="simplified-form-group">
                <label>Background Colors</label>
                <div class="color-row">
                    <div class="color-field">
                        <input type="color" 
                               name="homepage_background_color" 
                               id="homepage_bg_color"
                               value="{{ old('homepage_background_color', $settings['homepage']['background_color'] ?? '#667eea') }}">
                        <div class="color-info">
                            <strong>Primary Color</strong>
                            <small>Main background color</small>
                        </div>
                    </div>
                    <div class="color-field">
                        <input type="color" 
                               name="homepage_text_color" 
                               value="{{ old('homepage_text_color', $settings['homepage']['text_color'] ?? '#ffffff') }}">
                        <div class="color-info">
                            <strong>Text Color</strong>
                            <small>Color of text on homepage</small>
                        </div>
                    </div>
                </div>
                
                <div class="gradient-toggle">
                    <label>
                        <input type="checkbox" id="homepage_gradient_toggle" 
                               {{ (isset($settings['homepage']['gradient_color']) && $settings['homepage']['gradient_color']) ? 'checked' : '' }}>
                        <strong>Enable Gradient Background</strong>
                    </label>
                </div>
                
                <div class="gradient-controls" id="homepage_gradient_controls" 
                     {{ (isset($settings['homepage']['gradient_color']) && $settings['homepage']['gradient_color']) ? 'style=display:block' : '' }}>
                    <div class="color-field">
                        <input type="color" 
                               name="homepage_gradient_color" 
                               id="homepage_gradient_color"
                               value="{{ old('homepage_gradient_color', $settings['homepage']['gradient_color'] ?? '') }}">
                        <div class="color-info">
                            <strong>Gradient Color</strong>
                            <small>Second color for gradient effect</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="simplified-form-group">
                <label>Background Image (Optional)</label>
                <div class="image-upload-area {{ isset($settings['homepage']['background_image']) ? 'has-image' : '' }}">
                    @if(isset($settings['homepage']['background_image']) && $settings['homepage']['background_image'])
                        <img src="{{ asset('storage/' . $settings['homepage']['background_image']) }}" 
                             class="current-image-preview" alt="Current background">
                        <div>
                            <button type="button" class="remove-image-btn" onclick="removeHomepageImage()">
                                <i class="fas fa-trash"></i> Remove Image
                            </button>
                        </div>
                    @else
                        <i class="fas fa-cloud-upload-alt" style="font-size: 2rem; color: #6c757d; margin-bottom: 10px;"></i>
                        <div>Click to upload background image</div>
                        <small style="color: #6c757d;">JPG, PNG, GIF, WebP (max 5MB)</small>
                    @endif
                    <input type="file" 
                           name="homepage_background_image" 
                           accept="image/jpeg,image/jpg,image/png,image/gif,image/webp"
                           style="position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer;">
                </div>
            </div>

            <hr class="section-divider">

            <div class="preview-container">
                <div class="preview-title">Homepage Preview</div>
                <div id="homepage-preview" 
                     style="background: {{ isset($settings['homepage']['gradient_color']) && $settings['homepage']['gradient_color'] ? 'linear-gradient(135deg, ' . ($settings['homepage']['background_color'] ?? '#667eea') . ' 0%, ' . $settings['homepage']['gradient_color'] . ' 100%)' : ($settings['homepage']['background_color'] ?? '#667eea') }}; 
                            color: {{ $settings['homepage']['text_color'] ?? '#ffffff' }};
                            padding: 40px;
                            text-align: center;
                            border-radius: 12px;
                            font-size: 2rem;
                            font-weight: bold;
                            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);">
                    {{ $settings['homepage']['title'] ?? 'ENROLL NOW' }}
                </div>
            </div>

            <button type="submit" style="width: 100%; padding: 15px; background: #667eea; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; margin-top: 20px;">
                <i class="fas fa-save"></i> Update Homepage
            </button>
        </form>
    </div>

    {{-- Navigation Tab --}}
    <div class="tab-content" id="navigation">
        <form action="{{ route('admin.settings.update.navbar') }}" method="POST">
            @csrf
            
            <div class="simplified-form-group">
                <label>Brand Name</label>
                <input type="text" 
                       name="navbar_brand_name" 
                       value="{{ old('navbar_brand_name', $settings['navbar']['brand_name'] ?? 'Ascendo Review and Training Center') }}"
                       style="width: 100%; padding: 12px; border: 2px solid #e1e5e9; border-radius: 8px; font-size: 1rem;">
            </div>

            <div class="simplified-form-group">
                <label>Navigation Colors</label>
                <div class="color-row">
                    <div class="color-field">
                        <input type="color" 
                               name="navbar_background_color" 
                               id="navbar_bg_color"
                               value="{{ old('navbar_background_color', $settings['navbar']['background_color'] ?? '#f1f1f1') }}">
                        <div class="color-info">
                            <strong>Background</strong>
                            <small>Navigation bar background</small>
                        </div>
                    </div>
                    <div class="color-field">
                        <input type="color" 
                               name="navbar_text_color" 
                               value="{{ old('navbar_text_color', $settings['navbar']['text_color'] ?? '#222222') }}">
                        <div class="color-info">
                            <strong>Text Color</strong>
                            <small>Navigation text and links</small>
                        </div>
                    </div>
                </div>
                
                <div class="gradient-toggle">
                    <label>
                        <input type="checkbox" id="navbar_gradient_toggle"
                               {{ (isset($settings['navbar']['gradient_color']) && $settings['navbar']['gradient_color']) ? 'checked' : '' }}>
                        <strong>Enable Gradient Background</strong>
                    </label>
                </div>
                
                <div class="gradient-controls" id="navbar_gradient_controls"
                     {{ (isset($settings['navbar']['gradient_color']) && $settings['navbar']['gradient_color']) ? 'style=display:block' : '' }}>
                    <div class="color-field">
                        <input type="color" 
                               name="navbar_gradient_color" 
                               id="navbar_gradient_color"
                               value="{{ old('navbar_gradient_color', $settings['navbar']['gradient_color'] ?? '') }}">
                        <div class="color-info">
                            <strong>Gradient Color</strong>
                            <small>Second color for gradient</small>
                        </div>
                    </div>
                </div>
            </div>

            <div class="preview-container">
                <div class="preview-title">Navigation Preview</div>
                <div id="navbar-preview"
                     style="background: {{ isset($settings['navbar']['gradient_color']) && $settings['navbar']['gradient_color'] ? 'linear-gradient(135deg, ' . ($settings['navbar']['background_color'] ?? '#f1f1f1') . ' 0%, ' . $settings['navbar']['gradient_color'] . ' 100%)' : ($settings['navbar']['background_color'] ?? '#f1f1f1') }}; 
                            color: {{ $settings['navbar']['text_color'] ?? '#222222' }};
                            padding: 15px 20px;
                            border-radius: 8px;
                            display: flex;
                            justify-content: space-between;
                            align-items: center;">
                    <strong>{{ $settings['navbar']['brand_name'] ?? 'Ascendo Review and Training Center' }}</strong>
                    <span>Home | Programs | About | Contact</span>
                </div>
            </div>

            <button type="submit" style="width: 100%; padding: 15px; background: #667eea; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; margin-top: 20px;">
                <i class="fas fa-save"></i> Update Navigation
            </button>
        </form>
    </div>

    {{-- Pages Tab (Program Cards, Footer) --}}
    <div class="tab-content" id="pages">
        <!-- Program Cards Section -->
        <h3 style="color: #495057; border-bottom: 2px solid #e1e5e9; padding-bottom: 10px;">Program Cards</h3>
        <form action="{{ route('admin.settings.update.program-cards') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="simplified-form-group">
                <label>Card Colors</label>
                <div class="color-row">
                    <div class="color-field">
                        <input type="color" 
                               name="program_card_background_color" 
                               id="program_bg_color"
                               value="{{ old('program_card_background_color', $settings['program_cards']['background_color'] ?? '#f9f9f9') }}">
                        <div class="color-info">
                            <strong>Background</strong>
                            <small>Card background color</small>
                        </div>
                    </div>
                    <div class="color-field">
                        <input type="color" 
                               name="program_card_text_color" 
                               value="{{ old('program_card_text_color', $settings['program_cards']['text_color'] ?? '#333333') }}">
                        <div class="color-info">
                            <strong>Text Color</strong>
                            <small>Card text color</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Enrollment Button Colors -->
            <div class="simplified-form-group">
                <label>Enrollment Button</label>
                <div class="color-row">
                    <div class="color-field">
                        <input type="color" 
                               name="enrollment_button_color" 
                               value="{{ old('enrollment_button_color', $settings['program_cards']['enrollment_button_color'] ?? '#667eea') }}">
                        <div class="color-info">
                            <strong>Button Color</strong>
                            <small>Enroll button background</small>
                        </div>
                    </div>
                    <div class="color-field">
                        <input type="color" 
                               name="enrollment_button_text_color" 
                               value="{{ old('enrollment_button_text_color', $settings['program_cards']['enrollment_button_text_color'] ?? '#ffffff') }}">
                        <div class="color-info">
                            <strong>Button Text</strong>
                            <small>Enroll button text color</small>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" style="width: 100%; padding: 15px; background: #667eea; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; margin-top: 20px;">
                <i class="fas fa-save"></i> Update Program Cards
            </button>
        </form>

        <hr class="section-divider">

        <!-- Footer Section -->
        <h3 style="color: #495057; border-bottom: 2px solid #e1e5e9; padding-bottom: 10px;">Footer</h3>
        <form action="{{ route('admin.settings.update.footer') }}" method="POST">
            @csrf
            
            <div class="simplified-form-group">
                <label>Footer Text</label>
                <textarea name="footer_text" 
                          rows="3" 
                          style="width: 100%; padding: 12px; border: 2px solid #e1e5e9; border-radius: 8px; resize: vertical;">{{ old('footer_text', $settings['footer']['text'] ?? '© Copyright Ascendo Review and Training Center. All Rights Reserved.') }}</textarea>
            </div>

            <div class="simplified-form-group">
                <label>Footer Colors</label>
                <div class="color-row">
                    <div class="color-field">
                        <input type="color" 
                               name="footer_background_color" 
                               value="{{ old('footer_background_color', $settings['footer']['background_color'] ?? '#ffffff') }}">
                        <div class="color-info">
                            <strong>Background</strong>
                            <small>Footer background color</small>
                        </div>
                    </div>
                    <div class="color-field">
                        <input type="color" 
                               name="footer_text_color" 
                               value="{{ old('footer_text_color', $settings['footer']['text_color'] ?? '#444444') }}">
                        <div class="color-info">
                            <strong>Text Color</strong>
                            <small>Footer text color</small>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" style="width: 100%; padding: 15px; background: #667eea; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; margin-top: 20px;">
                <i class="fas fa-save"></i> Update Footer
            </button>
        </form>
    </div>

    {{-- Login Tab --}}
    <div class="tab-content" id="login">
        <form action="{{ route('admin.settings.update.login') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="simplified-form-group">
                <label>Login Page Colors</label>
                <div class="color-row">
                    <div class="color-field">
                        <input type="color" 
                               name="login_background_color" 
                               value="{{ old('login_background_color', $settings['login']['background_color'] ?? '#f8f9fa') }}">
                        <div class="color-info">
                            <strong>Background</strong>
                            <small>Page background color</small>
                        </div>
                    </div>
                    <div class="color-field">
                        <input type="color" 
                               name="login_accent_color" 
                               value="{{ old('login_accent_color', $settings['login']['accent_color'] ?? '#667eea') }}">
                        <div class="color-info">
                            <strong>Accent Color</strong>
                            <small>Buttons and links</small>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" style="width: 100%; padding: 15px; background: #667eea; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; margin-top: 20px;">
                <i class="fas fa-save"></i> Update Login Page
            </button>
        </form>
    </div>

    {{-- Branding Tab --}}
    <div class="tab-content" id="branding">
        <form action="{{ route('admin.settings.global.logo') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="simplified-form-group">
                <label>Website Logo</label>
                <div class="image-upload-area {{ isset($settings['global_logo']) ? 'has-image' : '' }}">
                    @if(isset($settings['global_logo']) && $settings['global_logo'])
                        <img src="{{ asset('storage/' . $settings['global_logo']) }}" 
                             class="current-image-preview" alt="Current logo">
                        <div>
                            <button type="button" class="remove-image-btn" onclick="removeGlobalLogo()">
                                <i class="fas fa-trash"></i> Remove Logo
                            </button>
                        </div>
                    @else
                        <i class="fas fa-image" style="font-size: 2rem; color: #6c757d; margin-bottom: 10px;"></i>
                        <div>Upload your website logo</div>
                        <small style="color: #6c757d;">JPG, PNG, GIF, WebP, SVG (max 5MB)</small>
                    @endif
                    <input type="file" 
                           name="global_logo" 
                           accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/svg+xml"
                           style="position: absolute; opacity: 0; width: 100%; height: 100%; cursor: pointer;">
                </div>
                <small style="color: #6c757d;">This logo will be used across all pages (navbar, login, etc.)</small>
            </div>

            <button type="submit" style="width: 100%; padding: 15px; background: #667eea; color: white; border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 600; margin-top: 20px;">
                <i class="fas fa-save"></i> Update Logo
            </button>
        </form>
    </div>
</div>

<script>
// Simplified Tab System
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    tabButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetTab = this.dataset.tab;
            
            // Remove active class from all tabs and contents
            tabButtons.forEach(btn => btn.classList.remove('active'));
            tabContents.forEach(content => content.classList.remove('active'));
            
            // Add active class to clicked tab and corresponding content
            this.classList.add('active');
            document.getElementById(targetTab).classList.add('active');
        });
    });
    
    // Gradient toggles
    const gradientToggles = document.querySelectorAll('[id$="_gradient_toggle"]');
    gradientToggles.forEach(toggle => {
        const prefix = toggle.id.replace('_gradient_toggle', '');
        const controls = document.getElementById(prefix + '_gradient_controls');
        
        if (controls) {
            toggle.addEventListener('change', function() {
                if (this.checked) {
                    controls.classList.add('show');
                    controls.style.display = 'block';
                } else {
                    controls.classList.remove('show');
                    controls.style.display = 'none';
                    // Clear gradient color when disabled
                    const gradientInput = document.getElementById(prefix + '_gradient_color');
                    if (gradientInput) gradientInput.value = '';
                }
                updatePreviews();
            });
        }
    });
    
    // Real-time preview updates
    const colorInputs = document.querySelectorAll('input[type="color"], input[name*="title"], input[name*="brand_name"], textarea[name*="footer_text"]');
    colorInputs.forEach(input => {
        input.addEventListener('input', updatePreviews);
    });
    
    function updatePreviews() {
        // Homepage preview
        const homepagePreview = document.getElementById('homepage-preview');
        if (homepagePreview) {
            const bgColor = document.querySelector('[name="homepage_background_color"]')?.value || '#667eea';
            const gradientColor = document.querySelector('[name="homepage_gradient_color"]')?.value || '';
            const textColor = document.querySelector('[name="homepage_text_color"]')?.value || '#ffffff';
            const title = document.querySelector('[name="homepage_title"]')?.value || 'ENROLL NOW';
            
            if (document.getElementById('homepage_gradient_toggle')?.checked && gradientColor) {
                homepagePreview.style.background = `linear-gradient(135deg, ${bgColor} 0%, ${gradientColor} 100%)`;
            } else {
                homepagePreview.style.background = bgColor;
            }
            homepagePreview.style.color = textColor;
            homepagePreview.textContent = title;
        }
        
        // Navbar preview
        const navbarPreview = document.getElementById('navbar-preview');
        if (navbarPreview) {
            const bgColor = document.querySelector('[name="navbar_background_color"]')?.value || '#f1f1f1';
            const gradientColor = document.querySelector('[name="navbar_gradient_color"]')?.value || '';
            const textColor = document.querySelector('[name="navbar_text_color"]')?.value || '#222222';
            const brandName = document.querySelector('[name="navbar_brand_name"]')?.value || 'Ascendo Review and Training Center';
            
            if (document.getElementById('navbar_gradient_toggle')?.checked && gradientColor) {
                navbarPreview.style.background = `linear-gradient(135deg, ${bgColor} 0%, ${gradientColor} 100%)`;
            } else {
                navbarPreview.style.background = bgColor;
            }
            navbarPreview.style.color = textColor;
            navbarPreview.querySelector('strong').textContent = brandName;
        }
    }
    
    // Image removal functions
    window.removeHomepageImage = function() {
        if (confirm('Are you sure you want to remove the homepage background image?')) {
            // Create a form to submit the removal
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.settings.remove.image") }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            const typeInput = document.createElement('input');
            typeInput.type = 'hidden';
            typeInput.name = 'type';
            typeInput.value = 'homepage';
            
            form.appendChild(csrfToken);
            form.appendChild(typeInput);
            document.body.appendChild(form);
            form.submit();
        }
    };
    
    window.removeGlobalLogo = function() {
        if (confirm('Are you sure you want to remove the global logo?')) {
            // Create a form to submit the removal
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.settings.remove.global.logo") }}';
            
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            
            form.appendChild(csrfToken);
            document.body.appendChild(form);
            form.submit();
        }
    };
    
    // Initialize previews
    updatePreviews();
});
</script>
@endsection
