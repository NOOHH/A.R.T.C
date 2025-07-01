@extends('admin.admin-dashboard-layout')

@section('title', 'Settings')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/admin/admin-settings/admin-settings.css') }}">
@endpush

@section('content')
<div class="main-content-wrapper">
    <div class="settings-container">
        <div class="settings-header">
            <h1>Settings</h1>
        </div>
        
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

        <div class="settings-grid">
            {{-- Global Logo Settings --}}
            <div class="settings-section global-logo-section settings-section-small">
                <h2 class="section-title">🌐 Global Logo</h2>
                
                <form id="globalLogoForm" action="{{ route('admin.settings.global.logo') }}" method="POST" enctype="multipart/form-data" class="settings-form">
                    @csrf
                    
                    <div class="form-group">
                        <label for="global_logo">Upload Global Logo</label>
                        <input type="file" 
                               id="global_logo" 
                               name="global_logo" 
                               accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/svg+xml" 
                               class="form-control">
                        <small class="form-text">This logo will be used across all pages (navbar, login, etc.). Supported formats: JPG, PNG, GIF, WebP, SVG. Max size: 5MB</small>
                    </div>
                    
                    {{-- Current Global Logo Preview --}}
                    @if(isset($settings['global_logo']) && $settings['global_logo'])
                        <div class="current-logo-preview">
                            <label>Current Global Logo:</label>
                            <div class="image-preview-wrapper">
                                <img src="{{ asset('storage/' . $settings['global_logo']) }}" alt="Current Global Logo" class="current-image">
                                <form action="{{ route('admin.settings.remove.global.logo') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" 
                                            class="remove-image-btn-large" 
                                            onclick="return confirm('Are you sure you want to remove this logo?')" 
                                            title="Remove Global Logo">
                                        <i class="fas fa-trash"></i> Remove Logo
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                    
                    <button type="submit" id="updateGlobalLogoBtn" class="btn-primary">Update Global Logo</button>
                </form>
            </div>

            {{-- Homepage Settings --}}
            <div class="settings-section homepage-section">
                <h2 class="section-title">Homepage Customization</h2>
                
                <form action="{{ route('admin.settings.update.homepage') }}" method="POST" enctype="multipart/form-data" class="settings-form">
                    @csrf
                    
                    <div class="form-group">
                        <label for="homepage_title">Homepage Title</label>
                        <input type="text" 
                               id="homepage_title" 
                               name="homepage_title" 
                               value="{{ old('homepage_title', $settings['homepage']['title'] ?? 'ENROLL NOW') }}"
                               placeholder="Enter homepage title">
                    </div>
                    
                    <div class="form-group">
                        <label for="homepage_background_color">Background Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="homepage_background_color" 
                                   name="homepage_background_color" 
                                   value="{{ old('homepage_background_color', $settings['homepage']['background_color'] ?? '#667eea') }}">
                            <input type="text" 
                                   value="{{ old('homepage_background_color', $settings['homepage']['background_color'] ?? '#667eea') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="homepage_gradient_color">Gradient Color (Optional)</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="homepage_gradient_color" 
                                   name="homepage_gradient_color" 
                                   value="{{ old('homepage_gradient_color', $settings['homepage']['gradient_color'] ?? '') }}">
                            <input type="text" 
                                   value="{{ old('homepage_gradient_color', $settings['homepage']['gradient_color'] ?? '') }}"
                                   placeholder="Leave empty for solid color"
                                   readonly>
                        </div>
                        <small class="form-text">Add a second color to create a gradient background. Leave empty for solid color.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="homepage_text_color">Text Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="homepage_text_color" 
                                   name="homepage_text_color" 
                                   value="{{ old('homepage_text_color', $settings['homepage']['text_color'] ?? '#ffffff') }}">
                            <input type="text" 
                                   value="{{ old('homepage_text_color', $settings['homepage']['text_color'] ?? '#ffffff') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="homepage_background_image">Background Image</label>
                        <input type="file" 
                               id="homepage_background_image" 
                               name="homepage_background_image" 
                               accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                        
                        @if(isset($settings['homepage']['background_image']) && $settings['homepage']['background_image'])
                            <img src="{{ asset('storage/' . $settings['homepage']['background_image']) }}" 
                                 alt="Current background" 
                                 class="image-preview">
                            <div class="image-actions">
                                <form action="{{ route('admin.settings.remove.image') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="type" value="homepage">
                                    <button type="submit" class="remove-image-btn-large" onclick="return confirm('Are you sure you want to remove this image?')">
                                        <i class="fas fa-trash"></i> Remove Image
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    
                    <button type="submit" class="btn-primary" style="margin-bottom: 20px;">Update Homepage</button>
                    
                    <div class="preview-section">
                        <div class="preview-title">Preview</div>
                        <div class="homepage-preview" 
                             id="homepage-preview"
                             style="background: {{ isset($settings['homepage']['gradient_color']) && $settings['homepage']['gradient_color'] ? 'linear-gradient(135deg, ' . ($settings['homepage']['background_color'] ?? '#667eea') . ' 0%, ' . $settings['homepage']['gradient_color'] . ' 100%)' : ($settings['homepage']['background_color'] ?? '#667eea') }}; 
                                    color: {{ $settings['homepage']['text_color'] ?? '#ffffff' }};
                                    padding: 30px;
                                    text-align: center;
                                    border-radius: 8px;
                                    font-size: 1.2rem;
                                    font-weight: 600;">
                            {{ $settings['homepage']['title'] ?? 'ENROLL NOW' }}
                        </div>
                    </div>
                </form>
            </div>

        {{-- Additional Settings Grid --}}
        <div class="settings-grid-small">
            {{-- Navbar Settings --}}
            <div class="settings-section-small navbar-section">
                <h2 class="section-title">Navbar Customization</h2>
                
                <form action="{{ route('admin.settings.update.navbar') }}" method="POST" enctype="multipart/form-data" class="settings-form">
                    @csrf
                    
                    <div class="form-group">
                        <label for="navbar_brand_name">Brand Name</label>
                        <input type="text" 
                               id="navbar_brand_name" 
                               name="navbar_brand_name" 
                               value="{{ old('navbar_brand_name', $settings['navbar']['brand_name'] ?? 'Ascendo Review and Training Center') }}"
                               placeholder="Enter brand name">
                    </div>
                    
                    <div class="form-group">
                        <label for="navbar_background_color">Background Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="navbar_background_color" 
                                   name="navbar_background_color" 
                                   value="{{ old('navbar_background_color', $settings['navbar']['background_color'] ?? '#f1f1f1') }}">
                            <input type="text" 
                                   value="{{ old('navbar_background_color', $settings['navbar']['background_color'] ?? '#f1f1f1') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="navbar_gradient_color">Gradient Color (Optional)</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="navbar_gradient_color" 
                                   name="navbar_gradient_color" 
                                   value="{{ old('navbar_gradient_color', $settings['navbar']['gradient_color'] ?? '') }}">
                            <input type="text" 
                                   value="{{ old('navbar_gradient_color', $settings['navbar']['gradient_color'] ?? '') }}"
                                   placeholder="Leave empty for solid color"
                                   readonly>
                        </div>
                        <small class="form-text">Add a second color to create a gradient background. Leave empty for solid color.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="navbar_text_color">Text Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="navbar_text_color" 
                                   name="navbar_text_color" 
                                   value="{{ old('navbar_text_color', $settings['navbar']['text_color'] ?? '#222222') }}">
                            <input type="text" 
                                   value="{{ old('navbar_text_color', $settings['navbar']['text_color'] ?? '#222222') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div style="background: #e3f2fd; padding: 12px; border-radius: 8px; margin: 15px 0; color: #1565c0; font-size: 0.9rem;">
                        <i class="fas fa-info-circle"></i> <strong>Note:</strong> The navbar uses the Global Logo automatically. No separate logo upload needed.
                    </div>
                    
                    <button type="submit" class="btn-primary">Update Navbar</button>
                </form>
            </div>

            {{-- Footer Settings --}}
            <div class="settings-section-small footer-section">
                <h2 class="section-title">Footer Customization</h2>
                
                <form action="{{ route('admin.settings.update.footer') }}" method="POST" class="settings-form">
                    @csrf
                    
                    <div class="form-group">
                        <label for="footer_text">Footer Text</label>
                        <textarea id="footer_text" 
                                  name="footer_text" 
                                  rows="3" 
                                  placeholder="Enter footer text (HTML allowed)"
                                  style="padding: 12px 15px; border: 2px solid #e1e5e9; border-radius: 8px; font-size: 1rem; resize: vertical; width: 100%; box-sizing: border-box;">{{ old('footer_text', $settings['footer']['text'] ?? '© Copyright Ascendo Review and Training Center.<br>All Rights Reserved.') }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="footer_background_color">Background Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="footer_background_color" 
                                   name="footer_background_color" 
                                   value="{{ old('footer_background_color', $settings['footer']['background_color'] ?? '#ffffff') }}">
                            <input type="text" 
                                   value="{{ old('footer_background_color', $settings['footer']['background_color'] ?? '#ffffff') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="footer_gradient_color">Gradient Color (Optional)</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="footer_gradient_color" 
                                   name="footer_gradient_color" 
                                   value="{{ old('footer_gradient_color', $settings['footer']['gradient_color'] ?? '') }}">
                            <input type="text" 
                                   value="{{ old('footer_gradient_color', $settings['footer']['gradient_color'] ?? '') }}"
                                   placeholder="Leave empty for solid color"
                                   readonly>
                        </div>
                        <small class="form-text">Add a second color to create a gradient background. Leave empty for solid color.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="footer_text_color">Text Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="footer_text_color" 
                                   name="footer_text_color" 
                                   value="{{ old('footer_text_color', $settings['footer']['text_color'] ?? '#444444') }}">
                            <input type="text" 
                                   value="{{ old('footer_text_color', $settings['footer']['text_color'] ?? '#444444') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-primary" style="margin-bottom: 20px;">Update Footer</button>
                    
                    <div class="preview-section">
                        <div class="preview-title">Preview</div>
                        <div class="footer-preview" 
                             style="background: {{ isset($settings['footer']['gradient_color']) && $settings['footer']['gradient_color'] ? 'linear-gradient(135deg, ' . ($settings['footer']['background_color'] ?? '#ffffff') . ' 0%, ' . $settings['footer']['gradient_color'] . ' 100%)' : ($settings['footer']['background_color'] ?? '#ffffff') }}; color: {{ $settings['footer']['text_color'] ?? '#444444' }}; padding: 15px; border-radius: 8px; text-align: center;">
                            {!! $settings['footer']['text'] ?? '© Copyright Ascendo Review and Training Center.<br>All Rights Reserved.' !!}
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Program Cards Settings --}}
        <div class="settings-grid">
            <div class="settings-section program-cards-section">
                <h2 class="section-title">Program Cards & Enrollment Customization</h2>
                
                <form action="{{ route('admin.settings.update.program-cards') }}" method="POST" enctype="multipart/form-data" class="settings-form">
                    @csrf
                    
                    <h4 style="margin: 20px 0 15px 0; color: #2c3e50; border-bottom: 2px solid #eee; padding-bottom: 5px;">Program Card Colors</h4>
                    
                    <div class="form-group">
                        <label for="program_card_background_color">Card Background Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="program_card_background_color" 
                                   name="program_card_background_color" 
                                   value="{{ old('program_card_background_color', $settings['program_cards']['background_color'] ?? '#f9f9f9') }}">
                            <input type="text" 
                                   value="{{ old('program_card_background_color', $settings['program_cards']['background_color'] ?? '#f9f9f9') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="program_card_gradient_color">Card Gradient Color (Optional)</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="program_card_gradient_color" 
                                   name="program_card_gradient_color" 
                                   value="{{ old('program_card_gradient_color', $settings['program_cards']['gradient_color'] ?? '') }}">
                            <input type="text" 
                                   value="{{ old('program_card_gradient_color', $settings['program_cards']['gradient_color'] ?? '') }}"
                                   placeholder="Leave empty for solid color"
                                   readonly>
                        </div>
                        <small class="form-text">Add a second color to create a gradient background for cards. Leave empty for solid color.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="program_card_text_color">Text Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="program_card_text_color" 
                                   name="program_card_text_color" 
                                   value="{{ old('program_card_text_color', $settings['program_cards']['text_color'] ?? '#333333') }}">
                            <input type="text" 
                                   value="{{ old('program_card_text_color', $settings['program_cards']['text_color'] ?? '#333333') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="program_card_border_color">Border Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="program_card_border_color" 
                                   name="program_card_border_color" 
                                   value="{{ old('program_card_border_color', $settings['program_cards']['border_color'] ?? '#dddddd') }}">
                            <input type="text" 
                                   value="{{ old('program_card_border_color', $settings['program_cards']['border_color'] ?? '#dddddd') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="program_card_hover_color">Hover Background Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="program_card_hover_color" 
                                   name="program_card_hover_color" 
                                   value="{{ old('program_card_hover_color', $settings['program_cards']['hover_color'] ?? '#1c2951') }}">
                            <input type="text" 
                                   value="{{ old('program_card_hover_color', $settings['program_cards']['hover_color'] ?? '#1c2951') }}"
                                   readonly>
                        </div>
                    </div>

                    <h4 style="margin: 20px 0 15px 0; color: #2c3e50; border-bottom: 2px solid #eee; padding-bottom: 5px;">Enrollment Button Colors</h4>
                    
                    <div class="form-group">
                        <label for="enrollment_button_color">Enrollment Button Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="enrollment_button_color" 
                                   name="enrollment_button_color" 
                                   value="{{ old('enrollment_button_color', $settings['program_cards']['enrollment_button_color'] ?? '#667eea') }}">
                            <input type="text" 
                                   value="{{ old('enrollment_button_color', $settings['program_cards']['enrollment_button_color'] ?? '#667eea') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="enrollment_button_text_color">Button Text Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="enrollment_button_text_color" 
                                   name="enrollment_button_text_color" 
                                   value="{{ old('enrollment_button_text_color', $settings['program_cards']['enrollment_button_text_color'] ?? '#ffffff') }}">
                            <input type="text" 
                                   value="{{ old('enrollment_button_text_color', $settings['program_cards']['enrollment_button_text_color'] ?? '#ffffff') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="enrollment_button_hover_color">Button Hover Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="enrollment_button_hover_color" 
                                   name="enrollment_button_hover_color" 
                                   value="{{ old('enrollment_button_hover_color', $settings['program_cards']['enrollment_button_hover_color'] ?? '#5a67d8') }}">
                            <input type="text" 
                                   value="{{ old('enrollment_button_hover_color', $settings['program_cards']['enrollment_button_hover_color'] ?? '#5a67d8') }}"
                                   readonly>
                        </div>
                    </div>

                    <h4 style="margin: 20px 0 15px 0; color: #2c3e50; border-bottom: 2px solid #eee; padding-bottom: 5px;">Enrollment Page Colors</h4>
                    
                    <div class="form-group">
                        <label for="enrollment_page_background_color">Page Background Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="enrollment_page_background_color" 
                                   name="enrollment_page_background_color" 
                                   value="{{ old('enrollment_page_background_color', $settings['program_cards']['enrollment_page_background_color'] ?? '#f8f9fa') }}">
                            <input type="text" 
                                   value="{{ old('enrollment_page_background_color', $settings['program_cards']['enrollment_page_background_color'] ?? '#f8f9fa') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="enrollment_page_gradient_color">Page Gradient Color (Optional)</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="enrollment_page_gradient_color" 
                                   name="enrollment_page_gradient_color" 
                                   value="{{ old('enrollment_page_gradient_color', $settings['program_cards']['enrollment_page_gradient_color'] ?? '') }}">
                            <input type="text" 
                                   value="{{ old('enrollment_page_gradient_color', $settings['program_cards']['enrollment_page_gradient_color'] ?? '') }}"
                                   placeholder="Leave empty for solid color"
                                   readonly>
                        </div>
                        <small class="form-text">Add a second color to create a gradient background for the enrollment page. Leave empty for solid color.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="enrollment_page_text_color">Page Text Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="enrollment_page_text_color" 
                                   name="enrollment_page_text_color" 
                                   value="{{ old('enrollment_page_text_color', $settings['program_cards']['enrollment_page_text_color'] ?? '#333333') }}">
                            <input type="text" 
                                   value="{{ old('enrollment_page_text_color', $settings['program_cards']['enrollment_page_text_color'] ?? '#333333') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="enrollment_form_background_color">Form Background Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="enrollment_form_background_color" 
                                   name="enrollment_form_background_color" 
                                   value="{{ old('enrollment_form_background_color', $settings['program_cards']['enrollment_form_background_color'] ?? '#ffffff') }}">
                            <input type="text" 
                                   value="{{ old('enrollment_form_background_color', $settings['program_cards']['enrollment_form_background_color'] ?? '#ffffff') }}"
                                   readonly>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="program_cards_background_image">Background Image (Optional)</label>
                        <input type="file" 
                               id="program_cards_background_image" 
                               name="program_cards_background_image" 
                               accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                        
                        @if(isset($settings['program_cards']['background_image']) && $settings['program_cards']['background_image'])
                            <img src="{{ asset('storage/' . $settings['program_cards']['background_image']) }}" 
                                 alt="Current background" 
                                 class="image-preview">
                            <div class="image-actions">
                                <form action="{{ route('admin.settings.remove.image') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="type" value="program_cards">
                                    <button type="submit" class="remove-image-btn-large" onclick="return confirm('Are you sure you want to remove this image?')">
                                        <i class="fas fa-trash"></i> Remove Image
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    
                    <button type="submit" class="btn-primary" style="margin-bottom: 20px;">Update Program Cards & Enrollment</button>
                    
                    <div class="preview-section">
                        <div class="preview-title">Program Card & Enrollment Preview</div>
                        <div class="program-card-preview" 
                             style="background: {{ isset($settings['program_cards']['gradient_color']) && $settings['program_cards']['gradient_color'] ? 'linear-gradient(135deg, ' . ($settings['program_cards']['background_color'] ?? '#f9f9f9') . ' 0%, ' . $settings['program_cards']['gradient_color'] . ' 100%)' : ($settings['program_cards']['background_color'] ?? '#f9f9f9') }}; 
                                    color: {{ $settings['program_cards']['text_color'] ?? '#333333' }}; 
                                    border: 2px solid {{ $settings['program_cards']['border_color'] ?? '#dddddd' }}; 
                                    padding: 20px; 
                                    border-radius: 10px; 
                                    text-align: center;
                                    transition: all 0.3s ease;
                                    cursor: pointer;"
                             onmouseover="this.style.borderColor='{{ $settings['program_cards']['hover_color'] ?? '#1c2951' }}'; this.style.boxShadow='0 4px 12px rgba(28,41,81,0.3)'; this.style.transform='translateY(-2px)';"
                             onmouseout="this.style.borderColor='{{ $settings['program_cards']['border_color'] ?? '#dddddd' }}'; this.style.boxShadow='none'; this.style.transform='translateY(0)';">
                            <strong>Sample Program Card</strong><br>
                            <p style="margin: 10px 0;">Program description and details...</p>
                            <button type="button" style="background: {{ $settings['program_cards']['enrollment_button_color'] ?? '#667eea' }}; 
                                                         color: {{ $settings['program_cards']['enrollment_button_text_color'] ?? '#ffffff' }}; 
                                                         border: none; 
                                                         padding: 10px 20px; 
                                                         border-radius: 5px; 
                                                         cursor: pointer;
                                                         transition: all 0.3s ease;"
                                    onmouseover="this.style.background='{{ $settings['program_cards']['enrollment_button_hover_color'] ?? '#5a67d8' }}'"
                                    onmouseout="this.style.background='{{ $settings['program_cards']['enrollment_button_color'] ?? '#667eea' }}'">
                                Enroll Now
                            </button>
                        </div>
                        <div style="margin-top: 15px; padding: 15px; border-radius: 8px; 
                                   background: {{ isset($settings['program_cards']['enrollment_page_gradient_color']) && $settings['program_cards']['enrollment_page_gradient_color'] ? 'linear-gradient(135deg, ' . ($settings['program_cards']['enrollment_page_background_color'] ?? '#f8f9fa') . ' 0%, ' . $settings['program_cards']['enrollment_page_gradient_color'] . ' 100%)' : ($settings['program_cards']['enrollment_page_background_color'] ?? '#f8f9fa') }};
                                   color: {{ $settings['program_cards']['enrollment_page_text_color'] ?? '#333333' }};">
                            <strong>Enrollment Page Preview</strong><br>
                            <div style="margin-top: 10px; padding: 15px; border-radius: 5px; 
                                       background: {{ $settings['program_cards']['enrollment_form_background_color'] ?? '#ffffff' }};">
                                Sample enrollment form background
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Overall Preview Section --}}
            <div class="settings-section">
                <h2 class="section-title">🎨 Overall Preview</h2>
                
                <div class="preview-section">
                    <div class="preview-title">Site Components Preview</div>
                    
                    {{-- Navbar Preview --}}
                    <div data-navbar-preview 
                         style="background: {{ isset($settings['navbar']['gradient_color']) && $settings['navbar']['gradient_color'] ? 'linear-gradient(135deg, ' . ($settings['navbar']['background_color'] ?? '#f1f1f1') . ' 0%, ' . $settings['navbar']['gradient_color'] . ' 100%)' : ($settings['navbar']['background_color'] ?? '#f1f1f1') }}; 
                               color: {{ $settings['navbar']['text_color'] ?? '#222222' }}; 
                               padding: 15px; 
                               border-radius: 8px; 
                               margin-bottom: 15px; 
                               display: flex; 
                               justify-content: space-between; 
                               align-items: center;">
                        <strong>{{ $settings['navbar']['brand_name'] ?? 'Ascendo Review and Training Center' }}</strong>
                        <span>Home | About | Contact</span>
                    </div>
                    
                    {{-- Content Preview --}}
                    <div style="background: {{ isset($settings['homepage']['gradient_color']) && $settings['homepage']['gradient_color'] ? 'linear-gradient(135deg, ' . ($settings['homepage']['background_color'] ?? '#667eea') . ' 0%, ' . $settings['homepage']['gradient_color'] . ' 100%)' : ($settings['homepage']['background_color'] ?? '#667eea') }}; 
                               color: {{ $settings['homepage']['text_color'] ?? '#ffffff' }}; 
                               padding: 30px; 
                               border-radius: 8px; 
                               margin-bottom: 15px; 
                               text-align: center;">
                        <h3 style="margin: 0; color: inherit;">{{ $settings['homepage']['title'] ?? 'ENROLL NOW' }}</h3>
                    </div>
                    
                    {{-- Cards Preview --}}
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 15px;">
                        <div style="background: {{ isset($settings['program_cards']['gradient_color']) && $settings['program_cards']['gradient_color'] ? 'linear-gradient(135deg, ' . ($settings['program_cards']['background_color'] ?? '#f9f9f9') . ' 0%, ' . $settings['program_cards']['gradient_color'] . ' 100%)' : ($settings['program_cards']['background_color'] ?? '#f9f9f9') }}; 
                                   color: {{ $settings['program_cards']['text_color'] ?? '#333333' }}; 
                                   border: 2px solid {{ $settings['program_cards']['border_color'] ?? '#dddddd' }}; 
                                   padding: 15px; 
                                   border-radius: 8px; 
                                   text-align: center;">
                            <strong>Program 1</strong>
                        </div>
                        <div style="background: {{ isset($settings['program_cards']['gradient_color']) && $settings['program_cards']['gradient_color'] ? 'linear-gradient(135deg, ' . ($settings['program_cards']['background_color'] ?? '#f9f9f9') . ' 0%, ' . $settings['program_cards']['gradient_color'] . ' 100%)' : ($settings['program_cards']['background_color'] ?? '#f9f9f9') }}; 
                                   color: {{ $settings['program_cards']['text_color'] ?? '#333333' }}; 
                                   border: 2px solid {{ $settings['program_cards']['border_color'] ?? '#dddddd' }}; 
                                   padding: 15px; 
                                   border-radius: 8px; 
                                   text-align: center;">
                            <strong>Program 2</strong>
                        </div>
                    </div>
                    
                    {{-- Footer Preview --}}
                    <div style="background: {{ isset($settings['footer']['gradient_color']) && $settings['footer']['gradient_color'] ? 'linear-gradient(135deg, ' . ($settings['footer']['background_color'] ?? '#ffffff') . ' 0%, ' . $settings['footer']['gradient_color'] . ' 100%)' : ($settings['footer']['background_color'] ?? '#ffffff') }}; 
                               color: {{ $settings['footer']['text_color'] ?? '#444444' }}; 
                               padding: 15px; 
                               border-radius: 8px; 
                               text-align: center; 
                               border-top: 1px solid #eee;">
                        {!! $settings['footer']['text'] ?? '© Copyright Ascendo Review and Training Center.<br>All Rights Reserved.' !!}
                    </div>
                </div>
            </div>
        </div>

        {{-- Button Settings --}}
        <div class="settings-grid">
            <div class="settings-section buttons-section">
                <h2 class="section-title">Button Customization</h2>
                
                <form action="{{ route('admin.settings.update.buttons') }}" method="POST" class="settings-form">
                    @csrf
                    
                    <h4 style="margin: 20px 0 15px 0; color: #2c3e50; border-bottom: 2px solid #eee; padding-bottom: 5px;">Primary Buttons</h4>
                    
                    <div class="form-group">
                        <label for="primary_color">Primary Button Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="primary_color" 
                                   name="primary_color" 
                                   value="{{ old('primary_color', $settings['buttons']['primary_color'] ?? '#667eea') }}">
                            <input type="text" 
                                   value="{{ old('primary_color', $settings['buttons']['primary_color'] ?? '#667eea') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="primary_text_color">Primary Button Text Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="primary_text_color" 
                                   name="primary_text_color" 
                                   value="{{ old('primary_text_color', $settings['buttons']['primary_text_color'] ?? '#ffffff') }}">
                            <input type="text" 
                                   value="{{ old('primary_text_color', $settings['buttons']['primary_text_color'] ?? '#ffffff') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="primary_hover_color">Primary Button Hover Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="primary_hover_color" 
                                   name="primary_hover_color" 
                                   value="{{ old('primary_hover_color', $settings['buttons']['primary_hover_color'] ?? '#5a67d8') }}">
                            <input type="text" 
                                   value="{{ old('primary_hover_color', $settings['buttons']['primary_hover_color'] ?? '#5a67d8') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <h4 style="margin: 20px 0 15px 0; color: #2c3e50; border-bottom: 2px solid #eee; padding-bottom: 5px;">Secondary Buttons</h4>
                    
                    <div class="form-group">
                        <label for="secondary_color">Secondary Button Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="secondary_color" 
                                   name="secondary_color" 
                                   value="{{ old('secondary_color', $settings['buttons']['secondary_color'] ?? '#6c757d') }}">
                            <input type="text" 
                                   value="{{ old('secondary_color', $settings['buttons']['secondary_color'] ?? '#6c757d') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="success_color">Success Button Color (Edit/Save)</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="success_color" 
                                   name="success_color" 
                                   value="{{ old('success_color', $settings['buttons']['success_color'] ?? '#28a745') }}">
                            <input type="text" 
                                   value="{{ old('success_color', $settings['buttons']['success_color'] ?? '#28a745') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="danger_color">Danger Button Color (Delete)</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="danger_color" 
                                   name="danger_color" 
                                   value="{{ old('danger_color', $settings['buttons']['danger_color'] ?? '#dc3545') }}">
                            <input type="text" 
                                   value="{{ old('danger_color', $settings['buttons']['danger_color'] ?? '#dc3545') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-primary" style="margin-bottom: 20px;">Update Button Settings</button>
                    
                    <div class="preview-section">
                        <div class="preview-title">Button Preview</div>
                        <div style="display: flex; gap: 10px; flex-wrap: wrap; justify-content: center;">
                            <button type="button" class="btn-preview btn-primary-preview" 
                                    style="background: {{ $settings['buttons']['primary_color'] ?? '#667eea' }}; 
                                           color: {{ $settings['buttons']['primary_text_color'] ?? '#ffffff' }}; 
                                           border: none; padding: 10px 20px; border-radius: 5px;">
                                Primary Button
                            </button>
                            <button type="button" class="btn-preview btn-secondary-preview"
                                    style="background: {{ $settings['buttons']['secondary_color'] ?? '#6c757d' }}; 
                                           color: {{ $settings['buttons']['secondary_text_color'] ?? '#ffffff' }}; 
                                           border: none; padding: 10px 20px; border-radius: 5px;">
                                Secondary Button
                            </button>
                            <button type="button" class="btn-preview btn-success-preview"
                                    style="background: {{ $settings['buttons']['success_color'] ?? '#28a745' }}; 
                                           color: {{ $settings['buttons']['success_text_color'] ?? '#ffffff' }}; 
                                           border: none; padding: 10px 20px; border-radius: 5px;">
                                Success Button
                            </button>
                            <button type="button" class="btn-preview btn-danger-preview"
                                    style="background: {{ $settings['buttons']['danger_color'] ?? '#dc3545' }}; 
                                           color: {{ $settings['buttons']['danger_text_color'] ?? '#ffffff' }}; 
                                           border: none; padding: 10px 20px; border-radius: 5px;">
                                Danger Button
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Login Page Settings --}}
            <div class="settings-section login-section">
                <h2 class="section-title">Login Page Customization</h2>
                
                <form action="{{ route('admin.settings.update.login') }}" method="POST" enctype="multipart/form-data" class="settings-form">
                    @csrf
                    
                    <div class="form-group">
                        <label for="login_background_color">Background Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="login_background_color" 
                                   name="login_background_color" 
                                   value="{{ old('login_background_color', $settings['login']['background_color'] ?? '#f8f9fa') }}">
                            <input type="text" 
                                   value="{{ old('login_background_color', $settings['login']['background_color'] ?? '#f8f9fa') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="login_gradient_color">Background Gradient Color (Optional)</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="login_gradient_color" 
                                   name="login_gradient_color" 
                                   value="{{ old('login_gradient_color', $settings['login']['gradient_color'] ?? '') }}">
                            <input type="text" 
                                   value="{{ old('login_gradient_color', $settings['login']['gradient_color'] ?? '') }}"
                                   placeholder="Leave empty for solid color"
                                   readonly>
                        </div>
                        <small class="form-text">Add a second color to create a gradient background for the login page. Leave empty for solid color.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="login_text_color">Text Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="login_text_color" 
                                   name="login_text_color" 
                                   value="{{ old('login_text_color', $settings['login']['text_color'] ?? '#333333') }}">
                            <input type="text" 
                                   value="{{ old('login_text_color', $settings['login']['text_color'] ?? '#333333') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="login_accent_color">Accent Color (Buttons/Links)</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="login_accent_color" 
                                   name="login_accent_color" 
                                   value="{{ old('login_accent_color', $settings['login']['accent_color'] ?? '#667eea') }}">
                            <input type="text" 
                                   value="{{ old('login_accent_color', $settings['login']['accent_color'] ?? '#667eea') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="login_card_background">Login Card Background</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="login_card_background" 
                                   name="login_card_background" 
                                   value="{{ old('login_card_background', $settings['login']['card_background'] ?? '#ffffff') }}">
                            <input type="text" 
                                   value="{{ old('login_card_background', $settings['login']['card_background'] ?? '#ffffff') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="login_background_image">Background Image</label>
                        <input type="file" 
                               id="login_background_image" 
                               name="login_background_image" 
                               accept="image/jpeg,image/jpg,image/gif,image/webp">
                        
                        @if(isset($settings['login']['background_image']) && $settings['login']['background_image'])
                            <img src="{{ asset('storage/' . $settings['login']['background_image']) }}" 
                                 alt="Current login background" 
                                 class="image-preview">
                            <div class="image-actions">
                                <form action="{{ route('admin.settings.remove.image') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <input type="hidden" name="type" value="login">
                                    <button type="submit" class="remove-image-btn-large" onclick="return confirm('Are you sure you want to remove this image?')">
                                        <i class="fas fa-trash"></i> Remove Image
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                    
                    <div class="form-group">
                        <label for="login_illustration">Study Illustration</label>
                        <input type="file" 
                               id="login_illustration" 
                               name="login_illustration" 
                               accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/svg+xml"
                               class="form-control">
                        <small class="form-text">Upload a custom study illustration for the login page background. Supported formats: JPG, PNG, GIF, WebP, SVG. Max size: 5MB</small>
                        
                        @if(isset($settings['login']['login_illustration']) && $settings['login']['login_illustration'])
                            <div class="current-logo-preview">
                                <label>Current Study Illustration (Background):</label>
                                <div class="image-preview-wrapper">
                                    <img src="{{ asset('storage/' . $settings['login']['login_illustration']) }}" 
                                         alt="Current study illustration" 
                                         class="current-image">
                                    <form action="{{ route('admin.settings.remove.login.illustration') }}" method="POST" style="display: inline;">
                                        @csrf
                                        <button type="submit" class="remove-image-btn-large" onclick="return confirm('Are you sure you want to remove this illustration?')" title="Remove Study Illustration">
                                            <i class="fas fa-trash"></i> Remove Illustration
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <button type="submit" class="btn-primary" style="margin-bottom: 20px;">Update Login Page</button>
                    
                    <div class="preview-section">
                        <div class="preview-title">Login Preview</div>
                        <div style="background: {{ $settings['login']['card_background'] ?? '#ffffff' }}; 
                                   color: {{ $settings['login']['text_color'] ?? '#333333' }}; 
                                   padding: 20px; 
                                   border-radius: 10px; 
                                   border: 1px solid #ddd;
                                   max-width: 300px;
                                   margin: 0 auto;">
                            <h4 style="text-align: center; margin-bottom: 15px; color: inherit;">Login</h4>
                            <div style="margin-bottom: 10px;">
                                <input type="text" placeholder="Email" style="width: 100%; padding: 8px; border: 1px solid {{ $settings['login']['input_border_color'] ?? '#dee2e6' }}; border-radius: 4px; box-sizing: border-box;">
                            </div>
                            <div style="margin-bottom: 15px;">
                                <input type="password" placeholder="Password" style="width: 100%; padding: 8px; border: 1px solid {{ $settings['login']['input_border_color'] ?? '#dee2e6' }}; border-radius: 4px; box-sizing: border-box;">
                            </div>
                            <button type="button" style="width: 100%; padding: 10px; background: {{ $settings['login']['accent_color'] ?? '#667eea' }}; color: #ffffff; border: none; border-radius: 4px;">
                                Login
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // File validation for image uploads
    function validateImageFile(input) {
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'image/svg+xml'];
        const maxSize = 5 * 1024 * 1024; // 5MB
        
        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Check file type
            if (!allowedTypes.includes(file.type)) {
                alert('Please select a valid image file (JPG, PNG, GIF, WebP, or SVG).');
                input.value = '';
                return false;
            }
            
            // Check file size
            if (file.size > maxSize) {
                alert('File size must be less than 5MB.');
                input.value = '';
                return false;
            }
            
            return true;
        }
    }
    
    // Add validation to all file inputs
    const fileInputs = document.querySelectorAll('input[type="file"]');
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            validateImageFile(this);
        });
    });

    // Global Logo Form handling
    const globalLogoForm = document.getElementById('globalLogoForm');
    const globalLogoInput = document.getElementById('global_logo');
    const updateGlobalLogoBtn = document.getElementById('updateGlobalLogoBtn');
    
    if (globalLogoForm && globalLogoInput && updateGlobalLogoBtn) {
        // Debug logging
        console.log('Global Logo Form initialized');
        
        // Handle form submission with validation
        globalLogoForm.addEventListener('submit', function(e) {
            console.log('Global logo form submitted');
            
            if (!globalLogoInput.files || globalLogoInput.files.length === 0) {
                e.preventDefault();
                alert('Please select a logo file to upload.');
                console.log('No file selected');
                return false;
            }
            
            console.log('File selected:', globalLogoInput.files[0].name);
            
            // Validate file
            if (!validateImageFile(globalLogoInput)) {
                e.preventDefault();
                console.log('File validation failed');
                return false;
            }
            
            console.log('File validation passed, submitting form');
            
            // Show loading state
            updateGlobalLogoBtn.disabled = true;
            updateGlobalLogoBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
            
            return true;
        });
        
        // File selection feedback
        globalLogoInput.addEventListener('change', function() {
            if (this.files && this.files.length > 0) {
                console.log('File selected for global logo:', this.files[0].name);
            }
        });
    }

    // Color picker sync
    function setupColorSync(colorInput, textInput) {
        const colorPicker = document.getElementById(colorInput);
        const textField = document.querySelector(`input[name="${colorInput}"] + input[type="text"]`);
        
        if (colorPicker && textField) {
            colorPicker.addEventListener('input', function() {
                textField.value = this.value;
                updatePreview();
            });
            
            textField.addEventListener('input', function() {
                if (/^#[0-9A-F]{6}$/i.test(this.value)) {
                    colorPicker.value = this.value;
                    updatePreview();
                }
            });
        }
    }
    
    // Homepage title input
    const homepageTitleInput = document.getElementById('homepage_title');
    if (homepageTitleInput) {
        homepageTitleInput.addEventListener('input', updatePreview);
    }
    
    // Navbar brand name input
    const navbarBrandInput = document.getElementById('navbar_brand_name');
    if (navbarBrandInput) {
        navbarBrandInput.addEventListener('input', updatePreview);
    }
    
    // Footer text input
    const footerTextInput = document.getElementById('footer_text');
    if (footerTextInput) {
        footerTextInput.addEventListener('input', updatePreview);
    }
    
    // Add input event listeners for all color inputs including gradient
    const colorInputs = [
        'homepage_background_color',
        'homepage_gradient_color',
        'homepage_text_color',
        'navbar_background_color',
        'navbar_gradient_color',
        'navbar_text_color',
        'footer_background_color',
        'footer_gradient_color',
        'footer_text_color',
        'program_card_background_color',
        'program_card_gradient_color',
        'program_card_text_color',
        'program_card_border_color',
        'program_card_hover_color',
        'enrollment_button_color',
        'enrollment_button_text_color',
        'enrollment_button_hover_color',
        'enrollment_page_background_color',
        'enrollment_page_gradient_color',
        'enrollment_page_text_color',
        'enrollment_form_background_color',
        'primary_color',
        'primary_text_color',
        'primary_hover_color',
        'secondary_color',
        'success_color',
        'danger_color'
    ];
    
    colorInputs.forEach(inputId => {
        const colorInput = document.getElementById(inputId);
        if (colorInput) {
            colorInput.addEventListener('input', updatePreview);
        }
    });
    
    // Add input event listeners for all login color inputs
    const loginColorInputs = [
        'login_background_color',
        'login_gradient_color',
        'login_text_color', 
        'login_accent_color',
        'login_card_background'
    ];
    
    loginColorInputs.forEach(inputId => {
        const colorInput = document.getElementById(inputId);
        if (colorInput) {
            colorInput.addEventListener('input', updatePreview);
        }
    });
    
    // Setup color syncing
    setupColorSync('homepage_background_color');
    setupColorSync('homepage_gradient_color');
    setupColorSync('homepage_text_color');
    setupColorSync('navbar_background_color');
    setupColorSync('navbar_gradient_color');
    setupColorSync('navbar_text_color');
    setupColorSync('footer_background_color');
    setupColorSync('footer_gradient_color');
    setupColorSync('footer_text_color');
    setupColorSync('program_card_background_color');
    setupColorSync('program_card_gradient_color');
    setupColorSync('program_card_text_color');
    setupColorSync('program_card_border_color');
    setupColorSync('program_card_hover_color');
    setupColorSync('enrollment_button_color');
    setupColorSync('enrollment_button_text_color');
    setupColorSync('enrollment_button_hover_color');
    setupColorSync('enrollment_page_background_color');
    setupColorSync('enrollment_page_gradient_color');
    setupColorSync('enrollment_page_text_color');
    setupColorSync('enrollment_form_background_color');
    setupColorSync('primary_color');
    setupColorSync('primary_text_color');
    setupColorSync('primary_hover_color');
    setupColorSync('secondary_color');
    setupColorSync('success_color');
    setupColorSync('danger_color');
    setupColorSync('login_background_color');
    setupColorSync('login_gradient_color');
    setupColorSync('login_text_color');
    setupColorSync('login_accent_color');
    setupColorSync('login_card_background');
    
    function updatePreview() {
        // Update homepage preview with gradient support
        const homepagePreview = document.getElementById('homepage-preview');
        if (homepagePreview) {
            const bgColor = document.getElementById('homepage_background_color')?.value || '#667eea';
            const gradientColor = document.getElementById('homepage_gradient_color')?.value || '';
            const textColor = document.getElementById('homepage_text_color')?.value || '#ffffff';
            const title = document.getElementById('homepage_title')?.value || 'ENROLL NOW';
            
            // Set background with gradient if gradient color is provided
            if (gradientColor && gradientColor !== bgColor) {
                homepagePreview.style.background = `linear-gradient(135deg, ${bgColor} 0%, ${gradientColor} 100%)`;
            } else {
                homepagePreview.style.background = bgColor;
            }
            
            homepagePreview.style.color = textColor;
            homepagePreview.textContent = title;
        }
        
        // Update enrollment preview
        const enrollmentPreview = document.querySelector('.enrollment-preview');
        if (enrollmentPreview) {
            const bgColor = document.getElementById('enrollment_background_color')?.value || '#f8f9fa';
            const textColor = document.getElementById('enrollment_text_color')?.value || '#333333';
            const accentColor = document.getElementById('enrollment_accent_color')?.value || '#667eea';
            
            enrollmentPreview.style.setProperty('--bg-color', bgColor);
            enrollmentPreview.style.setProperty('--text-color', textColor);
            enrollmentPreview.style.setProperty('--accent-color', accentColor);
        }
        
        // Update navbar preview (in overall preview section)
        const navbarPreviewElements = document.querySelectorAll('.navbar-preview, [data-navbar-preview]');
        if (navbarPreviewElements.length > 0) {
            const bgColor = document.getElementById('navbar_background_color')?.value || '#f1f1f1';
            const gradientColor = document.getElementById('navbar_gradient_color')?.value || '';
            const textColor = document.getElementById('navbar_text_color')?.value || '#222222';
            const brandName = document.getElementById('navbar_brand_name')?.value || 'Ascendo Review and Training Center';
            
            navbarPreviewElements.forEach(element => {
                if (gradientColor) {
                    element.style.background = `linear-gradient(135deg, ${bgColor} 0%, ${gradientColor} 100%)`;
                } else {
                    element.style.background = bgColor;
                }
                element.style.color = textColor;
                const brandElement = element.querySelector('strong');
                if (brandElement) {
                    brandElement.textContent = brandName;
                }
            });
        }
        
        // Update footer preview
        const footerPreview = document.querySelector('.footer-preview');
        if (footerPreview) {
            const bgColor = document.getElementById('footer_background_color')?.value || '#ffffff';
            const gradientColor = document.getElementById('footer_gradient_color')?.value || '';
            const textColor = document.getElementById('footer_text_color')?.value || '#444444';
            const footerText = document.getElementById('footer_text')?.value || '© Copyright Ascendo Review and Training Center.<br>All Rights Reserved.';
            
            if (gradientColor) {
                footerPreview.style.setProperty('background', `linear-gradient(135deg, ${bgColor} 0%, ${gradientColor} 100%)`);
            } else {
                footerPreview.style.setProperty('background', bgColor);
            }
            footerPreview.style.setProperty('color', textColor);
            footerPreview.innerHTML = footerText;
        }
        
        // Update program card preview
        const programCardPreview = document.querySelector('.program-card-preview');
        if (programCardPreview) {
            const bgColor = document.getElementById('program_card_background_color')?.value || '#f9f9f9';
            const gradientColor = document.getElementById('program_card_gradient_color')?.value || '';
            const textColor = document.getElementById('program_card_text_color')?.value || '#333333';
            const borderColor = document.getElementById('program_card_border_color')?.value || '#dddddd';
            const hoverColor = document.getElementById('program_card_hover_color')?.value || '#1c2951';
            
            if (gradientColor) {
                programCardPreview.style.background = `linear-gradient(135deg, ${bgColor} 0%, ${gradientColor} 100%)`;
            } else {
                programCardPreview.style.background = bgColor;
            }
            programCardPreview.style.color = textColor;
            programCardPreview.style.borderColor = borderColor;
            
            // Update hover styles with new behavior (border and shadow instead of background)
            programCardPreview.onmouseover = function() {
                this.style.borderColor = hoverColor;
                this.style.boxShadow = '0 4px 12px rgba(' + hexToRgb(hoverColor) + ', 0.3)';
                this.style.transform = 'translateY(-2px)';
            }
            programCardPreview.onmouseout = function() {
                this.style.borderColor = borderColor;
                this.style.boxShadow = 'none';
                this.style.transform = 'translateY(0)';
            }
        }
        
        // Update login preview - FIXED VERSION
        const loginPreviewSection = document.querySelector('.login-section .preview-section');
        if (loginPreviewSection) {
            const loginPreview = loginPreviewSection.querySelector('div[style*="background:"]');
            if (loginPreview) {
                const bgColor = document.getElementById('login_background_color')?.value || '#f8f9fa';
                const cardBg = document.getElementById('login_card_background')?.value || '#ffffff';
                const textColor = document.getElementById('login_text_color')?.value || '#333333';
                const accentColor = document.getElementById('login_accent_color')?.value || '#667eea';
                
                // Update the main login preview background
                loginPreview.style.background = cardBg;
                loginPreview.style.color = textColor;
                
                // Update the login button in preview
                const loginButton = loginPreview.querySelector('button');
                if (loginButton) {
                    loginButton.style.background = accentColor;
                    loginButton.style.color = '#ffffff';
                }
                
                // Update h4 color
                const heading = loginPreview.querySelector('h4');
                if (heading) {
                    heading.style.color = textColor;
                }
                
                // Update input border colors
                const inputs = loginPreview.querySelectorAll('input');
                inputs.forEach(input => {
                    input.style.borderColor = '#dee2e6';
                });
            }
        }
        
        // Update button previews
        const primaryPreview = document.querySelector('.btn-primary-preview');
        if (primaryPreview) {
            const primaryColor = document.getElementById('primary_color')?.value || '#667eea';
            const primaryTextColor = document.getElementById('primary_text_color')?.value || '#ffffff';
            primaryPreview.style.background = primaryColor;
            primaryPreview.style.color = primaryTextColor;
        }
        
        const secondaryPreview = document.querySelector('.btn-secondary-preview');
        if (secondaryPreview) {
            const secondaryColor = document.getElementById('secondary_color')?.value || '#6c757d';
            secondaryPreview.style.background = secondaryColor;
            secondaryPreview.style.color = '#ffffff';
        }
        
        const successPreview = document.querySelector('.btn-success-preview');
        if (successPreview) {
            const successColor = document.getElementById('success_color')?.value || '#28a745';
            successPreview.style.background = successColor;
            successPreview.style.color = '#ffffff';
        }
        
        const dangerPreview = document.querySelector('.btn-danger-preview');
        if (dangerPreview) {
            const dangerColor = document.getElementById('danger_color')?.value || '#dc3545';
            dangerPreview.style.background = dangerColor;
            dangerPreview.style.color = '#ffffff';
        }
    }
        
    // Helper function to convert hex to rgb
    function hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? 
            parseInt(result[1], 16) + ', ' + parseInt(result[2], 16) + ', ' + parseInt(result[3], 16) : 
            '28, 41, 81';
    }
    
    // Image removal functions with improved UX
    window.removeImage = function(type, elementId) {
        if (confirm('Are you sure you want to remove this image?')) {
            const imageElement = document.getElementById(elementId);
            if (imageElement) {
                imageElement.parentElement.style.display = 'none';
            }
            
            // Create hidden input to mark for removal
            const form = document.querySelector(`.${type}-section form`);
            if (form) {
                const removeInput = document.createElement('input');
                removeInput.type = 'hidden';
                removeInput.name = 'remove_background_image';
                removeInput.value = '1';
                form.appendChild(removeInput);
            }
            
            // Update preview to reflect removal
            updatePreview();
        }
    };
    
    // Initialize preview updates
    updatePreview();
});
</script>
@endpush
