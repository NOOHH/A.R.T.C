<!-- Branding Settings -->
<div class="sidebar-section" id="branding-settings" style="display: none;">
    <div class="section-header">
        <h5><i class="fas fa-palette me-2"></i>Branding & Design</h5>
    </div>
    
    <form id="brandingForm" method="POST" action="{{ route('smartprep.dashboard.settings.update.branding', ['website' => $selectedWebsite->id]) }}">
        @csrf
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        <div class="form-group mb-3">
            <label class="form-label">Primary Color</label>
            <div class="color-picker-group">
                <input type="color" class="color-input" value="{{ $settings['branding']['primary_color'] ?? '#667eea' }}" onchange="updatePreviewColor('primary', this.value)">
                <input type="text" class="form-control" name="primary_color" value="{{ $settings['branding']['primary_color'] ?? '#667eea' }}">
            </div>
        </div>
        
        <div class="form-group mb-3">
            <label class="form-label">Secondary Color</label>
            <div class="color-picker-group">
                <input type="color" class="color-input" value="{{ $settings['branding']['secondary_color'] ?? '#764ba2' }}" onchange="updatePreviewColor('secondary', this.value)">
                <input type="text" class="form-control" name="secondary_color" value="{{ $settings['branding']['secondary_color'] ?? '#764ba2' }}">
            </div>
        </div>
        
        <div class="form-group mb-3">
            <label class="form-label">Background Color</label>
            <div class="color-picker-group">
                <input type="color" class="color-input" value="{{ $settings['branding']['background_color'] ?? '#ffffff' }}" onchange="updatePreviewColor('background', this.value)">
                <input type="text" class="form-control" name="background_color" value="{{ $settings['branding']['background_color'] ?? '#ffffff' }}">
            </div>
        </div>
        
        <div class="form-group mb-3">
            <label class="form-label">Logo URL</label>
            <input type="text" class="form-control" name="logo_url" value="{{ $settings['branding']['logo_url'] ?? '' }}" placeholder="Enter logo URL or path">
            <small class="form-text text-muted">Enter the URL or path to your logo image</small>
        </div>
        
        <div class="form-group mb-3">
            <label class="form-label">Favicon URL</label>
            <input type="text" class="form-control" name="favicon_url" value="{{ $settings['branding']['favicon_url'] ?? '' }}" placeholder="Enter favicon URL or path">
            <small class="form-text text-muted">32x32px ICO or PNG format</small>
        </div>
        
        <div class="form-group mb-3">
            <label class="form-label">Custom Font</label>
            <select class="form-control" name="font_family">
                <option value="Inter" {{ ($settings['branding']['font_family'] ?? 'Inter') == 'Inter' ? 'selected' : '' }}>Inter (Default)</option>
                <option value="Roboto" {{ ($settings['branding']['font_family'] ?? '') == 'Roboto' ? 'selected' : '' }}>Roboto</option>
                <option value="Open Sans" {{ ($settings['branding']['font_family'] ?? '') == 'Open Sans' ? 'selected' : '' }}>Open Sans</option>
                <option value="Lato" {{ ($settings['branding']['font_family'] ?? '') == 'Lato' ? 'selected' : '' }}>Lato</option>
                <option value="Poppins" {{ ($settings['branding']['font_family'] ?? '') == 'Poppins' ? 'selected' : '' }}>Poppins</option>
                <option value="Montserrat" {{ ($settings['branding']['font_family'] ?? '') == 'Montserrat' ? 'selected' : '' }}>Montserrat</option>
            </select>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-sync me-2"></i>Update Branding
        </button>
    </form>
</div>
