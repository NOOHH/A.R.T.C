<?php
/**
 * Quick Manual Debug Test
 * This creates a simple test file to check the JavaScript behavior
 */

echo "\n🐛 MANUAL DEBUG TEST\n";
echo "===================\n\n";

echo "Creating a test HTML file to debug the advanced settings issue...\n\n";

$testHtml = '<!DOCTYPE html>
<html>
<head>
    <title>Advanced Settings Debug</title>
    <style>
        .settings-nav-tab { padding: 10px; margin: 5px; background: #f0f0f0; cursor: pointer; }
        .settings-nav-tab.active { background: #007bff; color: white; }
        .sidebar-section { border: 1px solid #ccc; padding: 20px; margin: 10px; }
        .sidebar-section.active { border-color: #007bff; }
    </style>
</head>
<body>
    <h1>Advanced Settings Navigation Test</h1>
    
    <div class="settings-nav-tabs">
        <button class="settings-nav-tab active" data-section="general">General</button>
        <button class="settings-nav-tab" data-section="branding">Branding</button>
        <button class="settings-nav-tab" data-section="navbar">Navigation</button>
        <button class="settings-nav-tab" data-section="permissions">Permissions</button>
        <button class="settings-nav-tab" data-section="advanced">Advanced</button>
    </div>
    
    <div class="settings-sidebar">
        <div class="sidebar-section active" id="general-settings">
            <h3>General Settings</h3>
            <p>This is the general settings section.</p>
        </div>
        
        <div class="sidebar-section" id="branding-settings" style="display: none;">
            <h3>Branding Settings</h3>
            <p>This is the branding settings section.</p>
        </div>
        
        <div class="sidebar-section" id="navbar-settings" style="display: none;">
            <h3>Navigation Settings</h3>
            <p>This is the navigation settings section.</p>
        </div>
        
        <div class="sidebar-section" id="permissions-settings" style="display: none;">
            <h3>Permissions Settings</h3>
            <p>This is the permissions settings section.</p>
            <div class="card">
                <h4>Permission Management</h4>
                <button onclick="showSection(\'director-features\')">Configure Director Features</button>
                <button onclick="showSection(\'professor-features\')">Configure Professor Features</button>
            </div>
        </div>
        
        <div class="sidebar-section" id="advanced-settings" style="display: none;">
            <h3>Advanced Settings</h3>
            <p>This is the advanced settings section.</p>
            <form>
                <label>Custom CSS:</label><br>
                <textarea rows="5" cols="50"></textarea><br><br>
                <label>Custom JavaScript:</label><br>
                <textarea rows="5" cols="50"></textarea><br><br>
                <button type="submit">Update Advanced Settings</button>
            </form>
        </div>
        
        <!-- Sub-sections for permissions -->
        <div class="sidebar-section" id="director-features" style="display: none;">
            <h3>Director Features</h3>
            <p>Configure director access and features.</p>
            <button onclick="showSection(\'permissions-settings\')">Back to Permissions</button>
        </div>
        
        <div class="sidebar-section" id="professor-features" style="display: none;">
            <h3>Professor Features</h3>
            <p>Configure professor access and features.</p>
            <button onclick="showSection(\'permissions-settings\')">Back to Permissions</button>
        </div>
    </div>
    
    <script>
        // Settings tab navigation
        document.addEventListener("DOMContentLoaded", function() {
            console.log("DOM loaded, initializing navigation...");
            
            const navTabs = document.querySelectorAll(".settings-nav-tab");
            const sidebarSections = document.querySelectorAll(".sidebar-section");
            
            console.log("Found tabs:", navTabs.length);
            console.log("Found sections:", sidebarSections.length);
            
            navTabs.forEach(tab => {
                tab.addEventListener("click", function() {
                    const section = this.getAttribute("data-section");
                    console.log("Tab clicked:", section);
                    
                    // Update active tab
                    navTabs.forEach(t => t.classList.remove("active"));
                    this.classList.add("active");
                    
                    // Update active section
                    sidebarSections.forEach(s => {
                        s.classList.remove("active");
                        s.style.display = "none";
                    });
                    
                    const sectionElement = document.getElementById(section + "-settings");
                    console.log("Looking for section:", section + "-settings");
                    console.log("Section found:", sectionElement);
                    
                    if (sectionElement) {
                        sectionElement.style.display = "block";
                        sectionElement.classList.add("active");
                        console.log("Section displayed:", section);
                    } else {
                        console.error("Section not found:", section + "-settings");
                    }
                });
            });
        });
        
        // Function for sub-section navigation
        function showSection(sectionId) {
            console.log("showSection called with:", sectionId);
            
            const sidebarSections = document.querySelectorAll(".sidebar-section");
            
            // Hide all sections
            sidebarSections.forEach(s => {
                s.classList.remove("active");
                s.style.display = "none";
            });
            
            // Show the requested section
            const sectionElement = document.getElementById(sectionId);
            if (sectionElement) {
                sectionElement.style.display = "block";
                sectionElement.classList.add("active");
                console.log("Sub-section displayed:", sectionId);
            } else {
                console.error("Sub-section not found:", sectionId);
            }
        }
    </script>
</body>
</html>';

// Save the test file
file_put_contents(__DIR__ . '/debug_advanced_settings.html', $testHtml);

echo "✅ Test file created: debug_advanced_settings.html\n";
echo "✅ You can open this file in a browser to test the navigation\n";
echo "✅ Check the browser console for debug messages\n\n";

echo "📋 Manual Test Steps:\n";
echo "1. Open debug_advanced_settings.html in a browser\n";
echo "2. Click each tab and verify the content changes\n";
echo "3. Click 'Permissions' tab, then click 'Configure Director Features'\n";
echo "4. Click 'Back to Permissions' to return\n";
echo "5. Check browser console for any error messages\n\n";

echo "🔧 If the test file works but the real page doesn't:\n";
echo "1. Check for CSS conflicts in the real page\n";
echo "2. Look for JavaScript errors in browser console\n";
echo "3. Verify CSRF tokens and authentication\n";
echo "4. Check if Laravel is rendering the correct sections\n\n";

echo "Test file location: " . __DIR__ . '/debug_advanced_settings.html' . "\n";
?>
