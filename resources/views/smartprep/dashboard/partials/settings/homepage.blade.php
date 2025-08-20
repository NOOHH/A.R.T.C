<!-- Homepage Settings -->
<div class="sidebar-section" id="homepage-settings" style="display: none;">
    <div class="section-header">
        <h5><i class="fas fa-home me-2"></i>Homepage Content</h5>
    </div>
    
    <form id="homepageForm" method="POST" action="{{ route('smartprep.dashboard.settings.update.homepage', ['website' => $selectedWebsite->id]) }}" enctype="multipart/form-data" onsubmit="updateHomepage(event)">
        @csrf
        <div class="form-group mb-3">
            <label class="form-label">Hero Title</label>
            <input type="text" class="form-control" name="hero_title" value="{{ $settings['homepage']['hero_title'] ?? 'Review Smarter. Learn Better. Succeed Faster.' }}" placeholder="Main headline">
        </div>
        
        <div class="form-group mb-3">
            <label class="form-label">Hero Subtitle</label>
            <textarea class="form-control" name="hero_subtitle" rows="3" placeholder="Hero description">{{ $settings['homepage']['hero_subtitle'] ?? 'Your premier destination for comprehensive review programs and professional training.' }}</textarea>
        </div>
        
        <!-- Section Content Customization -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-edit me-2"></i>Section Content</h6>
            </div>
            <div class="card-body">
                <!-- Programs Section Content -->
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-primary mb-3"><i class="fas fa-book me-2"></i>Programs Section</h6>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Programs Title</label>
                            <input type="text" class="form-control" name="programs_title" value="{{ $settings['homepage']['programs_title'] ?? 'Our Programs' }}" placeholder="Programs section title">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Programs Subtitle</label>
                            <input type="text" class="form-control" name="programs_subtitle" value="{{ $settings['homepage']['programs_subtitle'] ?? 'Choose from our comprehensive range of review and training programs' }}" placeholder="Programs section subtitle">
                        </div>
                    </div>
                </div>
                
                <!-- Modalities Section Content -->
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-primary mb-3 mt-3"><i class="fas fa-laptop me-2"></i>Learning Modalities Section</h6>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Modalities Title</label>
                            <input type="text" class="form-control" name="modalities_title" value="{{ $settings['homepage']['modalities_title'] ?? 'Learning Modalities' }}" placeholder="Modalities section title">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Modalities Subtitle</label>
                            <input type="text" class="form-control" name="modalities_subtitle" value="{{ $settings['homepage']['modalities_subtitle'] ?? 'Flexible learning options designed to fit your schedule and learning style' }}" placeholder="Modalities section subtitle">
                        </div>
                    </div>
                </div>
                
                <!-- About Section Content -->
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-primary mb-3 mt-3"><i class="fas fa-info-circle me-2"></i>About Section</h6>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">About Title</label>
                            <input type="text" class="form-control" name="about_title" value="{{ $settings['homepage']['about_title'] ?? 'About Us' }}" placeholder="About section title">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">About Text</label>
                            <textarea class="form-control" name="about_subtitle" rows="2" placeholder="About section description">{{ $settings['homepage']['about_subtitle'] ?? 'We are committed to providing high-quality education and training' }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Section-Specific Color Customization -->
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0"><i class="fas fa-layer-group me-2"></i>Section-Specific Colors</h6>
            </div>
            <div class="card-body">
                <!-- Hero Section Colors -->
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-primary mb-3"><i class="fas fa-home me-2"></i>Hero Section</h6>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Hero Background Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="{{ $settings['homepage']['hero_bg_color'] ?? '#667eea' }}" onchange="updatePreviewColor('hero_bg', this.value)">
                                <input type="text" class="form-control" name="homepage_hero_bg_color" value="{{ $settings['homepage']['hero_bg_color'] ?? '#667eea' }}">
                            </div>
                            <small class="form-text text-muted">Background color for hero section</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Hero Title Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="{{ $settings['homepage']['hero_title_color'] ?? '#ffffff' }}" onchange="updatePreviewColor('hero_title', this.value)">
                                <input type="text" class="form-control" name="homepage_hero_title_color" value="{{ $settings['homepage']['hero_title_color'] ?? '#ffffff' }}">
                            </div>
                            <small class="form-text text-muted">Color for hero title text</small>
                        </div>
                    </div>
                </div>
                
                <!-- Programs Section Colors -->
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-primary mb-3 mt-3"><i class="fas fa-book me-2"></i>Programs Section</h6>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Programs Title Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="{{ $settings['homepage']['programs_title_color'] ?? '#667eea' }}" onchange="updatePreviewColor('programs_title', this.value)">
                                <input type="text" class="form-control" name="homepage_programs_title_color" value="{{ $settings['homepage']['programs_title_color'] ?? '#667eea' }}">
                            </div>
                            <small class="form-text text-muted">"Our Programs" heading color</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Programs Subtitle Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="{{ $settings['homepage']['programs_subtitle_color'] ?? '#6c757d' }}" onchange="updatePreviewColor('programs_subtitle', this.value)">
                                <input type="text" class="form-control" name="homepage_programs_subtitle_color" value="{{ $settings['homepage']['programs_subtitle_color'] ?? '#6c757d' }}">
                            </div>
                            <small class="form-text text-muted">Programs description text color</small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="form-group mb-3">
                        <label class="form-label">Programs Section Gradient Color</label>
                        <div class="color-picker-group">
                            <input type="color" class="color-input" value="{{ $settings['homepage']['gradient_color'] ?? '#764ba2' }}" onchange="updatePreviewColor('homepage_gradient', this.value)">
                            <input type="text" class="form-control" name="homepage_gradient_color" value="{{ $settings['homepage']['gradient_color'] ?? '#764ba2' }}">
                        </div>
                        <small class="form-text text-muted">Second color for programs section gradient effect</small>
                    </div>
                    <div class="form-group mb-3">
                        <label class="form-label">Programs Section Background Color</label>
                        <div class="color-picker-group">
                            <input type="color" class="color-input" value="{{ $settings['homepage']['programs_section_bg_color'] ?? '#667eea' }}" onchange="updatePreviewColor('programs_section_bg', this.value)">
                            <input type="text" class="form-control" name="homepage_programs_section_bg_color" value="{{ $settings['homepage']['programs_section_bg_color'] ?? '#667eea' }}">
                        </div>
                        <small class="form-text text-muted">Background color for the programs section (creates gradient with secondary color)</small>
                    </div>
                </div>
                
                <!-- Modalities Section Colors -->
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-primary mb-3 mt-3"><i class="fas fa-laptop me-2"></i>Learning Modalities Section</h6>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Modalities Background Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="{{ $settings['homepage']['modalities_bg_color'] ?? '#667eea' }}" onchange="updatePreviewColor('modalities_bg', this.value)">
                                <input type="text" class="form-control" name="homepage_modalities_bg_color" value="{{ $settings['homepage']['modalities_bg_color'] ?? '#667eea' }}">
                            </div>
                            <small class="form-text text-muted">Background color for modalities section</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label class="form-label">Modalities Text Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="{{ $settings['homepage']['modalities_text_color'] ?? '#ffffff' }}" onchange="updatePreviewColor('modalities_text', this.value)">
                                <input type="text" class="form-control" name="homepage_modalities_text_color" value="{{ $settings['homepage']['modalities_text_color'] ?? '#ffffff' }}">
                            </div>
                            <small class="form-text text-muted">Text color for modalities section</small>
                        </div>
                    </div>
                </div>
                
                <!-- About Section Colors -->
                <div class="row">
                    <div class="col-12">
                        <h6 class="text-primary mb-3 mt-3"><i class="fas fa-info-circle me-2"></i>About Section</h6>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="form-label">About Background Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="{{ $settings['homepage']['about_bg_color'] ?? '#ffffff' }}" onchange="updatePreviewColor('about_bg', this.value)">
                                <input type="text" class="form-control" name="homepage_about_bg_color" value="{{ $settings['homepage']['about_bg_color'] ?? '#ffffff' }}">
                            </div>
                            <small class="form-text text-muted">Background color for about section</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="form-label">About Title Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="{{ $settings['homepage']['about_title_color'] ?? '#667eea' }}" onchange="updatePreviewColor('about_title', this.value)">
                                <input type="text" class="form-control" name="homepage_about_title_color" value="{{ $settings['homepage']['about_title_color'] ?? '#667eea' }}">
                            </div>
                            <small class="form-text text-muted">"About Us" heading color</small>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group mb-3">
                            <label class="form-label">About Text Color</label>
                            <div class="color-picker-group">
                                <input type="color" class="color-input" value="{{ $settings['homepage']['about_text_color'] ?? '#6c757d' }}" onchange="updatePreviewColor('about_text', this.value)">
                                <input type="text" class="form-control" name="homepage_about_text_color" value="{{ $settings['homepage']['about_text_color'] ?? '#6c757d' }}">
                            </div>
                            <small class="form-text text-muted">About description text color</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="form-group mb-3">
            <label class="form-label">Hero Background Image</label>
            <input type="file" class="form-control" name="hero_background" accept="image/*">
            <small class="form-text text-muted">Recommended: 1920x1080px</small>
            @if(isset($settings['homepage']['hero_background_image']) && $settings['homepage']['hero_background_image'])
                <div class="mt-2">
                    <small class="text-muted">Current image:</small><br>
                    <img src="{{ \Illuminate\Support\Facades\Storage::url($settings['homepage']['hero_background_image']) }}" alt="Current hero background" style="max-width: 200px; max-height: 100px;" class="img-thumbnail">
                </div>
            @endif
        </div>
        
        <div class="form-group mb-3">
            <label class="form-label">Login Page Image</label>
            <input type="file" class="form-control" name="login_image" accept="image/*">
            <small class="form-text text-muted">Image shown on login page</small>
        </div>
        
        <div class="form-group mb-3">
            <label class="form-label">Copyright Text</label>
            <input type="text" class="form-control" name="copyright" value="{{ $settings['homepage']['copyright'] ?? '© Copyright Ascendo Review and Training Center. All Rights Reserved.' }}" placeholder="Footer copyright">
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-sync me-2"></i>Update Homepage
        </button>
    </form>
</div>
