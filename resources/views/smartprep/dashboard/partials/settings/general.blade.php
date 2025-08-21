<!-- General Settings -->
<div class="sidebar-section active" id="general-settings">
    <div class="section-header">
        <h5><i class="fas fa-cog me-2"></i>General Settings</h5>
    </div>
    
    <form id="generalForm" method="POST" action="{{ route('smartprep.dashboard.settings.update.general', ['website' => $selectedWebsite->id]) }}" onsubmit="updateGeneral(event)">
        @csrf
        
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
        
        <!-- Admin Account Settings -->
        @php
            // Derive a fallback admin email if none stored
            $derivedAdminEmail = $settings['general']['admin_email'] ?? '';
            if (empty($derivedAdminEmail)) {
                $slugSource = $selectedWebsite->slug ?? 'artc';
                // Remove known prefix and sanitize
                $domainBase = preg_replace('/^smartprep-/', '', $slugSource);
                $domainBase = strtolower(preg_replace('/[^a-z0-9]+/', '', $domainBase));
                if ($domainBase === '') { $domainBase = 'site'; }
                $derivedAdminEmail = 'admin@' . $domainBase . '.com';
            }
        @endphp
        <div class="form-group mb-4">
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-user-shield text-primary me-2"></i>
                <label class="form-label mb-0">Admin Account Settings</label>
            </div>
            <p class="small text-muted mb-3">Manage the primary admin account for this website. The email will be automatically generated as admin@(website-name).com</p>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Admin Email</label>
                    <input type="email" class="form-control" name="admin_email" value="{{ $derivedAdminEmail }}" placeholder="e.g. admin@training.com">
                    <small class="form-text text-muted">Format: admin@(website-name).com</small>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Admin Password</label>
                    <input type="password" class="form-control" name="admin_password" placeholder="Minimum 8 characters">
                    <small class="form-text text-muted">Leave blank to keep current password</small>
                </div>
            </div>
        </div>

        <!-- Brand Name (Connected to Navigation) -->
        <div class="form-group mb-3">
            <label class="form-label">Brand Name</label>
            <input type="text" class="form-control" name="brand_name" value="{{ $settings['general']['brand_name'] ?? $settings['navbar']['brand_name'] ?? '' }}" placeholder="Enter brand name">
            <small class="form-text text-muted">This will appear in the navigation bar and browser tab</small>
        </div>

        <!-- Contact Information -->
        <div class="form-group mb-3">
            <label class="form-label">Contact Email (Optional)</label>
            <input type="email" class="form-control" name="contact_email" value="{{ $settings['general']['contact_email'] ?? '' }}" placeholder="Contact email">
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Phone Number (Optional)</label>
            <input type="text" class="form-control" name="contact_phone" value="{{ $settings['general']['contact_phone'] ?? '' }}" placeholder="Phone number">
        </div>

        <div class="form-group mb-3">
            <label class="form-label">Address (Optional)</label>
            <textarea class="form-control" name="contact_address" rows="3" placeholder="Physical address">{{ $settings['general']['contact_address'] ?? '' }}</textarea>
        </div>

        <!-- Terms and Conditions -->
        <div class="form-group mb-3">
            <label class="form-label">Terms and Conditions (Optional)</label>
            <textarea class="form-control" name="terms_conditions" rows="4" placeholder="Enter terms and conditions">{{ $settings['general']['terms_conditions'] ?? '' }}</textarea>
        </div>

        <!-- Social Media Links -->
        <div class="form-group mb-4">
            <div class="d-flex align-items-center mb-2">
                <i class="fas fa-share-alt text-primary me-2"></i>
                <label class="form-label mb-0">Social Media Links (Optional)</label>
            </div>
            <p class="small text-muted mb-3">Add social media links for your website footer</p>
            <div id="socialLinksContainer">
                @php
                    $socialLinks = json_decode($settings['general']['social_links'] ?? '[]', true) ?: [];
                @endphp
                @foreach($socialLinks as $index => $link)
                <div class="row g-2 mb-2 social-link-row">
                    <div class="col-md-4">
                        <select class="form-control" name="social_links[{{ $index }}][platform]">
                            <option value="">Select Platform</option>
                            <option value="facebook" {{ $link['platform'] == 'facebook' ? 'selected' : '' }}>Facebook</option>
                            <option value="youtube" {{ $link['platform'] == 'youtube' ? 'selected' : '' }}>YouTube</option>
                            <option value="twitter" {{ $link['platform'] == 'twitter' ? 'selected' : '' }}>Twitter</option>
                            <option value="instagram" {{ $link['platform'] == 'instagram' ? 'selected' : '' }}>Instagram</option>
                            <option value="linkedin" {{ $link['platform'] == 'linkedin' ? 'selected' : '' }}>LinkedIn</option>
                            <option value="tiktok" {{ $link['platform'] == 'tiktok' ? 'selected' : '' }}>TikTok</option>
                            <option value="telegram" {{ $link['platform'] == 'telegram' ? 'selected' : '' }}>Telegram</option>
                            <option value="whatsapp" {{ $link['platform'] == 'whatsapp' ? 'selected' : '' }}>WhatsApp</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <input type="url" class="form-control" name="social_links[{{ $index }}][url]" value="{{ $link['url'] ?? '' }}" placeholder="https://...">
                    </div>
                    <div class="col-md-2">
                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSocialLink(this)">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-outline-primary btn-sm" onclick="addSocialLink()">
                <i class="fas fa-plus me-1"></i>Add Social Link
            </button>
        </div>
        
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-sync me-2"></i>Update General Settings
        </button>
    </form>
</div>

<script>
function addSocialLink() {
    const container = document.getElementById('socialLinksContainer');
    const index = container.children.length;
    
    const linkRow = document.createElement('div');
    linkRow.className = 'row g-2 mb-2 social-link-row';
    linkRow.innerHTML = `
        <div class="col-md-4">
            <select class="form-control" name="social_links[${index}][platform]">
                <option value="">Select Platform</option>
                <option value="facebook">Facebook</option>
                <option value="youtube">YouTube</option>
                <option value="twitter">Twitter</option>
                <option value="instagram">Instagram</option>
                <option value="linkedin">LinkedIn</option>
                <option value="tiktok">TikTok</option>
                <option value="telegram">Telegram</option>
                <option value="whatsapp">WhatsApp</option>
            </select>
        </div>
        <div class="col-md-6">
            <input type="url" class="form-control" name="social_links[${index}][url]" placeholder="https://...">
        </div>
        <div class="col-md-2">
            <button type="button" class="btn btn-outline-danger btn-sm" onclick="removeSocialLink(this)">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    
    container.appendChild(linkRow);
}

function removeSocialLink(button) {
    const row = button.closest('.social-link-row');
    row.remove();
    
    // Reindex remaining rows
    const container = document.getElementById('socialLinksContainer');
    const rows = container.querySelectorAll('.social-link-row');
    rows.forEach((row, index) => {
        const select = row.querySelector('select');
        const input = row.querySelector('input');
        select.name = `social_links[${index}][platform]`;
        input.name = `social_links[${index}][url]`;
    });
}
</script>
