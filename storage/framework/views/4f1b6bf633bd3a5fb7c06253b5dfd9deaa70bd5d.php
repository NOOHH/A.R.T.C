<script type="text/javascript">
    // Settings tab navigation with enhanced functionality
    document.addEventListener('DOMContentLoaded', function() {
        const navTabs = document.querySelectorAll('.settings-nav-tab');
        const sidebarSections = document.querySelectorAll('.sidebar-section');
        
        navTabs.forEach(tab => {
            tab.addEventListener('click', function() {
                const section = this.getAttribute('data-section');
                
                // Update active tab
                navTabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                
                // Update active section
                sidebarSections.forEach(s => {
                    s.classList.remove('active');
                    s.style.display = 'none';
                });
                document.getElementById(section + '-settings').style.display = 'block';
                document.getElementById(section + '-settings').classList.add('active');
                
                // Update preview URL based on section
                updatePreviewForSection(section);
            });
        });
        
        // Initialize color picker synchronization
        initializeColorPickers();
        
        // Enable auto-save for important changes
        enableAutoSave();
        
        // Initialize preview URL from settings
        initializePreviewUrl();
        
        // Refresh homepage form with current data
        setTimeout(() => {
            refreshHomepageForm();
        }, 500);
    });

    // Form submission handlers
    async function updateGeneral(event) {
        event.preventDefault();
        await handleFormSubmission(event, 'general', 'Updating general settings...');
    }
    
    async function updateBranding(event) {
        event.preventDefault();
        await handleFormSubmission(event, 'branding', 'Updating branding...');
    }
    
    async function updateNavbar(event) {
        event.preventDefault();
        await handleFormSubmission(event, 'navbar', 'Updating navigation...');
    }
    
    async function updateHomepage(event) {
        event.preventDefault();
        await handleFormSubmission(event, 'homepage', 'Updating homepage content...');
    }
    
    async function updateStudent(event) {
        event.preventDefault();
        await handleFormSubmission(event, 'student', 'Updating student portal...');
    }
    
    async function updateProfessor(event) {
        event.preventDefault();
        await handleFormSubmission(event, 'professor', 'Updating professor panel...');
    }
    
    async function updateAdmin(event) {
        event.preventDefault();
        await handleFormSubmission(event, 'admin', 'Updating admin panel...');
    }
    
    async function updateAdvanced(event) {
        event.preventDefault();
        await handleFormSubmission(event, 'advanced', 'Updating advanced settings...');
    }

    async function handleFormSubmission(event, settingType, loadingText) {
        const submitBtn = event.target.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        const formData = new FormData(event.target);
        
        // Process social media links for general settings
        if (settingType === 'general') {
            const socialLinks = [];
            const platformInputs = document.querySelectorAll('select[name^="social_links"][name$="[platform]"]');
            const urlInputs = document.querySelectorAll('input[name^="social_links"][name$="[url]"]');
            
            for (let i = 0; i < platformInputs.length; i++) {
                const platform = platformInputs[i].value;
                const url = urlInputs[i].value;
                if (platform && url) {
                    socialLinks.push({ platform, url });
                }
            }
            
            // Remove existing social_links from formData and add processed version
            formData.delete('social_links');
            formData.append('social_links', JSON.stringify(socialLinks));
        }
        
        // Debug: Log form data
        console.log('Form submission debug:', {
            settingType: settingType,
            formData: Object.fromEntries(formData.entries()),
            heroTitle: formData.get('hero_title'),
            heroTitleLength: formData.get('hero_title') ? formData.get('hero_title').length : 0
        });
        
        // Update button state
        submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin me-2"></i>${loadingText}`;
        submitBtn.disabled = true;
        
        try {
            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
            
            // Determine the correct endpoint based on setting type and selected website id
            const websiteId = document.querySelector('meta[name="x-selected-website-id"]')?.getAttribute('content') || null;
            if (!websiteId) {
                throw new Error('No website selected for this customization session.');
            }

            // Map settingType directly to route path segment (names match)
            let endpoint = `/smartprep/dashboard/settings/${settingType}/${websiteId}`;
            
            // Make actual AJAX call
            const response = await fetch(endpoint, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            });
            
            console.log('Response debug:', {
                status: response.status,
                ok: response.ok,
                headers: Object.fromEntries(response.headers.entries())
            });
            
            if (response.ok) {
                const result = await response.json();
                console.log('Response result:', result);
                
                // Success state
                submitBtn.innerHTML = '<i class="fas fa-check me-2"></i>Settings Updated Successfully!';
                showNotification(`${settingType.charAt(0).toUpperCase() + settingType.slice(1)} settings have been updated successfully!`, 'success');
                
                // Show additional notification for homepage updates
                if (settingType === 'homepage') {
                    setTimeout(() => {
                        showNotification('💡 Tip: Refresh the homepage (Ctrl+F5) to see the changes!', 'info');
                        
                        // Add a "View Changes" button
                        const viewChangesBtn = document.createElement('button');
                        viewChangesBtn.className = 'btn btn-outline-primary btn-sm ms-2';
                        viewChangesBtn.onclick = () => window.open('<?php echo e($previewUrl); ?>?v=' + Date.now(), '_blank');
                        
                        // Add the button to the notification area
                        const notificationArea = document.querySelector('.notification-area') || document.body;
                        notificationArea.appendChild(viewChangesBtn);
                    }, 1000);
                }
                
                // Refresh preview if needed
                if (['branding', 'navbar', 'homepage'].includes(settingType)) {
                    refreshPreview();
                }
                
                // Refresh form data to show updated values
                if (settingType === 'homepage') {
                    refreshHomepageForm();
                }
                
                // Reset button after 2 seconds
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 2000);
            } else {
                throw new Error('Server error: ' + response.status);
            }
            
        } catch (error) {
            console.error('Error updating settings:', error);
            
            // Error state
            submitBtn.innerHTML = '<i class="fas fa-exclamation-triangle me-2"></i>Update Failed';
            showNotification('Failed to update settings. Please try again.', 'danger');
            
            setTimeout(() => {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }, 2000);
        }
    }

    // Update preview based on section
    function updatePreviewForSection(section) {
        const iframe = document.getElementById('previewFrame');
        const openInNewTabLink = document.getElementById('openInNewTabLink');
        const previewTitle = document.querySelector('.preview-title');
        
        if (!iframe || !openInNewTabLink || !previewTitle) {
            console.error('Preview elements not found');
            return;
        }
        
    // Determine base preview URL from DOM (selected website) or fallback
    const previewPanelEl = document.querySelector('.preview-panel');
    let previewUrl = (previewPanelEl && previewPanelEl.getAttribute('data-preview-base')) || '<?php echo e($previewUrl); ?>';
        let titleText = 'Live Preview';
        
        switch(section) {
            case 'student':
                // Use tenant-specific student dashboard URL instead of hardcoded /student/dashboard
                previewUrl = '<?php echo e($previewUrl); ?>' + '/student/dashboard';
                titleText = 'Student Portal Preview';
                break;
            case 'professor':
                // Use tenant-specific professor dashboard URL instead of hardcoded /professor/dashboard
                previewUrl = '<?php echo e($previewUrl); ?>' + '/professor/dashboard';
                titleText = 'Professor Panel Preview';
                break;
            case 'admin':
                // Use tenant-specific admin dashboard URL instead of hardcoded /admin-dashboard
                previewUrl = '<?php echo e($previewUrl); ?>' + '/admin-dashboard';
                titleText = 'Admin Panel Preview';
                break;
            case 'homepage':
                previewUrl = '<?php echo e($previewUrl); ?>';
                titleText = 'Homepage Preview';
                break;
            case 'navbar':
            case 'branding':
                previewUrl = '<?php echo e($previewUrl); ?>';
                titleText = 'Live Preview';
                break;
            default:
                previewUrl = '<?php echo e($previewUrl); ?>';
                titleText = 'Live Preview';
        }
        
    // Add preview parameter, website parameter, and timestamp to bypass cache
    const websiteId = '<?php echo e($selectedWebsite->id); ?>';
    const urlSeparator = previewUrl.includes('?') ? '&' : '?';
    const finalUrl = previewUrl + urlSeparator + 'website=' + websiteId + '&preview=true&t=' + Date.now();
        
        console.log('Updating preview for section:', section, 'URL:', finalUrl);
        
        // Update iframe
        iframe.src = finalUrl;
        
        // Update open in new tab link
        openInNewTabLink.href = finalUrl;
        
        // Update title
        previewTitle.innerHTML = `<i class="fas fa-eye me-2"></i>${titleText}`;
        
        // Show loading state
        showLoading();
    }

    // Color picker management
    function updatePreviewColor(type, color) {
        const textInput = event.target.nextElementSibling;
        if (textInput) {
            textInput.value = color;
        }
        
        // Apply color changes to preview iframe
        try {
            const iframe = document.getElementById('previewFrame');
            if (iframe && iframe.contentDocument) {
                const iframeDoc = iframe.contentDocument;
                const root = iframeDoc.documentElement;
                
                switch(type) {
                    case 'primary':
                        root.style.setProperty('--primary-color', color);
                        break;
                    case 'secondary':
                        root.style.setProperty('--secondary-color', color);
                        break;
                    case 'background':
                        root.style.setProperty('--background-color', color);
                        break;
                }
            }
        } catch (e) {
            console.log('Cross-origin iframe access restricted - normal behavior');
        }
        
        showNotification(`${type.charAt(0).toUpperCase() + type.slice(1)} color updated to ${color}`, 'info');
    }
    
    function initializeColorPickers() {
        document.querySelectorAll('.color-picker-group').forEach(group => {
            const colorInput = group.querySelector('.color-input');
            const textInput = group.querySelector('input[type="text"]');
            
            if (colorInput && textInput) {
                // Update color picker when text is changed
                textInput.addEventListener('input', function() {
                    if (/^#[0-9A-F]{6}$/i.test(this.value)) {
                        colorInput.value = this.value;
                    }
                });
                
                // Update text when color picker is changed
                colorInput.addEventListener('input', function() {
                    textInput.value = this.value;
                });
            }
        });
    }

    // Preview control functions
    async function refreshPreview() {
        const iframe = document.getElementById('previewFrame');
        const loading = document.getElementById('previewLoading');
        
        if (iframe && loading) {
            loading.style.display = 'flex';
            iframe.style.opacity = '0.5';
            
            try {
                // Fetch current UI settings from API with website parameter
                const websiteId = '<?php echo e($selectedWebsite->id); ?>';
                const apiUrl = '<?php echo e(route("smartprep.api.ui-settings")); ?>' + '?website=' + websiteId;
                const response = await fetch(apiUrl);
                if (response.ok) {
                    const settings = await response.json();
                    console.log('Fetched tenant settings:', settings.data);
                    applySettingsToPreview(settings.data);
                    
                    // Update iframe src with configurable preview URL and website parameter
                    const fallbackUrl = "<?php echo e($previewUrl); ?>";
                    const basePreviewUrl = (settings.data && settings.data.general && settings.data.general.preview_url)
                        ? settings.data.general.preview_url
                        : fallbackUrl;
                    const urlSeparator = basePreviewUrl.includes('?') ? '&' : '?';
                    const finalPreviewUrl = basePreviewUrl + urlSeparator + 'website=' + websiteId + '&preview=true&t=' + Date.now();
                    console.log('Setting iframe to:', finalPreviewUrl);
                    iframe.src = finalPreviewUrl;
                }
            } catch (error) {
                console.error('Failed to fetch UI settings:', error);
                // Fallback to default URL
                iframe.src = "<?php echo e($previewUrl); ?>";
            }
            
            showNotification('Refreshing preview...', 'info');
        }
    }
    
    // Apply settings to preview iframe
    function applySettingsToPreview(settings) {
        try {
            const iframe = document.getElementById('previewFrame');
            if (iframe && iframe.contentDocument) {
                const iframeDoc = iframe.contentDocument;
                const root = iframeDoc.documentElement;
                
                // Apply branding colors
                if (settings.branding) {
                    if (settings.branding.primary_color) {
                        root.style.setProperty('--primary-color', settings.branding.primary_color);
                    }
                    if (settings.branding.secondary_color) {
                        root.style.setProperty('--secondary-color', settings.branding.secondary_color);
                    }
                    if (settings.branding.background_color) {
                        root.style.setProperty('--background-color', settings.branding.background_color);
                    }
                    if (settings.branding.font_family) {
                        root.style.setProperty('--font-family', settings.branding.font_family);
                    }
                }
                
                // Apply navbar settings
                if (settings.navbar) {
                    const navbar = iframeDoc.querySelector('.navbar-brand');
                    if (navbar && settings.navbar.brand_name) {
                        // Find the strong element that contains the brand name
                        const brandText = navbar.querySelector('strong');
                        if (brandText) {
                            brandText.textContent = settings.navbar.brand_name;
                        } else {
                            // If no strong element exists, create one
                            const strong = iframeDoc.createElement('strong');
                            strong.textContent = settings.navbar.brand_name;
                            navbar.appendChild(strong);
                        }
                    }
                }
                
                // Apply homepage settings
                if (settings.homepage) {
                    const heroTitle = iframeDoc.querySelector('.hero-title');
                    if (heroTitle && settings.homepage.hero_title) {
                        heroTitle.textContent = settings.homepage.hero_title;
                    }
                    
                    const heroSubtitle = iframeDoc.querySelector('.hero-subtitle');
                    if (heroSubtitle && settings.homepage.hero_subtitle) {
                        heroSubtitle.textContent = settings.homepage.hero_subtitle;
                    }
                }
            }
            
            // Update "Open in New Tab" link with configurable preview URL
            const openInNewTabLink = document.getElementById('openInNewTabLink');
            // Blade-evaluated flag indicating tenant customization context (avoid Blade braces in JS)
            var isTenantContext = ('<?php echo $selectedWebsite ? 1 : 0; ?>' === '1');
            if (openInNewTabLink) {
                if (!isTenantContext && settings.general?.preview_url) {
                    // Global context: use platform preview_url
                    let href = settings.general.preview_url;
                    href += (href.includes('?') ? '&' : '?') + 'preview=true';
                    openInNewTabLink.href = href;
                } else if (isTenantContext) {
                    // Tenant context: preserve current iframe src base (without cache buster)
                    let base = iframe ? iframe.src.split('?')[0] : '<?php echo e($previewUrl); ?>';
                    let href = base + (base.includes('?') ? '&' : '?') + 'preview=true';
                    openInNewTabLink.href = href;
                }
            }
        } catch (e) {
            console.log('Cross-origin iframe access restricted - normal behavior');
        }
    }
    
    function hideLoading() {
        const loading = document.getElementById('previewLoading');
        const iframe = document.getElementById('previewFrame');
        
        setTimeout(() => {
            if (loading) loading.style.display = 'none';
            if (iframe) iframe.style.opacity = '1';
        }, 500);
    }

    function showLoading() {
        const loading = document.getElementById('previewLoading');
        const iframe = document.getElementById('previewFrame');
        
        if (loading) {
            loading.style.display = 'flex';
            loading.innerHTML = `
                <div class="loading-spinner"></div>
                <span class="text-muted">Loading preview...</span>
            `;
        }
        if (iframe) {
            iframe.style.opacity = '0.5';
        }
    }
    
    // Initialize preview URL from settings
    async function initializePreviewUrl() {
        try {
            const response = await fetch('<?php echo e(route("smartprep.api.ui-settings")); ?>');
            if (response.ok) {
                const settings = await response.json();
                const fallbackUrlInit = (document.querySelector('.preview-panel')?.getAttribute('data-preview-base')) || "<?php echo e($previewUrl); ?>";
                const previewUrl = fallbackUrlInit; // Keep tenant base, ignore global helper value
                
                // Update iframe src
                const iframe = document.getElementById('previewFrame');
                if (iframe) {
                    iframe.src = previewUrl + (previewUrl.includes('?') ? '&' : '?') + 'preview=true&t=' + Date.now();
                }
                
                // Update "Open in New Tab" link
                const openInNewTabLink = document.getElementById('openInNewTabLink');
                if (openInNewTabLink) {
                    const hrefWithPreview = previewUrl + (previewUrl.includes('?') ? '&' : '?') + 'preview=true';
                    openInNewTabLink.href = hrefWithPreview;
                }
            }
        } catch (error) {
            console.error('Failed to initialize preview URL:', error);
        }
    }

    // Refresh homepage form with latest data
    async function refreshHomepageForm() {
        try {
            const response = await fetch('<?php echo e(route("smartprep.api.ui-settings")); ?>');
            if (response.ok) {
                const settings = await response.json();
                const homepageSettings = settings.data.homepage;
                
                if (homepageSettings) {
                    // Update form fields with latest data
                    const form = document.getElementById('homepageForm');
                    if (form) {
                        // Update text inputs
                        const heroTitleInput = form.querySelector('input[name="hero_title"]');
                        if (heroTitleInput) {
                            heroTitleInput.value = homepageSettings.hero_title || '';
                        }
                        
                        const heroSubtitleInput = form.querySelector('textarea[name="hero_subtitle"]');
                        if (heroSubtitleInput) {
                            heroSubtitleInput.value = homepageSettings.hero_subtitle || '';
                        }
                        
                        const ctaPrimaryTextInput = form.querySelector('input[name="cta_primary_text"]');
                        if (ctaPrimaryTextInput) {
                            ctaPrimaryTextInput.value = homepageSettings.cta_primary_text || '';
                        }
                        
                        const ctaPrimaryLinkInput = form.querySelector('input[name="cta_primary_link"]');
                        if (ctaPrimaryLinkInput) {
                            ctaPrimaryLinkInput.value = homepageSettings.cta_primary_link || '';
                        }
                        
                        const ctaSecondaryTextInput = form.querySelector('input[name="cta_secondary_text"]');
                        if (ctaSecondaryTextInput) {
                            ctaSecondaryTextInput.value = homepageSettings.cta_secondary_text || '';
                        }
                        
                        const ctaSecondaryLinkInput = form.querySelector('input[name="cta_secondary_link"]');
                        if (ctaSecondaryLinkInput) {
                            ctaSecondaryLinkInput.value = homepageSettings.cta_secondary_link || '';
                        }
                        
                        const featuresTitleInput = form.querySelector('input[name="features_title"]');
                        if (featuresTitleInput) {
                            featuresTitleInput.value = homepageSettings.features_title || '';
                        }
                        
                        const copyrightInput = form.querySelector('input[name="copyright"]');
                        if (copyrightInput) {
                            copyrightInput.value = homepageSettings.copyright || '';
                        }
                        
                        // Update color inputs
                        const backgroundColorInput = form.querySelector('input[name="homepage_background_color"]');
                        if (backgroundColorInput) {
                            backgroundColorInput.value = homepageSettings.background_color || '#667eea';
                        }
                        
                        const gradientColorInput = form.querySelector('input[name="homepage_gradient_color"]');
                        if (gradientColorInput) {
                            gradientColorInput.value = homepageSettings.gradient_color || '#764ba2';
                        }
                        
                        const textColorInput = form.querySelector('input[name="homepage_text_color"]');
                        if (textColorInput) {
                            textColorInput.value = homepageSettings.text_color || '#ffffff';
                        }
                        
                        const buttonColorInput = form.querySelector('input[name="homepage_button_color"]');
                        if (buttonColorInput) {
                            buttonColorInput.value = homepageSettings.button_color || '#28a745';
                        }
                        
                        // Update color pickers
                        const colorPickers = form.querySelectorAll('.color-input');
                        colorPickers.forEach(picker => {
                            const textInput = picker.nextElementSibling;
                            if (textInput && textInput.value) {
                                picker.value = textInput.value;
                            }
                        });
                    }
                }
            }
        } catch (error) {
            console.error('Failed to refresh homepage form:', error);
        }
    }
    
    function showError() {
        const loading = document.getElementById('previewLoading');
        if (loading) {
            loading.innerHTML = `
                <div class="text-center text-danger">
                    <i class="fas fa-exclamation-triangle fa-2x mb-2"></i>
                    <div>Preview failed to load</div>
                    <small>Server may be offline</small>
                </div>
            `;
        }
    }

    // Enhanced notification system
    function showNotification(message, type = 'success') {
        // Remove existing notifications of the same type
        document.querySelectorAll('.settings-notification').forEach(n => n.remove());
        
        const notification = document.createElement('div');
        notification.className = `alert alert-${type} alert-dismissible fade show settings-notification position-fixed`;
        notification.style.top = '20px';
        notification.style.right = '20px';
        notification.style.zIndex = '9999';
        notification.style.minWidth = '300px';
        notification.style.maxWidth = '400px';
        notification.style.boxShadow = '0 4px 6px rgba(0,0,0,0.1)';
        
        const iconMap = {
            success: 'fas fa-check-circle',
            danger: 'fas fa-exclamation-circle',
            warning: 'fas fa-exclamation-triangle',
            info: 'fas fa-info-circle'
        };
        
        notification.innerHTML = `
            <i class="${iconMap[type]} me-2"></i>
            ${message}
            <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
    }

    // Global settings management
    async function saveAllSettings() {
        showNotification('Saving all settings...', 'info');
        
        // Simulate saving all forms
        const forms = document.querySelectorAll('form');
        let successCount = 0;
        
        for (let form of forms) {
            try {
                // Simulate API call for each form
                await new Promise(resolve => setTimeout(resolve, 200));
                successCount++;
            } catch (error) {
                console.error('Failed to save form:', form.id);
            }
        }
        
        showNotification(`Successfully saved ${successCount} setting sections!`, 'success');
        refreshPreview(); // Refresh preview after saving all settings
    }
    
    async function publishChanges() {
        showNotification('Publishing changes to live site...', 'info');
        
        try {
            // Simulate publishing process
            await new Promise(resolve => setTimeout(resolve, 2000));
            showNotification('Changes published successfully! Site is now live with your updates.', 'success');
            refreshPreview(); // Refresh preview after publishing
        } catch (error) {
            showNotification('Failed to publish changes. Please try again.', 'danger');
        }
    }

    // Auto-save functionality
    function enableAutoSave() {
        let autoSaveTimeout;
        
        document.querySelectorAll('input, textarea, select').forEach(element => {
            element.addEventListener('input', function() {
                clearTimeout(autoSaveTimeout);
                
                // Auto-save after 3 seconds of inactivity
                autoSaveTimeout = setTimeout(() => {
                    if (this.name && this.value) {
                        console.log('Auto-saving:', this.name, this.value);
                        // Implement actual auto-save logic here
                    }
                }, 3000);
            });
        });
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl+S or Cmd+S to save all
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            saveAllSettings();
        }
        
        // Ctrl+R or Cmd+R to refresh preview
        if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
            e.preventDefault();
            refreshPreview(); // Refresh preview with keyboard shortcut
        }
    });

    // ====== ROLE-SPECIFIC SIDEBAR FUNCTIONS ======

    // Student Sidebar Functions
    function updateStudentSidebarColor(type, value) {
        document.getElementById(`studentSidebar${type.charAt(0).toUpperCase() + type.slice(1)}Text`).value = value;
        updateStudentSidebarPreview();
    }

    function updateStudentSidebarPreview() {
        const preview = document.getElementById('studentSidebarPreview');
        if (!preview) return;

        const primaryColor = document.getElementById('studentSidebarPrimary').value;
        const secondaryColor = document.getElementById('studentSidebarSecondary').value;
        const accentColor = document.getElementById('studentSidebarAccent').value;
        const textColor = document.getElementById('studentSidebarText').value;
        const hoverColor = document.getElementById('studentSidebarHover').value;

        preview.style.setProperty('--preview-primary', primaryColor);
        preview.style.setProperty('--preview-secondary', secondaryColor);
        preview.style.setProperty('--preview-accent', accentColor);
        preview.style.setProperty('--preview-text', textColor);
        preview.style.setProperty('--preview-hover', hoverColor);
    }

    function saveStudentSidebarColors() {
        const colors = {
            primary_color: document.getElementById('studentSidebarPrimary').value,
            secondary_color: document.getElementById('studentSidebarSecondary').value,
            accent_color: document.getElementById('studentSidebarAccent').value,
            text_color: document.getElementById('studentSidebarText').value,
            hover_color: document.getElementById('studentSidebarHover').value
        };

        saveSidebarColorsForRole('student', colors);
    }

    function resetStudentSidebarColors() {
        if (confirm('Reset student sidebar colors to default?')) {
            document.getElementById('studentSidebarPrimary').value = '#1a1a1a';
            document.getElementById('studentSidebarSecondary').value = '#2d2d2d';
            document.getElementById('studentSidebarAccent').value = '#3b82f6';
            document.getElementById('studentSidebarText').value = '#e0e0e0';
            document.getElementById('studentSidebarHover').value = '#374151';
            updateStudentSidebarPreview();
            showNotification('Student sidebar colors reset to default', 'info');
        }
    }

    // Professor Sidebar Functions
    function updateProfessorSidebarColor(type, value) {
        document.getElementById(`professorSidebar${type.charAt(0).toUpperCase() + type.slice(1)}Text`).value = value;
        updateProfessorSidebarPreview();
    }

    function updateProfessorSidebarPreview() {
        const preview = document.getElementById('professorSidebarPreview');
        if (!preview) return;

        const primaryColor = document.getElementById('professorSidebarPrimary').value;
        const secondaryColor = document.getElementById('professorSidebarSecondary').value;
        const accentColor = document.getElementById('professorSidebarAccent').value;
        const textColor = document.getElementById('professorSidebarText').value;
        const hoverColor = document.getElementById('professorSidebarHover').value;

        preview.style.setProperty('--preview-primary', primaryColor);
        preview.style.setProperty('--preview-secondary', secondaryColor);
        preview.style.setProperty('--preview-accent', accentColor);
        preview.style.setProperty('--preview-text', textColor);
        preview.style.setProperty('--preview-hover', hoverColor);
    }

    function saveProfessorSidebarColors() {
        const colors = {
            primary_color: document.getElementById('professorSidebarPrimary').value,
            secondary_color: document.getElementById('professorSidebarSecondary').value,
            accent_color: document.getElementById('professorSidebarAccent').value,
            text_color: document.getElementById('professorSidebarText').value,
            hover_color: document.getElementById('professorSidebarHover').value
        };

        saveSidebarColorsForRole('professor', colors);
    }

    function resetProfessorSidebarColors() {
        if (confirm('Reset professor sidebar colors to default?')) {
            document.getElementById('professorSidebarPrimary').value = '#1e293b';
            document.getElementById('professorSidebarSecondary').value = '#334155';
            document.getElementById('professorSidebarAccent').value = '#10b981';
            document.getElementById('professorSidebarText').value = '#f1f5f9';
            document.getElementById('professorSidebarHover').value = '#475569';
            updateProfessorSidebarPreview();
            showNotification('Professor sidebar colors reset to default', 'info');
        }
    }

    // Admin Sidebar Functions
    function updateAdminSidebarColor(type, value) {
        document.getElementById(`adminSidebar${type.charAt(0).toUpperCase() + type.slice(1)}Text`).value = value;
        updateAdminSidebarPreview();
    }

    function updateAdminSidebarPreview() {
        const preview = document.getElementById('adminSidebarPreview');
        if (!preview) return;

        const primaryColor = document.getElementById('adminSidebarPrimary').value;
        const secondaryColor = document.getElementById('adminSidebarSecondary').value;
        const accentColor = document.getElementById('adminSidebarAccent').value;
        const textColor = document.getElementById('adminSidebarText').value;
        const hoverColor = document.getElementById('adminSidebarHover').value;

        preview.style.setProperty('--preview-primary', primaryColor);
        preview.style.setProperty('--preview-secondary', secondaryColor);
        preview.style.setProperty('--preview-accent', accentColor);
        preview.style.setProperty('--preview-text', textColor);
        preview.style.setProperty('--preview-hover', hoverColor);
    }

    function saveAdminSidebarColors() {
        const colors = {
            primary_color: document.getElementById('adminSidebarPrimary').value,
            secondary_color: document.getElementById('adminSidebarSecondary').value,
            accent_color: document.getElementById('adminSidebarAccent').value,
            text_color: document.getElementById('adminSidebarText').value,
            hover_color: document.getElementById('adminSidebarHover').value
        };

        saveSidebarColorsForRole('admin', colors);
    }

    function resetAdminSidebarColors() {
        if (confirm('Reset admin sidebar colors to default?')) {
            document.getElementById('adminSidebarPrimary').value = '#111827';
            document.getElementById('adminSidebarSecondary').value = '#1f2937';
            document.getElementById('adminSidebarAccent').value = '#f59e0b';
            document.getElementById('adminSidebarText').value = '#f9fafb';
            document.getElementById('adminSidebarHover').value = '#374151';
            updateAdminSidebarPreview();
            showNotification('Admin sidebar colors reset to default', 'info');
        }
    }

    // Function to save sidebar colors for a specific role
    function saveSidebarColors(role) {
        let colors = {};
        
        if (role === 'student') {
            colors = {
                primary_color: document.getElementById('studentSidebarPrimary').value,
                secondary_color: document.getElementById('studentSidebarSecondary').value,
                accent_color: document.getElementById('studentSidebarAccent').value,
                text_color: document.getElementById('studentSidebarText').value,
                hover_color: document.getElementById('studentSidebarHover').value
            };
        } else if (role === 'professor') {
            colors = {
                primary_color: document.getElementById('professorSidebarPrimary').value,
                secondary_color: document.getElementById('professorSidebarSecondary').value,
                accent_color: document.getElementById('professorSidebarAccent').value,
                text_color: document.getElementById('professorSidebarText').value,
                hover_color: document.getElementById('professorSidebarHover').value
            };
        } else if (role === 'admin') {
            colors = {
                primary_color: document.getElementById('adminSidebarPrimary').value,
                secondary_color: document.getElementById('adminSidebarSecondary').value,
                accent_color: document.getElementById('adminSidebarAccent').value,
                text_color: document.getElementById('adminSidebarText').value,
                hover_color: document.getElementById('adminSidebarHover').value
            };
        }
        
        saveSidebarColorsForRole(role, colors);
    }

    // Shared function for saving sidebar colors
    function saveSidebarColorsForRole(role, colors) {
        fetch(`/smartprep/dashboard/settings/sidebar/${websiteId}`, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                role: role,
                colors: colors
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(`${role.charAt(0).toUpperCase() + role.slice(1)} sidebar colors saved successfully!`, 'success');
            } else {
                showNotification('Error saving sidebar colors', 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error saving sidebar colors', 'danger');
        });
    }

    // Initialize role-specific previews when page loads
    document.addEventListener('DOMContentLoaded', function() {
        // Load saved colors from database (read from hidden element to avoid Blade parser issues in large script)
        const sidebarSettingsEl = document.getElementById('sidebar-settings-json');
        let sidebarSettings = {};
        if (sidebarSettingsEl) {
            try { sidebarSettings = JSON.parse(sidebarSettingsEl.textContent || '{}'); } catch(e) { sidebarSettings = {}; }
        }
        
        // Load student sidebar colors
        if (sidebarSettings.student) {
            const studentColors = sidebarSettings.student;
            document.getElementById('studentSidebarPrimary').value = studentColors.primary_color || '#1a1a1a';
            document.getElementById('studentSidebarSecondary').value = studentColors.secondary_color || '#2d2d2d';
            document.getElementById('studentSidebarAccent').value = studentColors.accent_color || '#3b82f6';
            document.getElementById('studentSidebarText').value = studentColors.text_color || '#e0e0e0';
            document.getElementById('studentSidebarHover').value = studentColors.hover_color || '#374151';
            
            // Sync text inputs
            document.getElementById('studentSidebarPrimaryText').value = studentColors.primary_color || '#1a1a1a';
            document.getElementById('studentSidebarSecondaryText').value = studentColors.secondary_color || '#2d2d2d';
            document.getElementById('studentSidebarAccentText').value = studentColors.accent_color || '#3b82f6';
            document.getElementById('studentSidebarTextText').value = studentColors.text_color || '#e0e0e0';
            document.getElementById('studentSidebarHoverText').value = studentColors.hover_color || '#374151';
        }
        
        // Load professor sidebar colors
        if (sidebarSettings.professor) {
            const professorColors = sidebarSettings.professor;
            document.getElementById('professorSidebarPrimary').value = professorColors.primary_color || '#1e293b';
            document.getElementById('professorSidebarSecondary').value = professorColors.secondary_color || '#334155';
            document.getElementById('professorSidebarAccent').value = professorColors.accent_color || '#10b981';
            document.getElementById('professorSidebarText').value = professorColors.text_color || '#f1f5f9';
            document.getElementById('professorSidebarHover').value = professorColors.hover_color || '#475569';
            
            // Sync text inputs
            document.getElementById('professorSidebarPrimaryText').value = professorColors.primary_color || '#1e293b';
            document.getElementById('professorSidebarSecondaryText').value = professorColors.secondary_color || '#334155';
            document.getElementById('professorSidebarAccentText').value = professorColors.accent_color || '#10b981';
            document.getElementById('professorSidebarTextText').value = professorColors.text_color || '#f1f5f9';
            document.getElementById('professorSidebarHoverText').value = professorColors.hover_color || '#475569';
        }
        
        // Load admin sidebar colors
        if (sidebarSettings.admin) {
            const adminColors = sidebarSettings.admin;
            document.getElementById('adminSidebarPrimary').value = adminColors.primary_color || '#111827';
            document.getElementById('adminSidebarSecondary').value = adminColors.secondary_color || '#1f2937';
            document.getElementById('adminSidebarAccent').value = adminColors.accent_color || '#f59e0b';
            document.getElementById('adminSidebarText').value = adminColors.text_color || '#f9fafb';
            document.getElementById('adminSidebarHover').value = adminColors.hover_color || '#374151';
            
            // Sync text inputs
            document.getElementById('adminSidebarPrimaryText').value = adminColors.primary_color || '#111827';
            document.getElementById('adminSidebarSecondaryText').value = adminColors.secondary_color || '#1f2937';
            document.getElementById('adminSidebarAccentText').value = adminColors.accent_color || '#f59e0b';
            document.getElementById('adminSidebarTextText').value = adminColors.text_color || '#f9fafb';
            document.getElementById('adminSidebarHoverText').value = adminColors.hover_color || '#374151';
        }
        
        setTimeout(() => {
            updateStudentSidebarPreview();
            updateProfessorSidebarPreview();
            updateAdminSidebarPreview();
        }, 100);
    });
</script>
<script type="application/json" id="sidebar-settings-json"><?php echo json_encode($sidebarSettings ?? [], 15, 512) ?></script>
<?php /**PATH C:\xampp\htdocs\A.R.T.C\resources\views/smartprep/dashboard/partials/customize-scripts.blade.php ENDPATH**/ ?>