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
            <h1>Website Settings</h1>
            <p>Customize your website's appearance and content</p>
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
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Global Logo Settings --}}
        <div class="settings-grid-small">
            <div class="settings-section global-logo-section settings-section-small">
                <h2 class="section-title">🌐 Global Logo</h2>
                
                <form id="globalLogoForm" action="{{ route('admin.settings.global.logo') }}" method="POST" enctype="multipart/form-data" class="settings-form">
                    @csrf
                    
                    <div class="form-group">
                        <label for="global_logo">Upload Global Logo</label>
                        <input type="file" 
                               id="global_logo" 
                               name="global_logo" 
                               accept="image/jpeg,image/jpg,image/png,image/gif,image/webp,image/svg+xml">
                        <small class="form-text">This logo will appear in the navigation bar and throughout your website. Recommended: PNG or SVG format, max 5MB</small>
                    </div>
                    
                    @if(isset($settings['global_logo']) && $settings['global_logo'])
                        <div class="current-logo-preview">
                            <label>Current Global Logo:</label>
                            <div class="logo-preview-wrapper">
                                <img src="{{ asset('storage/' . $settings['global_logo']) }}" 
                                     alt="Current global logo" 
                                     class="current-logo-small">
                                <form action="{{ route('admin.settings.remove.global-logo') }}" method="POST" style="display: inline;">
                                    @csrf
                                    <button type="submit" class="remove-image-btn-small" onclick="return confirm('Are you sure you want to remove this logo?')" title="Remove Global Logo">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endif
                    
                    <button type="submit" id="updateGlobalLogoBtn" class="btn-primary">Update Global Logo</button>
                </form>
            </div>
        </div>

        {{-- Homepage Settings --}}
        <div class="settings-grid">
            <div class="settings-section homepage-section">
                <h2 class="section-title">🏠 Homepage Customization</h2>
                
                <form action="{{ route('admin.settings.update.homepage') }}" method="POST" enctype="multipart/form-data" class="settings-form">
                    @csrf
                    
                    <div class="form-group">
                        <label for="homepage_title">Hero Title</label>
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
                        <label for="homepage_background_image">Background Image (Optional)</label>
                        <input type="file" 
                               id="homepage_background_image" 
                               name="homepage_background_image" 
                               accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                        
                        @if(isset($settings['homepage']['background_image']) && $settings['homepage']['background_image'])
                            <img src="{{ asset('storage/' . $settings['homepage']['background_image']) }}" 
                                 alt="Current homepage background" 
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
                    
                    <button type="submit" class="btn-primary">Update Homepage</button>
                    
                    <div class="preview-section">
                        <div class="preview-title">Homepage Preview</div>
                        <div id="homepage-preview" 
                             style="background: {{ isset($settings['homepage']['gradient_color']) && $settings['homepage']['gradient_color'] ? 'linear-gradient(135deg, ' . ($settings['homepage']['background_color'] ?? '#667eea') . ' 0%, ' . $settings['homepage']['gradient_color'] . ' 100%)' : ($settings['homepage']['background_color'] ?? '#667eea') }}; 
                                    color: {{ $settings['homepage']['text_color'] ?? '#ffffff' }}; 
                                    padding: 30px; 
                                    border-radius: 8px; 
                                    text-align: center;">
                            <h3 style="margin: 0; color: inherit;">{{ $settings['homepage']['title'] ?? 'ENROLL NOW' }}</h3>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Navigation Settings --}}
            <div class="settings-section navbar-section">
                <h2 class="section-title">🧭 Navigation Bar</h2>
                
                <form action="{{ route('admin.settings.update.navbar') }}" method="POST" class="settings-form">
                    @csrf
                    
                    <div class="form-group">
                        <label for="navbar_brand_name">Brand Name</label>
                        <input type="text" 
                               id="navbar_brand_name" 
                               name="navbar_brand_name" 
                               value="{{ old('navbar_brand_name', $settings['navbar']['brand_name'] ?? 'Ascendo Review and Training Center') }}">
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
                                   readonly>
                        </div>
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
                        <i class="fas fa-info-circle"></i> <strong>Note:</strong> The navbar uses the Global Logo automatically. No separate logo upload is needed here.
                    </div>
                    
                    <button type="submit" class="btn-primary">Update Navigation</button>
                </form>
            </div>
        </div>

        {{-- Program Cards & Enrollment Settings --}}
        <div class="settings-grid">
            {{-- Program Cards --}}
            <div class="settings-section program-cards-section">
                <h2 class="section-title">📋 Program Cards</h2>
                
                <form action="{{ route('admin.settings.update.program-cards') }}" method="POST" enctype="multipart/form-data" class="settings-form">
                    @csrf
                    
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
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="program_card_text_color">Card Text Color</label>
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
                        <label for="program_card_border_color">Card Border Color</label>
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
                        <label for="program_cards_background_image">Section Background Image (Optional)</label>
                        <input type="file" 
                               id="program_cards_background_image" 
                               name="program_cards_background_image" 
                               accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                        
                        @if(isset($settings['program_cards']['background_image']) && $settings['program_cards']['background_image'])
                            <img src="{{ asset('storage/' . $settings['program_cards']['background_image']) }}" 
                                 alt="Current program cards background" 
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
                    
                    <button type="submit" class="btn-primary">Update Program Cards</button>
                    
                    <div class="preview-section">
                        <div class="preview-title">Program Card Preview</div>
                        <div class="program-card-preview" 
                             style="background: {{ isset($settings['program_cards']['gradient_color']) && $settings['program_cards']['gradient_color'] ? 'linear-gradient(135deg, ' . ($settings['program_cards']['background_color'] ?? '#f9f9f9') . ' 0%, ' . $settings['program_cards']['gradient_color'] . ' 100%)' : ($settings['program_cards']['background_color'] ?? '#f9f9f9') }}; 
                                    color: {{ $settings['program_cards']['text_color'] ?? '#333333' }}; 
                                    border: 2px solid {{ $settings['program_cards']['border_color'] ?? '#dddddd' }}; 
                                    padding: 20px; 
                                    border-radius: 10px; 
                                    text-align: center;">
                            <strong>Sample Program Card</strong><br>
                            <p style="margin: 10px 0;">Program description and details...</p>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Enrollment Settings --}}
            <div class="settings-section enrollment-section">
                <h2 class="section-title">🎓 Enrollment Settings</h2>
                
                <form action="{{ route('admin.settings.update.enrollment') }}" method="POST" class="settings-form">
                    @csrf
                    
                    <h4 class="subsection-title">Enrollment Buttons</h4>
                    
                    <div class="form-group">
                        <label for="enrollment_button_color">Button Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="enrollment_button_color" 
                                   name="enrollment_button_color" 
                                   value="{{ old('enrollment_button_color', $settings['enrollment']['button_color'] ?? '#667eea') }}">
                            <input type="text" 
                                   value="{{ old('enrollment_button_color', $settings['enrollment']['button_color'] ?? '#667eea') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="enrollment_button_text_color">Button Text Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="enrollment_button_text_color" 
                                   name="enrollment_button_text_color" 
                                   value="{{ old('enrollment_button_text_color', $settings['enrollment']['button_text_color'] ?? '#ffffff') }}">
                            <input type="text" 
                                   value="{{ old('enrollment_button_text_color', $settings['enrollment']['button_text_color'] ?? '#ffffff') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <h4 class="subsection-title">Enrollment Page</h4>
                    
                    <div class="form-group">
                        <label for="enrollment_page_background_color">Page Background Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="enrollment_page_background_color" 
                                   name="enrollment_page_background_color" 
                                   value="{{ old('enrollment_page_background_color', $settings['enrollment']['page_background_color'] ?? '#f8f9fa') }}">
                            <input type="text" 
                                   value="{{ old('enrollment_page_background_color', $settings['enrollment']['page_background_color'] ?? '#f8f9fa') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="enrollment_page_gradient_color">Page Gradient Color (Optional)</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="enrollment_page_gradient_color" 
                                   name="enrollment_page_gradient_color" 
                                   value="{{ old('enrollment_page_gradient_color', $settings['enrollment']['page_gradient_color'] ?? '') }}">
                            <input type="text" 
                                   value="{{ old('enrollment_page_gradient_color', $settings['enrollment']['page_gradient_color'] ?? '') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="enrollment_page_text_color">Page Text Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="enrollment_page_text_color" 
                                   name="enrollment_page_text_color" 
                                   value="{{ old('enrollment_page_text_color', $settings['enrollment']['page_text_color'] ?? '#333333') }}">
                            <input type="text" 
                                   value="{{ old('enrollment_page_text_color', $settings['enrollment']['page_text_color'] ?? '#333333') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="enrollment_form_background_color">Form Background Color</label>
                        <div class="color-input-group">
                            <input type="color" 
                                   id="enrollment_form_background_color" 
                                   name="enrollment_form_background_color" 
                                   value="{{ old('enrollment_form_background_color', $settings['enrollment']['form_background_color'] ?? '#ffffff') }}">
                            <input type="text" 
                                   value="{{ old('enrollment_form_background_color', $settings['enrollment']['form_background_color'] ?? '#ffffff') }}"
                                   readonly>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn-primary">Update Enrollment Settings</button>
                    
                    <div class="preview-section">
                        <div class="preview-title">Enrollment Preview</div>
                        <div id="enrollment-preview" 
                             style="background: {{ isset($settings['enrollment']['page_gradient_color']) && $settings['enrollment']['page_gradient_color'] ? 'linear-gradient(135deg, ' . ($settings['enrollment']['page_background_color'] ?? '#f8f9fa') . ' 0%, ' . $settings['enrollment']['page_gradient_color'] . ' 100%)' : ($settings['enrollment']['page_background_color'] ?? '#f8f9fa') }};
                                    color: {{ $settings['enrollment']['page_text_color'] ?? '#333333' }}; 
                                    padding: 20px; 
                                    border-radius: 8px; 
                                    text-align: center;">
                            <strong>Enrollment Page Preview</strong><br>
                            <div style="margin-top: 10px; padding: 15px; border-radius: 5px; 
                                       background: {{ $settings['enrollment']['form_background_color'] ?? '#ffffff' }};">
                                <small>Sample enrollment form background</small>
                            </div>
                            <button type="button" style="background: {{ $settings['enrollment']['button_color'] ?? '#667eea' }}; 
                                                         color: {{ $settings['enrollment']['button_text_color'] ?? '#ffffff' }}; 
                                                         border: none; 
                                                         padding: 10px 20px; 
                                                         border-radius: 5px; 
                                                         margin-top: 10px;">
                                Enroll Now
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Footer and Other Settings --}}
        <div class="settings-grid">
            {{-- Footer Settings --}}
            <div class="settings-section footer-section">
                <h2 class="section-title">📄 Footer</h2>
                
                <form action="{{ route('admin.settings.update.footer') }}" method="POST" class="settings-form">
                    @csrf
                    
                    <div class="form-group">
                        <label for="footer_text">Footer Text</label>
                        <textarea id="footer_text" 
                                  name="footer_text" 
                                  rows="3">{{ old('footer_text', $settings['footer']['text'] ?? '© Copyright Ascendo Review and Training Center.<br>All Rights Reserved.') }}</textarea>
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
                                   readonly>
                        </div>
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
                    
                    <button type="submit" class="btn-primary">Update Footer</button>
                    
                    <div class="preview-section">
                        <div class="preview-title">Footer Preview</div>
                        <div class="footer-preview" 
                             style="background: {{ isset($settings['footer']['gradient_color']) && $settings['footer']['gradient_color'] ? 'linear-gradient(135deg, ' . ($settings['footer']['background_color'] ?? '#ffffff') . ' 0%, ' . $settings['footer']['gradient_color'] . ' 100%)' : ($settings['footer']['background_color'] ?? '#ffffff') }}; 
                                    color: {{ $settings['footer']['text_color'] ?? '#444444' }}; 
                                    padding: 15px; 
                                    border-radius: 8px; 
                                    text-align: center; 
                                    border-top: 1px solid #eee;">
                            {!! $settings['footer']['text'] ?? '© Copyright Ascendo Review and Training Center.<br>All Rights Reserved.' !!}
                        </div>
                    </div>
                </form>
            </div>

            {{-- Login Page Settings --}}
            <div class="settings-section login-section">
                <h2 class="section-title">🔐 Login Page</h2>
                
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
                        <label for="login_background_image">Background Image (Optional)</label>
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
                    
                    <button type="submit" class="btn-primary">Update Login Page</button>
                </form>
            </div>
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
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Real-time preview updates
    function updatePreview() {
        // Update homepage preview
        const homepagePreview = document.getElementById('homepage-preview');
        if (homepagePreview) {
            const bgColor = document.getElementById('homepage_background_color')?.value || '#667eea';
            const gradientColor = document.getElementById('homepage_gradient_color')?.value || '';
            const textColor = document.getElementById('homepage_text_color')?.value || '#ffffff';
            const title = document.getElementById('homepage_title')?.value || 'ENROLL NOW';
            
            if (gradientColor && gradientColor !== bgColor) {
                homepagePreview.style.background = `linear-gradient(135deg, ${bgColor} 0%, ${gradientColor} 100%)`;
            } else {
                homepagePreview.style.background = bgColor;
            }
            
            homepagePreview.style.color = textColor;
            homepagePreview.querySelector('h3').textContent = title;
        }

        // Update enrollment preview
        const enrollmentPreview = document.getElementById('enrollment-preview');
        if (enrollmentPreview) {
            const bgColor = document.getElementById('enrollment_page_background_color')?.value || '#f8f9fa';
            const gradientColor = document.getElementById('enrollment_page_gradient_color')?.value || '';
            const textColor = document.getElementById('enrollment_page_text_color')?.value || '#333333';
            const formBgColor = document.getElementById('enrollment_form_background_color')?.value || '#ffffff';
            const buttonColor = document.getElementById('enrollment_button_color')?.value || '#667eea';
            const buttonTextColor = document.getElementById('enrollment_button_text_color')?.value || '#ffffff';
            
            if (gradientColor && gradientColor !== bgColor) {
                enrollmentPreview.style.background = `linear-gradient(135deg, ${bgColor} 0%, ${gradientColor} 100%)`;
            } else {
                enrollmentPreview.style.background = bgColor;
            }
            
            enrollmentPreview.style.color = textColor;
            
            const formDiv = enrollmentPreview.querySelector('div');
            if (formDiv) {
                formDiv.style.background = formBgColor;
            }
            
            const button = enrollmentPreview.querySelector('button');
            if (button) {
                button.style.background = buttonColor;
                button.style.color = buttonTextColor;
            }
        }

        // Update navbar preview
        const navbarPreview = document.querySelector('[data-navbar-preview]');
        if (navbarPreview) {
            const bgColor = document.getElementById('navbar_background_color')?.value || '#f1f1f1';
            const gradientColor = document.getElementById('navbar_gradient_color')?.value || '';
            const textColor = document.getElementById('navbar_text_color')?.value || '#222222';
            const brandName = document.getElementById('navbar_brand_name')?.value || 'Ascendo Review and Training Center';
            
            if (gradientColor) {
                navbarPreview.style.background = `linear-gradient(135deg, ${bgColor} 0%, ${gradientColor} 100%)`;
            } else {
                navbarPreview.style.background = bgColor;
            }
            navbarPreview.style.color = textColor;
            const brandElement = navbarPreview.querySelector('strong');
            if (brandElement) {
                brandElement.textContent = brandName;
            }
        }

        // Update footer preview
        const footerPreview = document.querySelector('.footer-preview');
        if (footerPreview) {
            const bgColor = document.getElementById('footer_background_color')?.value || '#ffffff';
            const gradientColor = document.getElementById('footer_gradient_color')?.value || '';
            const textColor = document.getElementById('footer_text_color')?.value || '#444444';
            const footerText = document.getElementById('footer_text')?.value || '© Copyright Ascendo Review and Training Center.<br>All Rights Reserved.';
            
            if (gradientColor) {
                footerPreview.style.background = `linear-gradient(135deg, ${bgColor} 0%, ${gradientColor} 100%)`;
            } else {
                footerPreview.style.background = bgColor;
            }
            footerPreview.style.color = textColor;
            footerPreview.innerHTML = footerText;
        }

        // Update program card preview
        const programCardPreview = document.querySelector('.program-card-preview');
        if (programCardPreview) {
            const bgColor = document.getElementById('program_card_background_color')?.value || '#f9f9f9';
            const gradientColor = document.getElementById('program_card_gradient_color')?.value || '';
            const textColor = document.getElementById('program_card_text_color')?.value || '#333333';
            const borderColor = document.getElementById('program_card_border_color')?.value || '#dddddd';
            
            if (gradientColor) {
                programCardPreview.style.background = `linear-gradient(135deg, ${bgColor} 0%, ${gradientColor} 100%)`;
            } else {
                programCardPreview.style.background = bgColor;
            }
            programCardPreview.style.color = textColor;
            programCardPreview.style.borderColor = borderColor;
        }
    }

    // Color picker sync
    function setupColorSync(colorInputId) {
        const colorPicker = document.getElementById(colorInputId);
        const textField = colorPicker?.parentElement.querySelector('input[type="text"]');
        
        if (colorPicker && textField) {
            colorPicker.addEventListener('input', function() {
                textField.value = this.value;
                updatePreview();
            });
        }
    }

    // Text inputs
    document.querySelectorAll('input[type="text"], textarea').forEach(input => {
        input.addEventListener('input', updatePreview);
    });

    // Setup color syncing for all color inputs
    const colorInputs = [
        'homepage_background_color', 'homepage_gradient_color', 'homepage_text_color',
        'navbar_background_color', 'navbar_gradient_color', 'navbar_text_color',
        'footer_background_color', 'footer_gradient_color', 'footer_text_color',
        'program_card_background_color', 'program_card_gradient_color', 'program_card_text_color', 'program_card_border_color',
        'enrollment_button_color', 'enrollment_button_text_color', 'enrollment_page_background_color', 'enrollment_page_gradient_color', 'enrollment_page_text_color', 'enrollment_form_background_color',
        'login_background_color', 'login_accent_color'
    ];
    
    colorInputs.forEach(setupColorSync);
    
    // Initial preview update
    updatePreview();
});
</script>
@endpush
